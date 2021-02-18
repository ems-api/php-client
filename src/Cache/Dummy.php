<?php declare(strict_types=1);

namespace EmsApi\Cache;

/**
 * Class Dummy
 * @package EmsApi\Cache
 */
class Dummy extends CacheAbstract
{
    /**
     * Cache data by given key.
     *
     * This method implements {@link CacheAbstract::set()}.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set(string $key, $value): bool
    {
        return true;
    }
    
    /**
     * Get cached data by given key.
     *
     * This method implements {@link CacheAbstract::get()}.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return null;
    }
    
    /**
     * Delete cached data by given key.
     *
     * This method implements {@link CacheAbstract::delete()}.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return true;
    }
    
    /**
     * Delete all cached data.
     *
     * This method implements {@link CacheAbstract::flush()}.
     *
     * @return bool
     */
    public function flush(): bool
    {
        return true;
    }
}
