<?php

<<<<<<< HEAD
declare(strict_types=1);

class typecho_mysql implements TpCache
{
    private static ?self $_instance = null;
    private ?Typecho_Db $db = null;
    private string $host = '127.0.0.1';
    private int $port = 3306;
    private int $expire = 86400;
    private string $username = '';
    private string $password = '';
    private string $database = '';
    private string $charset = 'utf8mb4';
    private string $prefix = '';

    private function __construct($option = null)
    {
        if ($option) {
            $this->host = (string)$option->host;
            $this->port = (int)$option->port;
            $this->expire = (int)$option->expire;
            $this->username = (string)$option->username;
            $this->password = (string)$option->password;
            $this->database = (string)$option->database;
            $this->charset = (string)$option->charset;
        }
        $this->init($option);
    }

    public static function getInstance($option): self
    {
        if (self::$_instance === null) {
=======
/**
 * MySQL缓存驱动类
 * 
 * 支持MySQL 7/8+版本
 * 
 * @package TpRedis
 * @author 风之翼灵
 * @version 1.0.1
 * @link http://www.fungj.com
 */
class typecho_mysql implements TpCache
{

    private static $_instance = null;
    private $mc = null;
    private $expire = 86400;
    private $db = null;
    private $prefix = 'typecho_';

    private function __construct($option = null)
    {
        // 兼容数组和对象类型的配置
        if (is_array($option)) {
            $this->expire = isset($option['expire']) ? intval($option['expire']) : $this->expire;
            $this->prefix = isset($option['prefix']) ? $option['prefix'] : $this->prefix;
        } else if (is_object($option)) {
            $this->expire = isset($option->expire) ? intval($option->expire) : $this->expire;
            $this->prefix = isset($option->prefix) ? $option->prefix : $this->prefix;
        }
        
        $this->init($option);
    }

    static public function getInstance($option)
    {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
>>>>>>> origin/main
            self::$_instance = new self($option);
        }
        return self::$_instance;
    }

<<<<<<< HEAD
    public function init($option): void
    {
        try {
        $this->db = Typecho_Db::get();
            $this->prefix = $this->db->getPrefix();
            
            // 检查并创建缓存表
            $this->checkAndCreateTable();
            
            // 清理过期缓存
            $this->cleanExpiredCache();
            
        } catch (Exception $e) {
            throw new Exception('MySQL initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * 检查并创建缓存表
     */
    private function checkAndCreateTable(): void
    {
        $tableName = $this->prefix . 'cache';
        $sql = "SHOW TABLES LIKE '{$tableName}'";

        if (count($this->db->fetchAll($sql)) == 0) {
            $createTableSql = "
            CREATE TABLE IF NOT EXISTS `{$tableName}` (
                `key` VARCHAR(255) NOT NULL,
                `value` LONGTEXT,
                `expire` BIGINT UNSIGNED,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`key`),
                INDEX `idx_expire` (`expire`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$this->charset} COLLATE={$this->charset}_unicode_ci;";
            
            try {
                $this->db->query($createTableSql);
            } catch (Exception $e) {
                throw new Exception('Failed to create cache table: ' . $e->getMessage());
            }
        }
    }

    /**
     * 清理过期缓存
     */
    private function cleanExpiredCache(): void
    {
        try {
            $tableName = $this->prefix . 'cache';
            $sql = "DELETE FROM `{$tableName}` WHERE `expire` < ?";
            $this->db->query($sql, time());
        } catch (Exception $e) {
            // 清理失败不影响其他操作，只记录日志
            error_log('Failed to clean expired cache: ' . $e->getMessage());
        }
    }

    public function add(string $key, $value, ?int $expire = null): bool
    {
        try {
            if ($this->exists($key)) {
                return false;
            }
            return $this->set($key, $value, $expire);
        } catch (Exception $e) {
            throw new Exception('MySQL add failed: ' . $e->getMessage());
        }
    }

    public function delete(string $key): bool
    {
        try {
            $tableName = $this->prefix . 'cache';
            $sql = "DELETE FROM `{$tableName}` WHERE `key` = ?";
            return (bool)$this->db->query($sql, $key);
        } catch (Exception $e) {
            throw new Exception('MySQL delete failed: ' . $e->getMessage());
        }
    }

    public function set(string $key, $value, ?int $expire = null): bool
    {
        try {
            $tableName = $this->prefix . 'cache';
            $expire = $expire ?? $this->expire;
            $expireTime = $expire > 0 ? time() + $expire : 0;
            
            // 使用REPLACE INTO来处理插入或更新
            $sql = "REPLACE INTO `{$tableName}` (`key`, `value`, `expire`) VALUES (?, ?, ?)";
            $serializedValue = serialize($value);
            
            return (bool)$this->db->query($sql, $key, $serializedValue, $expireTime);
        } catch (Exception $e) {
            throw new Exception('MySQL set failed: ' . $e->getMessage());
        }
    }

    public function get(string $key)
    {
        try {
            $tableName = $this->prefix . 'cache';
            $sql = "SELECT `value` FROM `{$tableName}` WHERE `key` = ? AND (`expire` = 0 OR `expire` > ?)";
            $result = $this->db->fetchRow($sql, $key, time());
            
            if ($result === false) {
                return null;
            }
            
            return unserialize($result['value']);
        } catch (Exception $e) {
            throw new Exception('MySQL get failed: ' . $e->getMessage());
        }
    }

    public function exists(string $key): bool
    {
        try {
            $tableName = $this->prefix . 'cache';
            $sql = "SELECT COUNT(*) as count FROM `{$tableName}` WHERE `key` = ? AND (`expire` = 0 OR `expire` > ?)";
            $result = $this->db->fetchRow($sql, $key, time());
            return (bool)$result['count'];
        } catch (Exception $e) {
            throw new Exception('MySQL exists check failed: ' . $e->getMessage());
        }
    }

    public function flush(): bool
    {
        try {
            $tableName = $this->prefix . 'cache';
            $sql = "TRUNCATE TABLE `{$tableName}`";
            return (bool)$this->db->query($sql);
        } catch (Exception $e) {
            throw new Exception('MySQL flush failed: ' . $e->getMessage());
=======
    public function init($option)
    {
        try {
            $this->db = Typecho_Db::get();
            $prefix = $this->db->getPrefix();
            $table_name = $prefix . 'cache';
            $sql_detect = "SHOW TABLES LIKE '" . $table_name . "'";

            if (count($this->db->fetchAll($sql_detect)) == 0) {
                $this->install_db();
            } else {
                // 用访问触发缓存过期
                $this->db->query($this->db->delete('table.cache')->where('time <= ?', (time() - $this->expire)));
            }
        } catch (Exception $e) {
            echo '<div class="message error">' . $e->getMessage() . '</div>';
        }
    }

    public function install_db()
    {
        try {
            $install_sql = '
DROP TABLE IF EXISTS `%prefix%cache`;
CREATE TABLE `%prefix%cache` (
  `key` char(64) NOT NULL,
  `data` longtext,
  `time` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `time_index` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=%charset%';

            $prefix = $this->db->getPrefix();
            $search = array('%prefix%', '%charset%');
            $replace = array($prefix, str_replace('UTF-8', 'utf8mb4', Helper::options()->charset));
            $sql = str_replace($search, $replace, $install_sql);
            $sqls = explode(';', $sql);

            foreach ($sqls as $sql) {
                $sql = trim($sql);
                if (empty($sql)) continue;
                
                $this->db->query($sql);
            }
        } catch (Typecho_Db_Exception $e) {
            echo '<div class="message error">' . $e->getMessage() . '</div>';
        }
    }

    public function add($key, $value, $expire = null)
    {
        try {
            $key = $this->prefix . md5($key);
            $this->db->query($this->db->insert('table.cache')->rows(array(
                'key' => $key,
                'data' => $value,
                'time' => time()
            )));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($key)
    {
        try {
            $key = $this->prefix . md5($key);
            return $this->db->query($this->db->delete('table.cache')->where('key = ?', $key));
        } catch (Exception $e) {
            return false;
        }
    }

    public function set($key, $value, $expire = null)
    {
        try {
            $key = $this->prefix . md5($key);
            $this->delete($key);
            return $this->add($key, $value);
        } catch (Exception $e) {
            return false;
        }
    }

    public function get($key)
    {
        try {
            $key = $this->prefix . md5($key);
            $rs = $this->db->fetchRow($this->db->select('*')->from('table.cache')->where('key = ?', $key));
            if (empty($rs) || !isset($rs['data'])) {
                return false;
            } else {
                return $rs['data'];
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function flush()
    {
        try {
            return $this->db->query($this->db->delete('table.cache'));
        } catch (Exception $e) {
            return false;
>>>>>>> origin/main
        }
    }
}