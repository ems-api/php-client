<?php declare(strict_types=1);

namespace EmsApi\Test;

use EmsApi\Config;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class Base
 */
class Base extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        // configuration object
        try {
            \EmsApi\Base::setConfig(new Config([
                'apiUrl' => getenv('EMS_API_URL') ?: 'https://example.com/api',
                'apiKey' => getenv('EMS_API_KEY') ?: 'test-api-key',
            ]));
        } catch (ReflectionException) {
        }
        
        // start UTC
        date_default_timezone_set('UTC');

        parent::setUp();
    }
}
