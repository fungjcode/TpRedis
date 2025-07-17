<?php

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
            self::$_instance = new self($option);
        }
        return self::$_instance;
    }

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
        }
    }
}