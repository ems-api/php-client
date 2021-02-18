<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class Countries
 * @package EmsApi\Endpoint
 */
class Countries extends Base
{
    /**
     * Get all available countries
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
    public function getCountries(int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('countries'),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage
            ],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Get all available country zones
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param int $countryId
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getZones(int $countryId, int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('countries/%d/zones', $countryId)),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage
            ],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }
}
