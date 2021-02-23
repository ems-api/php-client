<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class Templates
 * @package EmsApi\Endpoint
 */
class Templates extends Base
{
    /**
     * Get all the email templates of the current customer
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
    public function getTemplates(int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('templates'),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage
            ],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Search through all the email templates of the current customer
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param int $page
     * @param int $perPage
     * @param array $filter
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     * @since MailWizz 1.4.4
     */
    public function searchTemplates(int $page = 1, int $perPage = 10, array $filter = []): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('templates'),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage,
                'filter'    => $filter,
            ],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Get one template
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $templateUid
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function getTemplate(string $templateUid): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('templates/%s', $templateUid)),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);
        
        return $client->request();
    }

    /**
     * Create a new template
     *
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function create(array $data): Response
    {
        if (isset($data['content'])) {
            $data['content'] = base64_encode($data['content']);
        }
        
        if (isset($data['archive'])) {
            $data['archive'] = base64_encode($data['archive']);
        }
        
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl('templates'),
            'paramsPost'    => [
                'template'  => $data
            ],
        ]);
        
        return $client->request();
    }

    /**
     * Update existing template for the customer
     *
     * @param string $templateUid
     * @param array $data
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function update(string $templateUid, array $data): Response
    {
        if (isset($data['content'])) {
            $data['content'] = base64_encode($data['content']);
        }
        
        if (isset($data['archive'])) {
            $data['archive'] = base64_encode($data['archive']);
        }
        
        $client = new Client([
            'method'        => Client::METHOD_PUT,
            'url'           => $this->getConfig()->getApiUrl(sprintf('templates/%s', $templateUid)),
            'paramsPut'     => [
                'template'  => $data
            ],
        ]);
        
        return $client->request();
    }

    /**
     * Delete existing template for the customer
     *
     * @param string $templateUid
     *
     * @return Response
     * @throws ReflectionException
     */
    public function delete(string $templateUid): Response
    {
        $client = new Client([
            'method'    => Client::METHOD_DELETE,
            'url'       => $this->getConfig()->getApiUrl(sprintf('templates/%s', $templateUid)),
        ]);
        
        return $client->request();
    }
}
