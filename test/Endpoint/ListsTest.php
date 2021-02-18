<?php declare(strict_types=1);

namespace EmsApi\Test\Endpoint;

use EmsApi\Endpoint\Lists;
use EmsApi\Http\Response;
use EmsApi\Test\Base;

class ListsTest extends Base
{
    /**
     * @var string
     */
    public static $listUID;

    /**
     * @return void
     * @throws \ReflectionException
     */
    final public function testGetLists()
    {
        $endpoint = new Lists();

        $response = $endpoint->getLists();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertIsArray($response->body->itemAt('data'));
        $this->assertArrayHasKey('records', $response->body->itemAt('data'));
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    final public function testCreateList()
    {
        $endpoint = new Lists();

        $response = $endpoint->create([
            // required
            'general' => [
                'name'          => 'My list created from the API for tests',
                'description'   => 'My list created from the API for tests',
            ],
            // required
            'defaults' => [
                'from_name' => 'John Doe',
                'from_email'=> 'johndoe@doe.com',
                'reply_to'  => 'johndoe@doe.com',
                'subject'   => 'Hello!',
            ],
            'company' => [
                'name'      => 'John Doe INC',
                'country'   => 'United States',
                'zone'      => 'New York',
                'address_1' => 'Some street address',
                'address_2' => '',
                'zone_name' => '',
                'city'      => 'New York City',
                'zip_code'  => '10019',
            ],
        ]);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->getIsSuccess());
        $this->assertArrayHasKey('list_uid', $response->body->toArray());
        
        self::$listUID = $response->body->itemAt('list_uid');
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    final public function testGetList()
    {
        $endpoint = new Lists();
        
        $response = $endpoint->getList(self::$listUID);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->getIsSuccess());
        $this->assertIsArray($response->body->itemAt('data'));

        $general = isset($response->body->itemAt('data')['record']['general']) ? $response->body->itemAt('data')['record']['general'] : [];
        $this->assertArrayHasKey('list_uid', $general);
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testDeleteList()
    {
        $endpoint = new Lists();
        
        $response = $endpoint->delete(self::$listUID);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->getIsSuccess());
    }
}
