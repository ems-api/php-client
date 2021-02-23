<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use ReflectionException;
use Exception;

/**
 * Class Campaigns
 * @package EmsApi\Endpoint
 */
class Campaigns extends Base
{
    /**
     * Get all the campaigns of the current customer
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
    public function getCampaigns(int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('campaigns'),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage
            ],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Get one campaign
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $campaignUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getCampaign(string $campaignUid): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('campaigns/%s', (string)$campaignUid)),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Create a new campaign
     *
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function create(array $data): Response
    {
        if (isset($data['template']['content'])) {
            $data['template']['content'] = base64_encode($data['template']['content']);
        }
        
        if (isset($data['template']['archive'])) {
            $data['template']['archive'] = base64_encode($data['template']['archive']);
        }
        
        if (isset($data['template']['plain_text'])) {
            $data['template']['plain_text'] = base64_encode($data['template']['plain_text']);
        }
        
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl('campaigns'),
            'paramsPost'    => [
                'campaign'  => $data
            ],
        ]);
        
        return $client->request();
    }

    /**
     * Update existing campaign for the customer
     *
     * @param string $campaignUid
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function update(string $campaignUid, array $data): Response
    {
        if (isset($data['template']['content'])) {
            $data['template']['content'] = base64_encode($data['template']['content']);
        }
        
        if (isset($data['template']['archive'])) {
            $data['template']['archive'] = base64_encode($data['template']['archive']);
        }
        
        if (isset($data['template']['plain_text'])) {
            $data['template']['plain_text'] = base64_encode($data['template']['plain_text']);
        }
        
        $client = new Client([
            'method'        => Client::METHOD_PUT,
            'url'           => $this->getConfig()->getApiUrl(sprintf('campaigns/%s', $campaignUid)),
            'paramsPut'     => [
                'campaign'  => $data
            ],
        ]);
        
        return $client->request();
    }

    /**
     * Copy existing campaign for the customer
     *
     * @param string $campaignUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function copy(string $campaignUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_POST,
            'url'       => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/copy', $campaignUid)),
        ]);
        
        return $client->request();
    }

    /**
     * Pause/Unpause existing campaign
     *
     * @param string $campaignUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function pauseUnpause(string $campaignUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_PUT,
            'url'       => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/pause-unpause', $campaignUid)),
        ]);
        
        return $client->request();
    }

    /**
     * Mark existing campaign as sent
     *
     * @param string $campaignUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function markSent(string $campaignUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_PUT,
            'url'       => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/mark-sent', $campaignUid)),
        ]);
        
        return $client->request();
    }

    /**
     * Delete existing campaign for the customer
     *
     * @param string $campaignUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function delete(string $campaignUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_DELETE,
            'url'       => $this->getConfig()->getApiUrl(sprintf('campaigns/%s', $campaignUid)),
        ]);
        
        return $client->request();
    }

    /**
     * Get the stats for an existing campaign for the customer
     *
     * @param string $campaignUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getStats(string $campaignUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_GET,
            'url'       => $this->getConfig()->getApiUrl(sprintf('campaigns/%s/stats', $campaignUid)),
        ]);

        return $client->request();
    }
}
