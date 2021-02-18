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
                'apiUrl' => getenv('EMS_API_URL') ? getenv('EMS_API_URL') : '',
                'apiKey' => getenv('EMS_API_KEY') ? getenv('EMS_API_KEY') : '',
            ]));
        } catch (ReflectionException $e) {
        }
        
        // start UTC
        date_default_timezone_set('UTC');

        parent::setUp();
    }
}
