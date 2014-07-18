<?php
/**
 * Cache class that stores content in files.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFileCache.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfDatabaseCache extends sfCache
{
  protected $dbh                = null;
  protected $conn                = null;
  protected $connection         = null;
  protected $component          = null;
  protected $db_table           = null;

 /**
  * Initializes this sfCache instance.
  *
  * Available options:
  *
  * * cache_dir: The directory where to put cache files
  *
  * * see sfCache for options available for all drivers
  *
  * @see sfCache
  */
  public function initialize($options = array())
  {
    // Merge database columns options.
    $options = array_merge(array(
      'db_key_col'      => 'key',
      'db_data_col'     => 'data',
      'db_time_col'     => 'timeout',
      'db_modify_col'   => 'last_modified'
    ), $options);

    parent::initialize($options);

    // Init database connection.
    if (! $this->getOption('connection'))
    {
      throw new sfInitializationException('You must pass a "database" option to initialize a database object.');
    }

    $this->setConnection($this->getOption('connection'));

    // Init table.
    if (! $this->getOption('db_table'))
    {
      throw new sfInitializationException('You must pass a "table_name" option to initialize a database object.');
    }

    $this->setTable($this->getOption('db_table'));
  }

  /**
   * Sets the database name.
   *
   * @param string $database The database name where to store the cache
   */
  protected function setConnection($connection)
  {
    // Save connection.
    $this->connection = $connection;

    //$arConnectionOptions = Doctrine_Manager::getInstance()->getConnection($this->connection)->getOptions();

    // Create database connection.
    $this->conn = Doctrine_Manager::getInstance()->getConnection($this->connection);
    $this->dbh = $this->conn->getDbh();
    //if (! ($this->conn instanceof Doctrine_Connection))
    //{
    //  throw new sfCacheException(sprintf('Unable to connect for connection: %s.', $this->connection));
    //}
  }

  /**
   * Sets the database name.
   *
   * @param string $database The database name where to store the cache
   */
  protected function setTable($db_table)
  {
    $this->db_table = $db_table;

    /*
    // Create database connaction.
    $this->dbh = Doctrine_Manager::getInstance()->getConnection($this->database);

    if (! ($this->dbh instanceof sfDatabase))
    {
      throw new sfCacheException(sprintf('Unable to connect to database: %s.', $this->database));
    }
    */
  }

  /**
   * @see sfCache
   */
  public function get($key, $default = null)
  {
    // Select data from table.
    $query = sprintf("SELECT %s FROM %s WHERE %s = :key", $this->conn->quoteIdentifier($this->options['db_data_col']), $this->conn->quoteIdentifier($this->options['db_table']), $this->conn->quoteIdentifier($this->options['db_key_col']));
    $stmt = $this->dbh->prepare($query);
    $stmt->execute(array('key' => $key));
    $result = $stmt->fetch(Doctrine_Core::FETCH_NUM);

    return ((false != $result && is_array($result) && count($result)) ? $result[0] : $default);
  }

  /**
   * @see sfCache
   */
  public function has($key)
  {
    // Select data from table.
    $query = sprintf("SELECT %s FROM %s WHERE %s = :key", $this->conn->quoteIdentifier($this->options['db_data_col']), $this->conn->quoteIdentifier($this->options['db_table']), $this->conn->quoteIdentifier($this->options['db_key_col']));
    $stmt = $this->dbh->prepare($query);
    $stmt->execute(array('key' => $key));

    return (false != $stmt->fetch(Doctrine_Core::FETCH_NUM));
  }

  /**
   * @see sfCache
   */
  public function set($key, $data, $lifetime = null)
  {
    if ($this->getOption('automatic_cleaning_factor') > 0 && rand(1, $this->getOption('automatic_cleaning_factor')) == 1)
    {
      $this->clean(sfCache::OLD);
    }

    // Select data from table.
    $query = sprintf("SELECT %s FROM %s WHERE %s = :key", $this->conn->quoteIdentifier($this->options['db_data_col']), $this->conn->quoteIdentifier($this->options['db_table']), $this->conn->quoteIdentifier($this->options['db_key_col']));
    $stmt = $this->dbh->prepare($query);
    $stmt->execute(array('key' => $key));
    $result = $stmt->fetch(Doctrine_Core::FETCH_NUM);

    if (false != $result && is_array($result) && count($result))
    {
      // Формирование запроса.
      $query = sprintf("UPDATE %s SET %s = :data, %s = :timeout, %s = :last_modify WHERE %s = :key", 
        $this->conn->quoteIdentifier($this->options['db_table']),
        $this->conn->quoteIdentifier($this->options['db_data_col']),        
        $this->conn->quoteIdentifier($this->options['db_time_col']),
        $this->conn->quoteIdentifier($this->options['db_modify_col']),
        $this->conn->quoteIdentifier($this->options['db_key_col']));

      $stmt = $this->dbh->prepare($query);
      return $stmt->execute(array('key' => $key, 'data' => $data, 'timeout' => date('Y-m-d H:i:s', (time() + $this->getLifetime($lifetime))), 'last_modify' => date('Y-m-d H:i:s', time())));
    }
    else
    {
      // Формирование запроса.
      $query = sprintf("INSERT INTO %s (%s, %s, %s, %s) VALUES(:key, :data, :timeout, :last_modify)", 
        $this->conn->quoteIdentifier($this->options['db_table']),
        $this->conn->quoteIdentifier($this->options['db_key_col']),
        $this->conn->quoteIdentifier($this->options['db_data_col']),
        $this->conn->quoteIdentifier($this->options['db_time_col']),
        $this->conn->quoteIdentifier($this->options['db_modify_col']));

      $stmt = $this->dbh->prepare($query);
      return $stmt->execute(array('key' => $key, 'data' => $data, 'timeout' => date('Y-m-d H:i:s', (time() + $this->getLifetime($lifetime))), 'last_modify' => date('Y-m-d H:i:s', time())));
    }
  }

  /**
   * @see sfCache
   */
  public function remove($key)
  {
    // Select data from table.
    $query = sprintf("DELETE FROM %s WHERE %s = :key", $this->conn->quoteIdentifier($this->options['db_table']), $this->conn->quoteIdentifier($this->options['db_key_col']));
    $stmt = $this->dbh->prepare($query);
    $stmt->execute(array('key' => $key));
    
    return (boolean) $stmt->rowCount();
  }

  /**
   * @see sfCache
   */
  public function removePattern($pattern)
  {
    die(__METHOD__);
    if (false !== strpos($pattern, '**'))
    {
      $pattern = str_replace(sfCache::SEPARATOR, DIRECTORY_SEPARATOR, $pattern).self::EXTENSION;

      $regexp = self::patternToRegexp($pattern);
      $paths = array();
      foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->getOption('cache_dir'))) as $path)
      {
        if (preg_match($regexp, str_replace($this->getOption('cache_dir').DIRECTORY_SEPARATOR, '', $path)))
        {
          $paths[] = $path;
        }
      }
    }
    else
    {
      $paths = glob($this->getOption('cache_dir').DIRECTORY_SEPARATOR.str_replace(sfCache::SEPARATOR, DIRECTORY_SEPARATOR, $pattern).self::EXTENSION);
    }

    foreach ($paths as $path)
    {
      if (is_dir($path))
      {
        sfToolkit::clearDirectory($path);
      }
      else
      {
        @unlink($path);
      }
    }
  }

  /**
   * @see sfCache
   */
  public function clean($mode = sfCache::ALL)
  {
    die(__METHOD__);
    if (!is_dir($this->getOption('cache_dir')))
    {
      return true;
    }

    $result = true;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->getOption('cache_dir'))) as $file)
    {
      if (sfCache::ALL == $mode || !$this->isValid($file))
      {
        $result = @unlink($file) && $result;
      }
    }

    return $result;
  }

  /**
   * @see sfCache
   */
  public function getTimeout($key)
  {
    die(__METHOD__);
    $path = $this->getFilePath($key);

    if (!file_exists($path))
    {
      return 0;
    }

    $data = $this->read($path, self::READ_TIMEOUT);

    return $data[self::READ_TIMEOUT] < time() ? 0 : $data[self::READ_TIMEOUT];
  }

  /**
   * @see sfCache
   */
  public function getLastModified($key)
  {
    // Select data from table.
    $query = sprintf("SELECT %s FROM %s WHERE %s = :key ORDER BY %s DESC LIMIT 1", 
      $this->conn->quoteIdentifier($this->options['db_data_col']),
      $this->conn->quoteIdentifier($this->options['db_table']), 
      $this->conn->quoteIdentifier($this->options['db_key_col']), 
      $this->conn->quoteIdentifier($this->options['db_modify_col']));

    $stmt = $this->dbh->prepare($query);
    $stmt->execute(array('key' => $key));
    $result = $stmt->fetch(Doctrine_Core::FETCH_NUM);

    return ((false != $result && is_array($result) && count($result)) ? $result[0] : $default);
  }

  protected function isValid($path)
  {
    $data = $this->read($path, self::READ_TIMEOUT);
    return time() < $data[self::READ_TIMEOUT];
  }
}
