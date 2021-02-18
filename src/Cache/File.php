<?php declare(strict_types=1);

namespace EmsApi\Cache;

/**
 * Class File
 * @package EmsApi\Cache
 */
class File extends CacheAbstract
{
    /**
     * @var string the path to the directory where the cache files will be stored.
     *
     * Please note, the cache directory needs to be writable by the web server (chmod 0777).
     *
     * Defaults to data/cache under same directory.
     */
    private $_filesPath = '';

    /**
     * Cache data by given key.
     *
     * For consistency, the key will go through sha1() before it is saved.
     *
     * This method implements {@link CacheAbstract::set()}.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     * @throws \Exception
     */
    public function set(string $key, $value): bool
    {
        $value = serialize($value);
        if ($exists = $this->get($key)) {
            if ($value === serialize($exists)) {
                return true;
            }
        }
        $key = sha1($key);
        return (bool)@file_put_contents($this->getFilesPath() . '/' . $key.'.bin', $value);
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
     *
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key)
    {
        $key = sha1($key);
        
        if (isset($this->_loaded[$key])) {
            return $this->_loaded[$key];
        }
        
        if (!is_file($file = $this->getFilesPath() . '/' . $key.'.bin')) {
            return $this->_loaded[$key] = null;
        }
        
        $fileContents = (string)file_get_contents($file);
        return $this->_loaded[$key] = unserialize($fileContents);
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
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(string $key): bool
    {
        $key = sha1($key);
        
        if (isset($this->_loaded[$key])) {
            unset($this->_loaded[$key]);
        }
        
        if (is_file($file = $this->getFilesPath() . '/' . $key.'.bin')) {
            @unlink($file);
            return true;
        }
        
        return false;
    }

    /**
     * Delete all cached data.
     *
     * This method implements {@link CacheAbstract::flush()}.
     *
     * @return bool
     * @throws \Exception
     */
    public function flush(): bool
    {
        $this->_loaded = [];
        return $this->doFlush($this->getFilesPath());
    }
    
    /**
     * Set the cache path.
     *
     * @param string $path the path to the directory that will store the files
     * @return File
     */
    public function setFilesPath(string $path): self
    {
        if (file_exists($path) && is_dir($path)) {
            $this->_filesPath = $path;
        }
        return $this;
    }

    /**
     * Get the cache path.
     *
     * It defaults to "data/cache" under the same directory.
     *
     * Please make sure the given directoy is writable by the webserver(chmod 0777).
     *
     * @return string
     * @throws \Exception
     */
    public function getFilesPath(): string
    {
        if (empty($this->_filesPath)) {
            throw new \Exception('Please set the file path for cache!');
        }
        return $this->_filesPath;
    }
    
    /**
     * Helper method to clear the cache directory contents
     *
     * @param string $path
     * @return bool
     */
    protected function doFlush(string $path): bool
    {
        if (!file_exists($path) || !is_dir($path)) {
            return false;
        }
        
        if (($handle = opendir($path)) === false) {
            return false;
        }
        
        while (($file = readdir($handle)) !== false) {
            if ($file[0] === '.') {
                continue;
            }
            
            $fullPath=$path.DIRECTORY_SEPARATOR.$file;
            
            if (is_dir($fullPath)) {
                $this->doFlush($fullPath);
            } else {
                @unlink($fullPath);
            }
        }
        
        closedir($handle);
        return true;
    }
}
