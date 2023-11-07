<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class ListSegments
 * @package EmsApi\Endpoint
 */
class ListSegments extends Base
{
    /**
     * Get segments from a certain mail list
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $listUid
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getSegments(string $listUid, int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/segments', $listUid)),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage
            ],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Get one list segment
     *
     *  Note, the results returned by this endpoint can be cached.
     *
     * @param string $listUid
     * @param string $segmentUid
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getSegment(string $listUid, string $segmentUid): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/segments/%s', (string)$listUid, (string)$segmentUid)),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Create a new segment in the given list
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
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/segments', (string)$listUid)),
            'paramsPost'    => $data,
        ]);

        return $client->request();
    }

    /**
     * Update existing segment for the list
     *
     * @param string $listUid
     * @param string $segmentUid
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function update(string $listUid, string $segmentUid, array $data): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_PUT,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/segments/%s', (string)$listUid, (string)$segmentUid)),
            'paramsPut'     => $data,
        ]);

        return $client->request();
    }

    /**
     * Delete existing segment for the list
     *
     * @param string $listUid
     * @param string $segmentUid
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function delete(string $listUid, string $segmentUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_DELETE,
            'url'       => $this->getConfig()->getApiUrl(sprintf('lists/%s/segments/%s', (string)$listUid, (string)$segmentUid)),
        ]);

        return $client->request();
    }

    /**
     * Get the list segments condition operators
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getConditionOperators(): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('lists/segments/condition-operators'),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);

        return $client->request();
    }
}
