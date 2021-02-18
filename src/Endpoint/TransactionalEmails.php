<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;

/**
 * Class TransactionalEmails
 * @package EmsApi\Endpoint
 */
class TransactionalEmails extends Base
{
    /**
     * Get all transactional emails of the current customer
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws Exception
     */
    public function getEmails(int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('transactional-emails'),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage
            ],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Get one transactional email
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $emailUid
     *
     * @return Response
     * @throws Exception
     */
    public function getEmail(string $emailUid): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('transactional-emails/%s', $emailUid)),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Create a new transactional email
     *
     * @param array $data
     *
     * @return Response
     * @throws Exception
     */
    public function create(array $data): Response
    {
        if (!empty($data['body'])) {
            $data['body'] = base64_encode($data['body']);
        }
        
        if (!empty($data['plain_text'])) {
            $data['plain_text'] = base64_encode($data['plain_text']);
        }
        
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl('transactional-emails'),
            'paramsPost'    => [
                'email'  => $data
            ],
        ]);
        
        return $client->request();
    }

    /**
     * Delete existing transactional email
     *
     * @param string $emailUid
     *
     * @return Response
     * @throws Exception
     */
    public function delete(string $emailUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_DELETE,
            'url'       => $this->getConfig()->getApiUrl(sprintf('transactional-emails/%s', $emailUid)),
        ]);
        
        return $client->request();
    }
}
