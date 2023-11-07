<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class CampaignsTracking
 * @package EmsApi\Endpoint
 */
class CampaignsTracking extends Base
{
    /**
     * Track campaign url click for certain subscriber
     *
     * @param string $campaignUid
     * @param string $subscriberUid
     * @param string $hash
     * @param string $trackClickUrlSegment
     *
     * @return Response
     * @throws ReflectionException
     */
    public function trackUrl(string $campaignUid, string $subscriberUid, string $hash, string $trackClickUrlSegment = 'track-url'): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_GET,
            'url'       => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/%s/%s/%s', $campaignUid, $trackClickUrlSegment, $subscriberUid, (string)$hash)),
            'paramsGet' => [],
        ]);
        
        return $client->request();
    }

    /**
     * Track campaign open for certain subscriber
     *
     * @param string $campaignUid
     * @param string $subscriberUid
     * @param string $trackOpenUrlSegment
     *
     * @return Response
     * @throws ReflectionException
     */
    public function trackOpening(string $campaignUid, string $subscriberUid, string $trackOpenUrlSegment = 'track-opening'): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_GET,
            'url'       => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/%s/%s', $campaignUid, $trackOpenUrlSegment, $subscriberUid)),
            'paramsGet' => [],
        ]);

        return $client->request();
    }

    /**
     * Track campaign unsubscribe for certain subscriber
     *
     * @param string $campaignUid
     * @param string $subscriberUid
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function trackUnsubscribe(string $campaignUid, string $subscriberUid, array $data = []): Response
    {
        $client = new Client([
            'method'     => Client::METHOD_POST,
            'url'        => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/track-unsubscribe/%s', $campaignUid, $subscriberUid)),
            'paramsPost' => $data,
        ]);

        return $client->request();
    }
}
