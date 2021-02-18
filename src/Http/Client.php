<?php declare(strict_types=1);

namespace EmsApi\Http;

use EmsApi\Base;
use EmsApi\Params;
use Exception;
use ReflectionException;

/**
 * Class Client
 * @package EmsApi\Http
 */
class Client extends Base
{
    /**
     * Marker for GET requests.
     */
    const METHOD_GET     = 'GET';
    
    /**
     * Marker for POST requests.
     */
    const METHOD_POST    = 'POST';
    
    /**
     * Marker for PUT requests.
     */
    const METHOD_PUT     = 'PUT';
    
    /**
     * Marker for DELETE requests.
     */
    const METHOD_DELETE = 'DELETE';
    
    /**
     * Marker for the client version.
     */
    const CLIENT_VERSION = '1.0';

    /**
     * @var Params the GET params sent in the request.
     */
    public $paramsGet;
    
    /**
     * @var Params the POST params sent in the request.
     */
    public $paramsPost;
    
    /**
     * @var Params the PUT params sent in the request.
     */
    public $paramsPut;
    
    /**
     * @var Params the DELETE params sent in the request.
     */
    public $paramsDelete;
    
    /**
     * @var Params the headers sent in the request.
     */
    public $headers;

    /**
     * @var string the url where the remote calls will be made.
     */
    public $url = '';

    /**
     * @var int the default timeout for request.
     */
    public $timeout = 30;
    
    /**
     * @var bool whether to get the response headers.
     */
    public $getResponseHeaders = false;
    
    /**
     * @var bool whether to cache the request response.
     */
    public $enableCache = false;
    
    /**
     * @var string the method used in the request.
     */
    public $method = self::METHOD_GET;

    /**
     * Constructor.
     *
     * @param array $options
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(array $options = [])
    {
        $this->populateFromArray($options);
        
        foreach ([ 'paramsGet', 'paramsPost', 'paramsPut', 'paramsDelete', 'headers' ] as $param) {
            if (!($this->$param instanceof Params)) {
                $this->$param = new Params(!is_array($this->$param) ? [] : $this->$param);
            }
        }
        
        $this->headers->add('X-API-KEY', $this->getConfig()->getApiKey());
    }

    /**
     * Whether the request method is a GET method.
     *
     * @return bool
     */
    public function getIsGetMethod(): bool
    {
        return strtoupper($this->method) === self::METHOD_GET;
    }
    
    /**
     * Whether the request method is a POST method.
     *
     * @return bool
     */
    public function getIsPostMethod(): bool
    {
        return strtoupper($this->method) === self::METHOD_POST;
    }
    
    /**
     * Whether the request method is a PUT method.
     *
     * @return bool
     */
    public function getIsPutMethod(): bool
    {
        return strtoupper($this->method) === self::METHOD_PUT;
    }
    
    /**
     * Whether the request method is a DELETE method.
     *
     * @return bool
     */
    public function getIsDeleteMethod(): bool
    {
        return strtoupper($this->method) === self::METHOD_DELETE;
    }

    /**
     * Makes the request to the remote host.
     *
     * @return Response
     * @throws Exception
     */
    public function request(): Response
    {
        $request = new Request($this);
        return $response = $request->send();
    }
}
