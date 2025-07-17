<?php

<<<<<<< HEAD
declare(strict_types=1);

/**
 * Redis缓存驱动类
 * 
 * @property-read Redis $redis Redis实例
 */
class typecho_redis implements TpCache
{
    private static ?self $_instance = null;
    private ?Redis $redis = null;
    private string $host = '127.0.0.1';
    private int $port = 6379;
    private int $expire = 86400;
    private string $password = '';
    private int $database = 0;

    /**
     * 构造函数
     * @param object|null $option 配置选项
     */
    private function __construct(?object $option = null)
    {
        if ($option) {
            $this->host = (string)($option->host ?? '127.0.0.1');
            $this->port = (int)($option->port ?? 6379);
            $this->expire = (int)($option->expire ?? 86400);
            $this->password = (string)($option->password ?? '');
            $this->database = (int)($option->database ?? 0);
        }
        $this->init($option);
    }

    /**
     * 获取实例
     * @param object $option 配置选项
     * @return self
     * @throws Exception
     */
    public static function getInstance(object $option): self
    {
        if (self::$_instance === null) {
=======
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
>>>>>>> origin/main
            self::$_instance = new self($option);
        }
        return self::$_instance;
    }

<<<<<<< HEAD
    /**
     * 初始化Redis连接
     * @param object|null $option 配置选项
     * @throws Exception
     */
    public function init(?object $option): void
    {
        try {
            if (!extension_loaded('redis')) {
                throw new Exception('Redis扩展未安装');
            }

            $this->redis = new Redis();
            
            // 设置连接超时时间（2秒）
            if (!$this->redis->connect($this->host, $this->port, 2.0)) {
                throw new Exception('Redis连接失败');
            }

            // 如果设置了密码，进行认证
            if (!empty($this->password) && !$this->redis->auth($this->password)) {
                throw new Exception('Redis认证失败');
            }

            // 选择数据库
            if ($this->database > 0 && !$this->redis->select($this->database)) {
                throw new Exception('Redis选择数据库失败');
            }

            // 设置序列化方式
            $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
            
            // 设置前缀，避免键名冲突
            $this->redis->setOption(Redis::OPT_PREFIX, 'tpredis:');
            
            // 设置读写超时
            $this->redis->setOption(Redis::OPT_READ_TIMEOUT, 2.0);
            
        } catch (Exception $e) {
            throw new Exception('Redis初始化失败: ' . $e->getMessage());
        }
    }

    /**
     * 添加缓存
     * @param string $key 键名
     * @param mixed $value 值
     * @param int|null $expire 过期时间
     * @return bool
     * @throws Exception
     */
    public function add(string $key, $value, ?int $expire = null): bool
    {
        try {
            if ($this->exists($key)) {
                return false;
            }
            return $this->set($key, $value, $expire);
        } catch (Exception $e) {
            throw new Exception('Redis添加缓存失败: ' . $e->getMessage());
    }
    }

    /**
     * 删除缓存
     * @param string $key 键名
     * @return bool
     * @throws Exception
     */
    public function delete(string $key): bool
    {
        try {
            return (bool)$this->redis->del($key);
        } catch (Exception $e) {
            throw new Exception('Redis删除缓存失败: ' . $e->getMessage());
    }
    }

    /**
     * 设置缓存
     * @param string $key 键名
     * @param mixed $value 值
     * @param int|null $expire 过期时间
     * @return bool
     * @throws Exception
     */
    public function set(string $key, $value, ?int $expire = null): bool
    {
        try {
            $expire = $expire ?? $this->expire;
            
            // 压缩大值数据
            if (is_string($value) && strlen($value) > 1024) {
                $value = gzcompress($value, 9);
            }
            
            if ($expire > 0) {
                return $this->redis->setex($key, $expire, $value);
            }
            return $this->redis->set($key, $value);
        } catch (Exception $e) {
            throw new Exception('Redis设置缓存失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取缓存
     * @param string $key 键名
     * @return mixed
     * @throws Exception
     */
    public function get(string $key)
    {
        try {
            $value = $this->redis->get($key);
            
            if ($value === false) {
                return null;
            }
            
            // 解压压缩的数据
            if (is_string($value) && substr($value, 0, 2) === "\x1f\x8b") {
                $value = gzuncompress($value);
            }
            
            return $value;
        } catch (Exception $e) {
            throw new Exception('Redis获取缓存失败: ' . $e->getMessage());
    }
    }

    /**
     * 检查缓存是否存在
     * @param string $key 键名
     * @return bool
     * @throws Exception
     */
    public function exists(string $key): bool
    {
        try {
            return (bool)$this->redis->exists($key);
        } catch (Exception $e) {
            throw new Exception('Redis检查缓存失败: ' . $e->getMessage());
        }
    }

    /**
     * 清空所有缓存
     * @return bool
     * @throws Exception
     */
    public function flush(): bool
    {
        try {
        return $this->redis->flushDB();
        } catch (Exception $e) {
            throw new Exception('Redis清空缓存失败: ' . $e->getMessage());
        }
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if ($this->redis !== null) {
            try {
                $this->redis->close();
            } catch (Exception $e) {
                error_log('Redis关闭连接失败: ' . $e->getMessage());
            }
=======
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
>>>>>>> origin/main
        }
    }
}
