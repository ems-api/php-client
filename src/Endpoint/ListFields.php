<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class ListFields
 * @package EmsApi\Endpoint
 */
class ListFields extends Base
{
    /**
     * Get fields from a certain mail list
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $listUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getFields(string $listUid): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/fields', $listUid)),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Get one list field
     *
     *  Note, the results returned by this endpoint can be cached.
     *
     * @param string $listUid
     * @param int $fieldId
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getField(string $listUid, int $fieldId): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/fields/%d', (string)$listUid, $fieldId)),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Create a new field in the given list
     *
     * @param string $listUid
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function create(string $listUid, array $data): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/fields', (string)$listUid)),
            'paramsPost'    => $data,
        ]);

        return $client->request();
    }

    /**
     * Update existing field for the list
     *
     * @param string $listUid
     * @param int $fieldId
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function update(string $listUid, int $fieldId, array $data): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_PUT,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/fields/%d', (string)$listUid, $fieldId)),
            'paramsPut'     => $data,
        ]);

        return $client->request();
    }

    /**
     * Delete existing field for the list
     *
     * @param string $listUid
     * @param int $fieldId
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function delete(string $listUid, int $fieldId): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_DELETE,
            'url'       => $this->getConfig()->getApiUrl(sprintf('lists/%s/fields/%d', (string)$listUid, $fieldId)),
        ]);

        return $client->request();
    }

    /**
     * Get the list fields possible types
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getListFieldTypes(): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('lists/fields/types'),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);

        return $client->request();
    }
}
