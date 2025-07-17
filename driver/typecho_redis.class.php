<?php

/**
 * Redis缓存驱动类
 * 
 * 支持PHP 7.5+和最新的Redis扩展
 * 
 * @package TpRedis
 * @author 风之翼灵
 * @version 1.0.1
 * @link http://www.fungj.com
 */
class typecho_redis implements TpCache {

    private static $_instance = null;
    private $redis = null;
    private $host = '127.0.0.1';
    private $port = 6379;
    private $password = '';
    private $database = 0;
    private $prefix = 'typecho_';
    private $expire = 86400;

    private function __construct($option = null) {
        // 兼容数组和对象类型的配置
        if (is_array($option)) {
            $this->host = isset($option['host']) ? $option['host'] : $this->host;
            $this->port = isset($option['port']) ? $option['port'] : $this->port;
            $this->password = isset($option['password']) ? $option['password'] : $this->password;
            $this->database = isset($option['database']) ? $option['database'] : $this->database;
            $this->prefix = isset($option['prefix']) ? $option['prefix'] : $this->prefix;
            $this->expire = isset($option['expire']) ? intval($option['expire']) : $this->expire;
        } else if (is_object($option)) {
            $this->host = isset($option->host) ? $option->host : $this->host;
            $this->port = isset($option->port) ? $option->port : $this->port;
            $this->password = isset($option->password) ? $option->password : $this->password;
            $this->database = isset($option->database) ? $option->database : $this->database;
            $this->prefix = isset($option->prefix) ? $option->prefix : $this->prefix;
            $this->expire = isset($option->expire) ? intval($option->expire) : $this->expire;
        }
        
        $this->init($option);
    }

    static public function getInstance($option) {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            self::$_instance = new self($option);
        }
        return self::$_instance;
    }

    public function init($option)
    {
        try {
            // 检查Redis扩展是否已安装
            if (!class_exists('Redis')) {
                throw new Exception('PHP Redis扩展未安装，请先安装PHP的Redis扩展');
            }
            
            $this->redis = new Redis();
            
            // 连接Redis服务器
            if (!$this->redis->connect($this->host, $this->port)) {
                throw new Exception('Redis连接失败，请检查Redis服务是否启动和配置是否正确');
            }
            
            // 如果设置了密码，则进行认证
            if (!empty($this->password) && !$this->redis->auth($this->password)) {
                throw new Exception('Redis认证失败，请检查密码是否正确');
            }
            
            // 选择数据库
            if ($this->database !== 0 && !$this->redis->select($this->database)) {
                throw new Exception('Redis选择数据库失败');
            }
            
            // 设置键前缀
            if (!empty($this->prefix)) {
                $this->redis->setOption(Redis::OPT_PREFIX, $this->prefix);
            }
            
            // 设置序列化选项，对值进行自动序列化/反序列化
            $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
            
        } catch (Exception $e) {
            echo '<div class="message error">' . $e->getMessage() . '</div>';
        }
    }

    public function add($key, $value, $expire = null)
    {
        try {
            if (!$this->redis) return false;
            
            $expire = is_null($expire) ? $this->expire : $expire;
            return $this->redis->setex($key, $expire, $value);
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($key)
    {
        try {
            if (!$this->redis) return false;
            return $this->redis->del($key);
        } catch (Exception $e) {
            return false;
        }
    }

    public function set($key, $value, $expire = null)
    {
        try {
            if (!$this->redis) return false;
            
            $expire = is_null($expire) ? $this->expire : $expire;
            
            if ($expire > 0) {
                return $this->redis->setex($key, $expire, $value);
            } else {
                return $this->redis->set($key, $value);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function get($key)
    {
        try {
            if (!$this->redis) return false;
            return $this->redis->get($key);
        } catch (Exception $e) {
            return false;
        }
    }

    public function flush()
    {
        try {
            if (!$this->redis) return false;
            return $this->redis->flushDB();
        } catch (Exception $e) {
            return false;
        }
    }
}
