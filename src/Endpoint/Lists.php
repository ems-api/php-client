<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class Lists
 * @package EmsApi\Endpoint
 */
class Lists extends Base
{
    /**
     * Get all the mail list of the current customer
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getLists(int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('lists'),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage
            ],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Get one list
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $listUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getList(string $listUid): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s', $listUid)),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Create a new mail list for the customer
     *
     * The $data param must contain following indexed arrays:
     * -> general
     * -> defaults
     * -> notifications
     * -> company
     *
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function create(array $data): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl('lists'),
            'paramsPost'    => $data,
        ]);
        
        return $client->request();
    }

    /**
     * Update existing mail list for the customer
     *
     * The $data param must contain following indexed arrays:
     * -> general
     * -> defaults
     * -> notifications
     * -> company
     *
     * @param string $listUid
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function update(string $listUid, array $data): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_PUT,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s', $listUid)),
            'paramsPut'     => $data,
        ]);
        
        return $client->request();
    }

    /**
     * Copy existing mail list for the customer
     *
     * @param string $listUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function copy(string $listUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_POST,
            'url'       => $this->getConfig()->getApiUrl(sprintf('lists/%s/copy', $listUid)),
        ]);
        
        return $client->request();
    }

    /**
     * Delete existing mail list for the customer
     *
     * @param string $listUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function delete(string $listUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_DELETE,
            'url'       => $this->getConfig()->getApiUrl(sprintf('lists/%s', $listUid)),
        ]);
        
        return $client->request();
    }
}
