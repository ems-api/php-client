<?php declare(strict_types=1);

namespace EmsApi;

use ReflectionException;
use Exception;

/**
 * Class Config
 * @package EmsApi
 */
class Config extends Base
{
    /**
     * @var string the preffered charset.
     */
    public $charset = 'utf-8';
    
    /**
     * @var string the API url.
     */
    private $_apiUrl = '';

    /**
     * @var string the api key
     */
    private $_apiKey = '';

    /**
     * Constructor
     *
     * @param array $config the config array that will populate the class properties.
     *
     * @throws ReflectionException
     */
    public function __construct(array $config = [])
    {
        $this->populateFromArray($config);
    }

    /**
     * Setter for the API url.
     *
     * Please note, this url should NOT contain any endpoint,
     * just the base url to the API.
     *
     * Also, a basic url check is done, but you need to make sure the url is valid.
     *
     * @param string $url
     *
     * @return Config
     * @throws Exception
     */
    public function setApiUrl(string $url): self
    {
        if (!parse_url($url, PHP_URL_HOST)) {
            throw new Exception('Please set a valid api base url.');
        }

        $this->_apiUrl = trim($url, '/') . '/';
        return $this;
    }

    /**
     * Getter for the API url.
     *
     * Also, you can use the $endpoint param to point the request to a certain endpoint.
     *
     * @param string $endpoint
     *
     * @return string
     * @throws Exception
     */
    public function getApiUrl(string $endpoint = ''): string
    {
        if ($this->_apiUrl === '') {
            throw new Exception('Please set the api base url.');
        }
        
        return $this->_apiUrl . $endpoint;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setApiKey(string $key): self
    {
        $this->_apiKey = $key;
        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getApiKey(): string
    {
        if ($this->_apiKey === '') {
            throw new Exception('Please set the api key.');
        }

        return $this->_apiKey;
    }
}
