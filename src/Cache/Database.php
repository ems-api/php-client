<?php declare(strict_types=1);

namespace EmsApi\Cache;

use PDO;
use Exception;

/**
 * Class Database
 * @package EmsApi\Cache
 */
class Database extends CacheAbstract
{
    /**
     * @var PDO the PDO instance that should be imported.
     *
     * This is useful when the SDK is integrated with another application and you
     * have access to the database object.
     */
    private $_importConnection;
    
    /**
     * @var string the PDO connection string.
     */
    private $_connectionString = '';
    
    /**
     * @var string the username required to connect to the database.
     */
    private $_username = '';
    
    /**
     * @var string the password required to connect to the database.
     */
    private $_password = '';
    
    /**
     * @var PDO the newly created PDO instance used for all queries.
     */
    private $_connection;
    
    /**
     * @var bool whether to create or not the database table.
     *
     * Set this to true for the first call, then after the database is created,
     * set it back to false.
     */
    private $_createTable = false;
    
    /**
     * @var string the name of the database table.
     */
    private $_tableName = 'mwa_cache';
    
    /**
     * @var string the current driver in use.
     *
     * This is autodetected based on the connection string.
     */
    private $_driver;
    
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
        $value = serialize($value);
        if ($exists = $this->get($key)) {
            if ($value === serialize($exists)) {
                return true;
            }
            $key = sha1($key);
            $con = $this->getConnection();
            $sth = $con->prepare('UPDATE `'.$this->getTableName().'` SET `value` = :v WHERE `key` = :k');
            return $sth->execute([ ':v' => $value, ':k' => $key ]);
        }
        $key = sha1($key);
        $con = $this->getConnection();
        $sth = $con->prepare('INSERT INTO `'.$this->getTableName().'`(`key`, `value`) VALUES(:k, :v)');
        return $sth->execute([ ':k' => $key, ':v' => $value ]);
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
        $key = sha1($key);
        
        if (isset($this->_loaded[$key])) {
            return $this->_loaded[$key];
        }

        $con = $this->getConnection();
        $sth = $con->prepare('SELECT `value` FROM `'.$this->getTableName().'` WHERE `key` = :k LIMIT 1');
        $sth->execute([ ':k' => $key ]);
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        $sth->closeCursor();
        return $this->_loaded[$key] = !empty($row['value']) ? unserialize($row['value']) : null;
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
        $key = sha1($key);
        
        if (isset($this->_loaded[$key])) {
            unset($this->_loaded[$key]);
        }
        
        $con = $this->getConnection();
        $sth = $con->prepare('DELETE FROM `'.$this->getTableName().'` WHERE `key` = :k');
        return $sth->execute([ ':k' => $key ]);
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
        $this->_loaded = [];
        $con = $this->getConnection();
        $sth = $con->prepare('DELETE FROM `'.$this->getTableName().'` WHERE 1');
        return $sth->execute();
    }
    
    /**
     * Import a {@link PDO} connection to be reused instead of creating a new one.
     *
     * This is useful when the sdk is used inside another application that is already connected
     * to the database using {@link PDO} and same driver.
     *
     * In that case importing and reusing the connection makes more sense.
     *
     * @param PDO $connection
     * @return Database
     */
    public function setImportConnection(PDO $connection): self
    {
        $this->_importConnection = $connection;
        return $this;
    }
    
    /**
     * Get the imported connection, if any.
     *
     * @return PDO|null
     */
    public function getImportConnection(): ?PDO
    {
        return $this->_importConnection;
    }
    
    /**
     * Set the database access username.
     *
     * @param string $username
     * @return Database
     */
    public function setUsername(string $username): self
    {
        $this->_username = $username;
        return $this;
    }
    
    /**
     * Get the database access username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->_username;
    }
    
    /**
     * Set the database access password.
     *
     * @param string $password
     * @return Database
     */
    public function setPassword(string $password): self
    {
        $this->_password = $password;
        return $this;
    }
    
    /**
     * Get the database access password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->_password;
    }

    /**
     * Set the name of the database table.
     *
     * @param string $name
     * @return Database
     */
    public function setTableName(string $name): self
    {
        $this->_tableName = htmlspecialchars($name, ENT_QUOTES, 'utf-8');
        return $this;
    }
    
    /**
     * Get the name of the database table name.
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->_tableName;
    }
    
    /**
     * Set whether the database table should be created or not.
     *
     * @param bool $bool
     * @return Database
     */
    public function setCreateTable(bool $bool): self
    {
        $this->_createTable = $bool;
        return $this;
    }
    
    /**
     * Get whether the database table should be created or not.
     *
     * @return bool
     */
    public function getCreateTable(): bool
    {
        return $this->_createTable;
    }
    
    /**
     * Set the database connection string.
     *
     * Please note, this needs to be a valid DSN, see:
     * http://php.net/manual/en/ref.pdo-mysql.connection.php
     *
     * @param string $string
     * @return Database
     */
    public function setConnectionString(string $string): self
    {
        $this->_connectionString = $string;
        return $this;
    }
    
    /**
     * Get the database connection string.
     *
     * @return string
     */
    public function getConnectionString(): string
    {
        return $this->_connectionString;
    }

    /**
     * Create the database table if it doesn't exists.
     *
     * Please make sure you disable the table creation after the table has been created
     * otherwise it will cause unnecessary overhead.
     *
     * @return void
     * @throws Exception
     */
    protected function createTable(): void
    {
        if ($this->_driver === 'sqlite') {
            $sql = '
            BEGIN;
            CREATE TABLE IF NOT EXISTS `'.$this->getTableName().'` (
              `id` INTEGER PRIMARY KEY AUTOINCREMENT,
              `key` CHAR(40) NOT NULL,
              `value` BLOB NOT NULL
            );
            CREATE INDEX `key` ON `'.$this->getTableName().'` (`key`);
            COMMIT;
            ';
        } elseif ($this->_driver === 'mysql') {
            $sql = '
            CREATE TABLE IF NOT EXISTS `'.$this->getTableName().'` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `key` CHAR(40) NOT NULL,
              `value` LONGBLOB NOT NULL,
              PRIMARY KEY (`id`),
              KEY `key` (`key`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            ';
        } else {
            throw new Exception('The "'.$this->_driver.'" driver is not implemented.');
        }

        $this->_connection->exec($sql);
    }

    /**
     * Get the PDO connection.
     *
     * @return PDO
     * @throws Exception
     */
    public function getConnection(): PDO
    {
        if ($this->_connection === null && $this->getImportConnection() instanceof PDO) {
            $this->_connection = $this->getImportConnection();
        }
        
        $connectionParts = explode(':', $this->getConnectionString());
        $this->_driver = (string)array_shift($connectionParts);
        
        if ($this->getCreateTable() && $this->_driver === 'sqlite') {
            $dbPath = (string)array_shift($connectionParts);
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                throw new Exception('The database storage directory: "'.$dir.'" does not exists.');
            }
            if (!is_writable($dir)) {
                throw new Exception('The database storage path: "'.$dir.'" is not writable.');
            }
            if (!is_file($dbPath)) {
                touch($dbPath);
            }
        }
        
        $this->_connection    = new PDO($this->getConnectionString(), $this->getUsername(), $this->getPassword());
        $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if ($this->getCreateTable()) {
            $this->createTable();
        }
        
        return $this->_connection;
    }
    
    /**
     * Close the database connection.
     *
     * This will only work if the connection is not imported.
     *
     * @return Database
     */
    public function closeConnection(): self
    {
        if (!empty($this->_importConnection)) {
            return $this;
        }
        unset($this->_connection);
        return $this;
    }
}
