<?php declare(strict_types=1);

namespace EmsApi\Cache;

use EmsApi\Base;

/**
 * Class CacheAbstract
 * @package EmsApi\Cache
 */
abstract class CacheAbstract extends Base
{
    /**
     * @var array keeps a history of loaded keys for easier and faster reference
     */
    protected $_loaded = [];
    
    /**
     * Set data into the cache
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    abstract public function set(string $key, $value): bool;
    
    /**
     * Get data from the cache
     *
     * @param string $key
     * @return mixed
     */
    abstract public function get(string $key);
    
    /**
     * Delete data from cache
     *
     * @param string $key
     * @return bool
     */
    abstract public function delete(string $key): bool;
    
    /**
     * Delete all data from cache
     *
     * @return bool
     */
    abstract public function flush(): bool;
}
