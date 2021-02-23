<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class CampaignBounces
 * @package EmsApi\Endpoint
 */
class CampaignBounces extends Base
{
    /**
     * Get bounces from a certain campaign
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $campaignUid
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getBounces(string $campaignUid, int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/bounces', $campaignUid)),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage,
            ],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Create a new bounce in the given campaign
     *
     * @param string $campaignUid
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function create(string $campaignUid, array $data): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/bounces', (string)$campaignUid)),
            'paramsPost'    => $data,
        ]);

        return $client->request();
    }
}
