<?php declare(strict_types=1);

namespace EmsApi\Cache;

/**
 * Class Xcache
 * @package EmsApi\Cache
 */
class Xcache extends CacheAbstract
{
    /**
     * Cache data by given key.
     *
     * For consistency, the key will go through sha1() before it is saved.
     *
     * This method implements {@link CacheAbstract::set()}.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set(string $key, $value): bool
    {
        return xcache_set(sha1($key), $value, 0);
    }
    
    /**
     * Get cached data by given key.
     *
     * For consistency, the key will go through sha1()
     * before it will be used to retrieve the cached data.
     *
     * This method implements {@link CacheAbstract::get()}.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return xcache_isset(sha1($key)) ? xcache_get(sha1($key)) : null;
    }
    
    /**
     * Delete cached data by given key.
     *
     * For consistency, the key will go through sha1()
     * before it will be used to delete the cached data.
     *
     * This method implements {@link CacheAbstract::delete()}.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return xcache_unset(sha1($key));
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
        if (!defined('XC_TYPE_VAR')) {
            return false;
        }
        for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++) {
            xcache_clear_cache(XC_TYPE_VAR, $i);
        }
        return true;
    }
}
