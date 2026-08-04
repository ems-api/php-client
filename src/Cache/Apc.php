<?php declare(strict_types=1);

namespace EmsApi\Cache;

/**
 * Class Apc
 * @package EmsApi\Cache
 *
 * APCu-backed cache adapter kept under the historical Apc class name.
 */
class Apc extends CacheAbstract
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
        if (!function_exists('apcu_store')) {
            return false;
        }

        return apcu_store(sha1($key), $value, 0);
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
        if (!function_exists('apcu_fetch')) {
            return null;
        }

        $success = false;
        $value = apcu_fetch(sha1($key), $success);

        return $success ? $value : null;
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
        if (!function_exists('apcu_delete')) {
            return false;
        }

        return apcu_delete(sha1($key));
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
        if (!function_exists('apcu_clear_cache')) {
            return false;
        }

        return apcu_clear_cache();
    }
}
