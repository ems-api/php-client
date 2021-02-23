<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

class ListSubscribers extends Base
{
    /**
     * Get subscribers from a certain mail list
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $listUid
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws Exception
     */
    public function getSubscribers($listUid, int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers', $listUid)),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage,
            ],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Get one subscriber from a certain mail list
     *
     * Note, the results returned by this endpoint can be cached.
     *
     * @param string $listUid
     * @param string $subscriberUid
     * @return Response
     * @throws Exception
     */
    public function getSubscriber($listUid, $subscriberUid)
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers/%s', (string)$listUid, (string)$subscriberUid)),
            'paramsGet'     => [],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Create a new subscriber in the given list
     *
     * @param string $listUid
     * @param array $data
     * @return Response
     * @throws Exception
     */
    public function create($listUid, array $data)
    {
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers', (string)$listUid)),
            'paramsPost'    => $data,
        ]);

        return $client->request();
    }

    /**
     * Create subscribers in bulk in the given list
     * This feature is available since MailWizz 1.8.1
     *
     * @param string $listUid
     * @param array $data
     * @return Response
     * @throws Exception
     */
    public function createBulk($listUid, array $data)
    {
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers/bulk', (string)$listUid)),
            'paramsPost'    => [ 'subscribers' => $data ],
        ]);

        return $client->request();
    }

    /**
     * Update existing subscriber in given list
     *
     * @param string $listUid
     * @param string $subscriberUid
     * @param array $data
     * @return Response
     * @throws Exception
     */
    public function update($listUid, $subscriberUid, array $data)
    {
        $client = new Client([
            'method'        => Client::METHOD_PUT,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers/%s', (string)$listUid, (string)$subscriberUid)),
            'paramsPut'     => $data,
        ]);

        return $client->request();
    }

    /**
     * Update existing subscriber by email address
     *
     * @param string $listUid
     * @param string $emailAddress
     * @param array $data
     * @return Response
     * @throws Exception
     */
    public function updateByEmail($listUid, $emailAddress, array $data): Response
    {
        $response = $this->emailSearch($listUid, $emailAddress);

        // the request failed.
        if ($response->getIsCurlError()) {
            return $response;
        }

        $bodyData = $response->body->itemAt('data');

        // subscriber not found.
        if ($response->getIsError() && $response->getHttpCode() == 404) {
            return $response;
        }

        if (empty($bodyData['subscriber_uid'])) {
            return $response;
        }

        return $this->update($listUid, $bodyData['subscriber_uid'], $data);
    }

    /**
     * Unsubscribe existing subscriber from given list
     *
     * @param string $listUid
     * @param string $subscriberUid
     * @return Response
     * @throws Exception
     */
    public function unsubscribe($listUid, $subscriberUid): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_PUT,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers/%s/unsubscribe', (string)$listUid, (string)$subscriberUid)),
            'paramsPut'     => [],
        ]);

        return $client->request();
    }

    /**
     * Unsubscribe existing subscriber by email address
     *
     * @param string $listUid
     * @param string $emailAddress
     * @return Response
     * @throws Exception
     */
    public function unsubscribeByEmail($listUid, $emailAddress): Response
    {
        $response = $this->emailSearch($listUid, $emailAddress);

        // the request failed.
        if ($response->getIsCurlError()) {
            return $response;
        }

        $bodyData = $response->body->itemAt('data');

        // subscriber not found.
        if ($response->getIsError() && $response->getHttpCode() == 404) {
            return $response;
        }

        if (empty($bodyData['subscriber_uid'])) {
            return $response;
        }

        return $this->unsubscribe($listUid, $bodyData['subscriber_uid']);
    }

    /**
     * Unsubscribe existing subscriber by email address from all lists
     *
     * @param string $emailAddress
     * @return Response
     * @throws Exception
     */
    public function unsubscribeByEmailFromAllLists(string $emailAddress): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_PUT,
            'url'           => $this->getConfig()->getApiUrl('lists/subscribers/unsubscribe-by-email-from-all-lists'),
            'paramsPut'     => [
                'EMAIL' => $emailAddress,
            ],
        ]);

        return $client->request();
    }


    /**
     * Delete existing subscriber in given list
     *
     * @param string $listUid
     * @param string $subscriberUid
     *
     * @return Response
     * @throws Exception
     */
    public function delete(string $listUid, string $subscriberUid): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_DELETE,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers/%s', (string)$listUid, (string)$subscriberUid)),
            'paramsDelete'  => [],
        ]);

        return $client->request();
    }

    /**
     * Delete existing subscriber by email address
     *
     * @param string $listUid
     * @param string $emailAddress
     *
     * @return Response
     * @throws Exception
     */
    public function deleteByEmail(string $listUid, string $emailAddress): Response
    {
        $response = $this->emailSearch($listUid, $emailAddress);
        $bodyData = $response->body->itemAt('data');

        if ($response->getIsError() || empty($bodyData['subscriber_uid'])) {
            return $response;
        }

        return $this->delete($listUid, (string)$bodyData['subscriber_uid']);
    }

    /**
     * Search in a list for given subscriber by email address
     *
     * @param string $listUid
     * @param string $emailAddress
     *
     * @return Response
     * @throws Exception
     */
    public function emailSearch(string $listUid, string $emailAddress): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers/search-by-email', (string)$listUid)),
            'paramsGet'     => [ 'EMAIL' => (string)$emailAddress ],
        ]);

        return $client->request();
    }

    /**
     * Search in a all lists for given subscriber by email address
     * Please note that this is available only for mailwizz >= 1.3.6.2
     *
     * @param string $emailAddress
     *
     * @return Response
     * @throws Exception
     */
    public function emailSearchAllLists(string $emailAddress): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl('lists/subscribers/search-by-email-in-all-lists'),
            'paramsGet'     => [ 'EMAIL' => (string)$emailAddress ],
        ]);

        return $client->request();
    }

    /**
     * Search in a list by custom fields
     *
     * @param string $listUid
     * @param array $fields
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws Exception
     */
    public function searchByCustomFields(string $listUid, array $fields = [], int $page = 1, int $perPage = 10): Response
    {
        $paramsGet = $fields;
        $paramsGet['page']      = $page;
        $paramsGet['per_page']  = $perPage;
        
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers/search-by-custom-fields', (string)$listUid)),
            'paramsGet'     => $paramsGet,
        ]);

        return $client->request();
    }

    /**
     * Search in a list for given subscribers by status
     *
     * @param string $listUid
     * @param string $status
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function searchByStatus(string $listUid, string $status, int $page = 1, int $perPage = 10): Response
    {
        $client = new Client([
            'method'        => Client::METHOD_GET,
            'url'           => $this->getConfig()->getApiUrl(sprintf('lists/%s/subscribers', $listUid)),
            'paramsGet'     => [
                'page'      => $page,
                'per_page'  => $perPage,
                'status'    => $status,
            ],
            'enableCache'   => true,
        ]);

        return $client->request();
    }

    /**
     * Get only the confirmed subscribers
     *
     * @param string $listUid
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws ReflectionException
     */
    public function getConfirmedSubscribers(string $listUid, int $page = 1, int $perPage = 10): Response
    {
        return $this->searchByStatus($listUid, 'confirmed', $page, $perPage);
    }

    /**
     * Get only the unconfirmed subscribers
     *
     * @param string $listUid
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws ReflectionException
     */
    public function getUnconfirmedSubscribers(string $listUid, int $page = 1, int $perPage = 10): Response
    {
        return $this->searchByStatus($listUid, 'unconfirmed', $page, $perPage);
    }

    /**
     * Get only the unsubscribed subscribers
     *
     * @param string $listUid
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     * @throws ReflectionException
     */
    public function getUnsubscribedSubscribers(string $listUid, int $page = 1, int $perPage = 10): Response
    {
        return $this->searchByStatus($listUid, 'unsubscribed', $page, $perPage);
    }

    /**
     * Create or update a subscriber in given list
     *
     * @param string $listUid
     * @param array $data
     * @return Response
     * @throws Exception
     */
    public function createUpdate(string $listUid, array $data): Response
    {
        $emailAddress    = !empty($data['EMAIL']) ? $data['EMAIL'] : null;
        $response        = $this->emailSearch($listUid, $emailAddress);

        // the request failed.
        if ($response->getIsCurlError()) {
            return $response;
        }

        $bodyData = $response->body->itemAt('data');

        // subscriber not found.
        if ($response->getIsError() && $response->getHttpCode() == 404) {
            return $this->create($listUid, $data);
        }

        if (empty($bodyData['subscriber_uid'])) {
            return $response;
        }

        return $this->update($listUid, $bodyData['subscriber_uid'], $data);
    }
}
