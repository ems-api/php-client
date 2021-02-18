<?php declare(strict_types=1);

namespace EmsApi\Http;

use EmsApi\Base;
use EmsApi\Cache\CacheAbstract;
use EmsApi\Params;
use Exception;

/**
 * Class Request
 * @package EmsApi\Http
 */
class Request extends Base
{
    /**
     * @var Client the http client injected.
     */
    public $client;

    /**
     * @var Params the request params.
     */
    public $params;

    /**
     * Constructor.
     *
     * @param Client $client
     *
     * @throws Exception
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->params = new Params([]);
    }

    /**
     * Send the request to the remote url.
     *
     * @return Response
     * @throws Exception
     */
    public function send(): Response
    {
        foreach ($this->getEventHandlers(self::EVENT_BEFORE_SEND_REQUEST) as $callback) {
            call_user_func_array($callback, [ $this ]);
        }

        $client         = $this->client;
        $registry       = $this->getRegistry();
        $isCacheable    = $registry->contains('cache') && $client->getIsGetMethod() && $client->enableCache;
        $requestUrl     = rtrim($client->url, '/'); // no trailing slash

        $getParams = (array)$client->paramsGet->toArray();
        if (!empty($getParams)) {
            ksort($getParams, SORT_STRING);
            $queryString = http_build_query($getParams, '', '&');
            if (!empty($queryString)) {
                $requestUrl .= '?' . $queryString;
            }
        }
        
        $etagCache = '';
        $cacheKey  = '';
        
        /** @var CacheAbstract $cacheComponent */
        $cacheComponent = null;
        
        if ($isCacheable) {
            $client->getResponseHeaders = true;

            $bodyFromCache  = '';
            $etagCache      = '';
            $cacheKey       = $requestUrl;
            
            /** @var CacheAbstract $cacheComponent */
            $cacheComponent = $registry->itemAt('cache');
            
            /** @var array $cache */
            $cache = $cacheComponent->get($cacheKey);

            if (is_array($cache) && isset($cache['headers']) && is_array($cache['headers'])) {
                foreach ($cache['headers'] as $header) {
                    if (preg_match('/etag:(\s+)?(.*)/ix', $header, $matches)) {
                        $etagCache = trim($matches[2]);
                        $client->headers->add('If-None-Match', $etagCache);
                        $bodyFromCache = $cache['body'];
                        break;
                    }
                }
            }
        }
        
        if ($client->getIsPutMethod() || $client->getIsDeleteMethod()) {
            $client->headers->add('X-HTTP-Method-Override', strtoupper($client->method));
        }

        $ch = curl_init($requestUrl);
        if ($ch === false) {
            throw new Exception('Cannot initialize curl!');
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $client->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $client->timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MailWizzApi Client version '. Client::CLIENT_VERSION);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);

        if ($client->getResponseHeaders) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }

        if (!ini_get('safe_mode')) {
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            if (!ini_get('open_basedir')) {
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            }
        }

        if ($client->headers->getCount() > 0) {
            $headers = [];
            foreach ($client->headers as $name => $value) {
                $headers[] = $name . ': ' . $value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        if ($client->getIsPostMethod() || $client->getIsPutMethod() || $client->getIsDeleteMethod()) {
            $params = new Params($client->paramsPost);
            $params->mergeWith($client->paramsPut);
            $params->mergeWith($client->paramsDelete);

            if (!$client->getIsPostMethod()) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($client->method));
            }

            curl_setopt($ch, CURLOPT_POST, $params->getCount());
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params->toArray(), '', '&'));
        }

        $body           = (string)curl_exec($ch);
        $curlCode       = (int)curl_errno($ch);
        $curlMessage    = (string)curl_error($ch);
        $params         = $this->params = new Params((array)curl_getinfo($ch));

        if ($curlCode === 0 && $client->getResponseHeaders) {
            $headersSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = explode("\n", substr($body, 0, $headersSize));
            foreach ($headers as $index => $header) {
                $header = trim($header);
                if (empty($header)) {
                    unset($headers[$index]);
                }
            }
            $body = substr($body, $headersSize);
            $params->add('headers', new Params($headers));
        }

        $decodedBody = [];
        if ($curlCode === 0 && !empty($body)) {
            $decodedBody = json_decode($body, true);
            if (!is_array($decodedBody)) {
                $decodedBody = [];
            }
        }

        // note here
        if ((int)$params->itemAt('http_code') === 304 && $isCacheable && !empty($bodyFromCache)) {
            $decodedBody = $bodyFromCache;
        }
        
        $params->add('curl_code', $curlCode);
        $params->add('curl_message', $curlMessage);
        $params->add('body', new Params($decodedBody));

        $response = new Response($this);
        $body = $response->body;

        if (!$response->getIsSuccess() && $body->itemAt('status') !== 'success' && !$body->contains('error')) {
            $response->body->add('status', 'error');
            $response->body->add('error', $response->getMessage());
        }

        curl_close($ch);

        if ($isCacheable && $response->getIsSuccess() && $body->itemAt('status') == 'success') {
            $etagNew = null;
            foreach ($response->headers as $header) {
                if (preg_match('/etag:(\s+)?(.*)/ix', $header, $matches)) {
                    $etagNew = trim($matches[2]);
                    break;
                }
            }
            if ($etagNew && $etagNew != $etagCache) {
                $cacheComponent->set($cacheKey, [
                    'headers'   => $response->headers->toArray(),
                    'body'      => $response->body->toArray(),
                ]);
            }
        }

        foreach ($this->getEventHandlers(self::EVENT_AFTER_SEND_REQUEST) as $callback) {
            $response = call_user_func_array($callback, [ $this, $response ]);
        }

        return $response;
    }
}
