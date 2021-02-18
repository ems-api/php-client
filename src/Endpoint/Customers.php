<?php declare(strict_types=1);

namespace EmsApi\Endpoint;

use EmsApi\Base;
use EmsApi\Http\Client;
use EmsApi\Http\Response;
use Exception;
use ReflectionException;

/**
 * Class Customers
 * @package EmsApi\Endpoint
 */
class Customers extends Base
{
    /**
     * Create a new mail list for the customer
     *
     * The $data param must contain following indexed arrays:
     * -> customer
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
        if (isset($data['customer']['password'])) {
            $data['customer']['confirm_password'] = $data['customer']['password'];
        }
        
        if (isset($data['customer']['email'])) {
            $data['customer']['confirm_email'] = $data['customer']['email'];
        }
        
        if (empty($data['customer']['timezone'])) {
            $data['customer']['timezone'] = 'UTC';
        }
        
        $client = new Client([
            'method'        => Client::METHOD_POST,
            'url'           => $this->getConfig()->getApiUrl('customers'),
            'paramsPost'    => $data,
        ]);
        
        return $client->request();
    }
}
