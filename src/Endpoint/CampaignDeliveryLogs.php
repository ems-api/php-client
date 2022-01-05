<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class CampaignDeliveryLogs
 * @package EmsApi\Endpoint
 */
class CampaignDeliveryLogs extends Base
{
    /**
     * Get delivery logs from a certain campaign
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
    public function getDeliveryLogs(string $campaignUid, int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/delivery-logs', $campaignUid)),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage,
            ],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Get a delivery log based on its unique email message id
     *
     * @param string $emailMessageId
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getDeliveryLogByEmailMessageId(string $emailMessageId): Response
    {
        $client = new Client([
            'method' => Client::METHOD_GET,
            'url'    => $this->getConfig()->getApiUrl(sprintf('campaigns/delivery-logs/email-message-id/%s', (string)$emailMessageId)),
        ]);

        return $client->request();
    }
}
