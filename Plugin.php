<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Typecho Redis缓存插件
 *
 * @package TpRedis
 * @author 风之翼灵
<<<<<<< HEAD
 * @version 1.1.0
=======
 * @version 1.0.1
>>>>>>> origin/main
 * @link http://www.fungj.com
 */
class TpRedis_Plugin implements Typecho_Plugin_Interface
{
<<<<<<< HEAD
    /** @var string|null */
    public static $key = null;
    /** @var object|null */
    public static $cache = null;
    /** @var string|null */
    public static $html = null;
    /** @var string|null */
    public static $path = null;
    /** @var object|null */
    public static $sys_config = null;
    /** @var object|null */
    public static $plugin_config = null;
    /** @var object|null */
    public static $request = null;

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @static
     * @access public
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        try {
            // 检查Redis扩展是否安装
            if (!extension_loaded('redis')) {
                throw new Typecho_Plugin_Exception('Redis扩展未安装，请先安装Redis扩展后再启用插件。');
            }

            //页面收尾
            Typecho_Plugin::factory('index.php')->begin = array('TpRedis_Plugin', 'C');
            Typecho_Plugin::factory('index.php')->end = array('TpRedis_Plugin', 'S');

            //页面编辑
            Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = array('TpRedis_Plugin', 'post_update');
            Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishPublish = array('TpRedis_Plugin', 'post_update');

            //评论提交
            Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('TpRedis_Plugin', 'comment_update');

            //评论审核批准
            Typecho_Plugin::factory('Widget_Comments_Edit')->approve = array('TpRedis_Plugin', 'comment_approve');

            return '插件安装成功,请设置需要缓存的页面';
        } catch (Typecho_Plugin_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Typecho_Plugin_Exception('插件激活失败：' . $e->getMessage());
        }
=======
    public static $key = null;
    public static $cache = null;
    public static $html = null;
    public static $path = null;
    public static $sys_config = null;
    public static $plugin_config = null;
    public static $request = null;

    public static function activate()
    {
        //页面收尾
        Typecho_Plugin::factory('index.php')->begin = array('TpRedis_Plugin', 'C');
        Typecho_Plugin::factory('index.php')->end = array('TpRedis_Plugin', 'S');

        //页面编辑
        Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = array('TpRedis_Plugin', 'post_update');
        Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishPublish = array('TpRedis_Plugin', 'post_update');

        //评论
        Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('TpRedis_Plugin', 'comment_update');


        return '插件安装成功,请设置需要缓存的页面';
>>>>>>> origin/main
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        try {
<<<<<<< HEAD
            // 获取插件配置
            $options = Helper::options();
            $pluginOptions = $options->plugin('TpRedis');
            
            // 如果配置存在且缓存驱动已初始化，则尝试清理缓存
            if ($pluginOptions && isset($pluginOptions->cache_driver)) {
                // 先删除插件配置，避免后续操作依赖配置
                Helper::removePlugin('TpRedis');
                
                if ($pluginOptions->cache_driver === 'redis') {
                    // 检查Redis扩展
                    if (!extension_loaded('redis')) {
                        return '插件已禁用（Redis扩展未安装）';
                    }
                    
                    try {
                        // 初始化Redis驱动
                        require_once dirname(__FILE__) . '/driver/cache.interface.php';
                        require_once dirname(__FILE__) . '/driver/typecho_redis.class.php';
                        
                        $cache = typecho_redis::getInstance($pluginOptions);
                        if ($cache && method_exists($cache, 'flush')) {
                            $cache->flush();
                        }
                    } catch (Exception $e) {
                        // Redis连接失败，记录日志但继续禁用插件
                        error_log('Redis清理缓存失败: ' . $e->getMessage());
                    }
                } elseif ($pluginOptions->cache_driver === 'mysql') {
                    try {
                        // 初始化MySQL驱动
                        require_once dirname(__FILE__) . '/driver/cache.interface.php';
                        require_once dirname(__FILE__) . '/driver/typecho_mysql.class.php';
                        
                        $cache = typecho_mysql::getInstance($pluginOptions);
                        if ($cache && method_exists($cache, 'flush')) {
                            $cache->flush();
                        }
                    } catch (Exception $e) {
                        // MySQL清理失败，记录日志但继续禁用插件
                        error_log('MySQL清理缓存失败: ' . $e->getMessage());
                    }
                }
                
                return '插件已禁用，缓存数据已清理';
            }
            
            // 如果没有配置，直接删除插件
            Helper::removePlugin('TpRedis');
            return '插件已禁用';
            
        } catch (Exception $e) {
            // 如果发生其他错误，确保插件配置被删除
            Helper::removePlugin('TpRedis');
            return '插件已禁用（清理缓存时发生错误：' . $e->getMessage() . '）';
=======
            $uninstall_sql = 'DROP TABLE IF EXISTS `%prefix%cache`';
            $db = Typecho_Db::get();
            $prefix = $db->getPrefix();
            $sql = str_replace('%prefix%', $prefix, $uninstall_sql);
            $db->query($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
>>>>>>> origin/main
        }
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // 检查Redis扩展是否安装
<<<<<<< HEAD
        if (!extension_loaded('redis')) {
            $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('warning', null, 'Redis扩展未安装', '警告', 'Redis扩展未安装，插件无法正常工作。请先安装Redis扩展后再使用此插件。', 'style="color:red;font-weight:bold;"'));
            return;
        }

        // 缓存页面设置
        $cachePages = array(
=======
        $redis_installed = class_exists('Redis');
        if (!$redis_installed) {
            echo '<div class="message warning">您的PHP环境未安装Redis扩展，无法使用Redis缓存。将自动使用MySQL作为缓存驱动。<br>
            安装Redis扩展请参考：<a href="https://www.php.net/manual/zh/redis.installation.php" target="_blank">PHP Redis扩展安装教程</a></div>';
        } else {
            echo '<div class="message success">Redis扩展已安装，可以使用Redis作为缓存驱动</div>';
        }

        $list = array(
>>>>>>> origin/main
            'index' => '首页',
            'archive' => '归档',
            'post' => '文章',
            'attachment' => '附件',
            'category' => '分类',
            'tag' => '标签',
            'author' => '作者',
            'search' => '搜索',
            'feed' => 'feed',
<<<<<<< HEAD
            'page' => '页面'
        );
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Checkbox(
            'cache_page',
            $cachePages,
            array('index', 'post', 'search', 'page', 'author', 'tag'),
            '需要缓存的页面',
            '选择需要缓存的页面类型'
        ));

        // 缓存时间设置
        $cacheTimes = array(
            '3600' => '1小时',
            '7200' => '2小时',
            '14400' => '4小时',
            '28800' => '8小时',
            '43200' => '12小时',
            '86400' => '1天',
            '172800' => '2天',
            '259200' => '3天',
            '604800' => '1周',
            '2592000' => '30天',
            'custom' => '自定义'
        );
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Select(
            'cache_time_preset',
            $cacheTimes,
            '86400',
            '缓存时间预设',
            '选择预设的缓存时间'
        ));

        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
            'cache_time_custom',
            null,
            '86400',
            '自定义缓存时间（秒）',
            '当选择"自定义"时生效，请输入秒数'
        ));

        // 高级设置
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
            'login',
            array('0' => '关闭', '1' => '开启'),
            '1',
            '是否对已登录用户失效',
            '已登录用户不会触发缓存策略'
        ));

        $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
            'enable_ssl',
            array('0' => '关闭', '1' => '开启'),
            '0',
            '是否支持SSL',
            '是否支持HTTPS页面缓存'
        ));

        // 缓存规则
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
            'cache_mobile',
            array('0' => '关闭', '1' => '开启'),
            '1',
            '是否区分移动端缓存',
            '开启后将为移动端和桌面端分别缓存'
        ));

        $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
            'cache_query',
            array('0' => '关闭', '1' => '开启'),
            '0',
            '是否缓存带参数的URL',
            '开启后将缓存包含查询参数的URL'
        ));

        $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea(
            'cache_exclude',
            null,
            '',
            '排除缓存规则',
            '每行一个规则，支持正则表达式。例如：<br>/admin/*<br>/feed/*<br>/action/*'
        ));

        // Redis设置
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
            'redis_host',
            null,
            '127.0.0.1',
            'Redis主机地址',
            'Redis服务器地址，一般为127.0.0.1'
        ));

        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
            'redis_port',
            null,
            '6379',
            'Redis端口号',
            'Redis端口号，默认为6379'
        ));

        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
            'redis_password',
            null,
            '',
            'Redis密码',
            'Redis服务器密码，如果没有设置密码请留空'
        ));

        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
            'redis_database',
            null,
            '0',
            'Redis数据库',
            'Redis数据库编号，默认为0'
        ));

        // 调试设置
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
            'is_debug',
            array('0' => '关闭', '1' => '开启'),
            '0',
            '是否开启debug',
            '开启后将在页面显示缓存状态信息'
        ));

        $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
            'is_clean',
            array('0' => '关闭', '1' => '清除所有数据'),
            '0',
            '清除所有数据',
            '用于清除所有缓存数据（仅生效一次）'
        ));

        // 审核刷新设置
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
            'refresh_on_approve',
            array('0' => '关闭', '1' => '开启'),
            '1',
            '审核评论后刷新缓存',
            '开启后，当管理员批准评论时，将自动刷新相关文章和首页缓存'
        ));

        // 隐藏字段
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Hidden('cache_driver', 'redis'));
=======
            'page' => '页面',
        );
        $element = new Typecho_Widget_Helper_Form_Element_Checkbox('cache_page', $list, array('index', 'post', 'search', 'page', 'author', 'tag'), '需要缓存的页面', '选择需要缓存的页面类型');
        $form->addInput($element);

        $list = array('0' => '关闭', '1' => '开启');
        $element = new Typecho_Widget_Helper_Form_Element_Radio('login', $list, 1, '是否对已登录用户失效', '开启后已登录用户不会触发缓存策略');
        $form->addInput($element);

        $list = array('0' => '关闭', '1' => '开启');
        $element = new Typecho_Widget_Helper_Form_Element_Radio('enable_ssl', $list, '0', '是否支持SSL', '开启后会支持HTTPS页面的缓存');
        $form->addInput($element);

        // 缓存驱动选项
        $driver_options = array('0' => '不使用缓存');
        
        // 如果Redis扩展已安装，添加Redis选项
        if ($redis_installed) {
            $driver_options['redis'] = 'Redis';
        }
        
        // 总是提供MySQL选项
        $driver_options['mysql'] = 'MySQL';
        
        $default_driver = $redis_installed ? 'redis' : 'mysql';
        $element = new Typecho_Widget_Helper_Form_Element_Radio('cache_driver', $driver_options, $default_driver, '缓存驱动', '选择缓存数据的存储方式');
        $form->addInput($element);

        $element = new Typecho_Widget_Helper_Form_Element_Text('expire', null, '86400', '缓存过期时间(秒)', '86400 = 60s * 60m *24h，即一天的秒数');
        $form->addInput($element);

        // Redis相关配置，如果Redis扩展已安装才显示
        if ($redis_installed) {
            $element = new Typecho_Widget_Helper_Form_Element_Text('host', null, '127.0.0.1', 'Redis主机地址', '主机地址，一般为127.0.0.1');
            $form->addInput($element);

            $element = new Typecho_Widget_Helper_Form_Element_Text('port', null, '6379', 'Redis端口号', '端口号，Redis默认为6379');
            $form->addInput($element);
            
            $element = new Typecho_Widget_Helper_Form_Element_Text('password', null, '', 'Redis密码', '如果Redis设置了密码，请在此填写');
            $form->addInput($element);
            
            $element = new Typecho_Widget_Helper_Form_Element_Text('database', null, '0', 'Redis数据库', 'Redis数据库编号，默认为0');
            $form->addInput($element);
        }
        
        $element = new Typecho_Widget_Helper_Form_Element_Text('prefix', null, 'typecho_', '缓存键前缀', '用于区分不同网站的缓存数据');
        $form->addInput($element);

        $list = array('0' => '关闭', '1' => '开启');
        $element = new Typecho_Widget_Helper_Form_Element_Radio('is_debug', $list, 0, '是否开启debug', '开启后会显示缓存相关信息');
        $form->addInput($element);

        $list = array('0' => '关闭', '1' => '清除所有数据');
        $element = new Typecho_Widget_Helper_Form_Element_Radio('is_clean', $list, 0, '清除所有缓存', '选择开启后保存设置会清除所有缓存数据');
        $form->addInput($element);
>>>>>>> origin/main
    }

    /**
     * 手动保存配置句柄
     * @param $config array 插件配置
     * @param $is_init bool 是否初始化
     */
    public static function configHandle($config, $is_init)
    {
<<<<<<< HEAD
        if ($is_init != true) {
            try {
                // 检查Redis扩展
                if (!extension_loaded('redis')) {
                    throw new Typecho_Plugin_Exception('Redis扩展未安装，请先安装Redis扩展后再使用此插件。');
                }

                // 处理缓存时间设置
                if ($config['cache_time_preset'] === 'custom') {
                    $config['expire'] = (int)$config['cache_time_custom'];
                } else {
                    $config['expire'] = (int)$config['cache_time_preset'];
                }

                // 处理缓存排除规则
                if (!empty($config['cache_exclude'])) {
                    $config['cache_exclude'] = array_filter(
                        array_map('trim', explode("\n", $config['cache_exclude']))
                    );
                }

                // 设置Redis配置
                $config['host'] = $config['redis_host'];
                $config['port'] = $config['redis_port'];
                $config['password'] = $config['redis_password'];
                $config['database'] = $config['redis_database'];
                $config['cache_driver'] = 'redis';

                // 保存配置
                Helper::configPlugin('TpRedis', $config);

                // 如果选择了清理缓存，则执行清理
                if ($config['is_clean'] == '1') {
                    try {
                        self::init_driver();
                        if (self::$cache) {
                            self::$cache->flush();
                        }
                    } catch (Exception $e) {
                        throw new Typecho_Plugin_Exception('清理缓存失败：' . $e->getMessage());
                    }
                    // 删除缓存仅生效一次
                    $config['is_clean'] = '0';
                    Helper::configPlugin('TpRedis', $config);
                }
            } catch (Exception $e) {
                throw new Typecho_Plugin_Exception($e->getMessage());
            }
        }
=======
        if ($is_init != true && $config['cache_driver'] != '0') {
            try {
                // 如果选择了Redis但PHP没有安装Redis扩展，则自动切换到MySQL
                if ($config['cache_driver'] == 'redis' && !class_exists('Redis')) {
                    echo '<div class="message warning">PHP Redis扩展未安装，已自动切换到MySQL缓存驱动</div>';
                    $config['cache_driver'] = 'mysql';
                }
                
                $driver_name = $config['cache_driver'];
                $class_name = "typecho_$driver_name";
                $file_path = __DIR__ . "/driver/$class_name.class.php";
                
                if (!file_exists($file_path)) {
                    throw new Exception('缓存驱动文件不存在：' . $file_path);
                }
                
                require_once __DIR__ . '/driver/cache.interface.php';
                require_once $file_path;
                
                if (!class_exists($class_name)) {
                    throw new Exception('缓存驱动类不存在：' . $class_name);
                }
                
                self::$cache = call_user_func(array($class_name, 'getInstance'), $config);
                
                if ($config['is_clean'] == '1') {
                    self::$cache->flush();
                    // 删除缓存仅生效一次
                    $config['is_clean'] = '0';
                }
            } catch (Exception $e) {
                echo '<div class="message error">' . $e->getMessage() . '</div>';
            }
        }

        Helper::configPlugin('TpRedis', $config);
>>>>>>> origin/main
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 缓存前置操作
     */
    public static function C()
    {
        $start = microtime(true);
<<<<<<< HEAD
        // 插件初始化
        if (!self::init()) return false;
        // 前置条件检查
        if (!self::pre_check()) return false;
=======
        if (!self::init()) {
            return false;
        }
        if (!self::pre_check()) {
            return false;
        }
>>>>>>> origin/main

        try {
            $data = self::get(self::$path);
            if ($data != false) {
                $data = unserialize($data);
                //如果超时
                if ($data['c_time'] + self::$plugin_config->expire <= time()) {
<<<<<<< HEAD
                    if (self::$plugin_config->is_debug) echo "Expired!\n";
                    $data['c_time'] = $data['c_time'] + 20;
                    self::set(self::$path, serialize($data));
                } else {
                    if (self::$plugin_config->is_debug) echo "Hit!\n";
                    if ($data['html']) echo $data['html'];
                    $end = microtime(true);
                    $time = number_format(($end - $start), 6);
                    if (self::$plugin_config->is_debug) echo 'This page loaded in ', $time, ' seconds';
                    die;
                }
            } else {
                if (self::$plugin_config->is_debug) echo "Can't find cache!";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        // 先进行一次刷新
        ob_flush();
=======
                    $data['c_time'] = $data['c_time'] + 20;
                    self::set(self::$path, serialize($data));
                } else {
                    if ($data['html']) {
                        echo $data['html'];
                        $end = microtime(true);
                        $time = number_format(($end - $start), 6);
                        die;
                    }
                }
            }

        } catch (Exception $e) {
            echo "<!-- TpRedis错误: " . $e->getMessage() . " -->";
        }
        // 先进行一次刷新
        if (function_exists('ob_flush')) {
            ob_flush();
        }
>>>>>>> origin/main
    }

    /**
     * 前置检查
     * @return bool
     */
    public static function pre_check()
    {
<<<<<<< HEAD
        // 对登录用户失效
        if (self::check_login()) return false;
        
        // 针对POST失效
        if (self::$request->isPost()) return false;
        
        // 是否支持SSL
        if (self::$plugin_config->enable_ssl == '0' && self::$request->isSecure() == true) return false;

        // 检查移动端缓存
        if (self::$plugin_config->cache_mobile == '1') {
            $userAgent = self::$request->getAgent();
            $isMobile = preg_match('/(iPhone|iPod|Android|ios|iPad|Mobile)/i', $userAgent);
            if ($isMobile) {
                self::$key .= '_mobile';
            }
        }

        // 检查URL参数缓存
        if (self::$plugin_config->cache_query == '0') {
            $query = self::$request->getQuery();
            if (!empty($query)) return false;
        }

        // 检查排除规则
        if (!empty(self::$plugin_config->cache_exclude)) {
            $path = self::$request->getPathInfo();
            foreach (self::$plugin_config->cache_exclude as $pattern) {
                if (preg_match('#' . $pattern . '#i', $path)) {
                    return false;
                }
            }
        }

        // 检查缓存大小限制
        if (self::$plugin_config->cache_max_size > 0) {
            $content = ob_get_contents();
            if (strlen($content) > self::$plugin_config->cache_max_size) {
                return false;
            }
        }

=======
        //对登录用户失效
        if (self::check_login()) return false;
        //针对POST失效
        if (self::$request->isPost()) return false;
        //是否支持SSL
        if (self::$plugin_config->enable_ssl == '0' && self::$request->isSecure() == true) return false;
>>>>>>> origin/main
        return true;
    }

    /**
     * 判断用户是否登录
     * @return bool
     * @throws Typecho_Widget_Exception
     */
    public static function check_login()
    {
<<<<<<< HEAD
        //http与https相互独立
        return (self::$plugin_config->login && Typecho_Widget::widget('Widget_User')->hasLogin());
=======
        //判断是否对登录用户失效
        return (self::$plugin_config->login == '1' && Typecho_Widget::widget('Widget_User')->hasLogin());
>>>>>>> origin/main
    }

    /**
     * 根据配置判断是否需要缓存
     * @param string 路径信息
     * @return bool
     */
    public static function needCache($path)
    {
<<<<<<< HEAD
=======
        $_routingTable = self::$sys_config->routingTable;

>>>>>>> origin/main
        //后台数据不缓存
        $pattern = '#^' . __TYPECHO_ADMIN_DIR__ . '#i';
        if (preg_match($pattern, $path)) return false;

        //action动作不缓存
        $pattern = '#^/action#i';
        if (preg_match($pattern, $path)) return false;

<<<<<<< HEAD
        $_routingTable = self::$sys_config->routingTable;

        $exclude = array('_year', '_month', '_day', '_page');

        foreach ($_routingTable[0] as $key => $route) {
            if ($route['widget'] != 'Widget_Archive') continue;

            if (preg_match($route['regx'], $path, $matches)) {
                $key = str_replace($exclude, '', str_replace($exclude, '', $key));

                if (in_array($key, self::$plugin_config->cache_page)) {
                    if (self::$plugin_config->is_debug) echo "This page needs to be cached!\n" . '
<a href="http://www.phpgao.com/tpcache_for_typecho.html" target="_blank"> Bug Report </a>';
                    self::$path = $path;
                    return true;
                }
=======
        $exclude = array('_year', '_month', '_day', '_page');

        // 兼容一维和二维路由表
        $routes = isset($_routingTable[0]) && is_array($_routingTable[0]) ? $_routingTable[0] : $_routingTable;

        foreach ($routes as $key => $route) {
            if (!isset($route['widget']) || !preg_match('/Archive$/', $route['widget'])) continue;

            $match = preg_match($route['regx'], $path, $matches);

            $key_stripped = str_replace($exclude, '', str_replace($exclude, '', $key));

            if ($match && in_array($key_stripped, self::$plugin_config->cache_page)) {
                self::$path = $path;
                return true;
>>>>>>> origin/main
            }
        }

        return false;
    }

<<<<<<< HEAD
=======

>>>>>>> origin/main
    /**
     * 缓存后置操作
     */
    public static function S()
    {
        //对登录用户失效
        if (self::check_login()) return;

        //若self::$key不为空，则使用缓存
        if (is_null(self::$key)) return;

        $html = ob_get_contents();

        if (!empty($html)) {
            $data = array();
            $data['c_time'] = time();
            $data['html'] = $html;
            //更新缓存
<<<<<<< HEAD
            if (self::$plugin_config->is_debug) echo "Cache updated!\n";
=======
>>>>>>> origin/main
            self::set(self::$key, serialize($data));
        }
    }

<<<<<<< HEAD
=======

>>>>>>> origin/main
    /**
     * 编辑文章后更新缓存
     * @param $contents
     * @param $class
     */
    public static function post_update($contents, $class)
    {
        if ('publish' != $contents['visibility'] || $contents['created'] > time()) {
            return;
        }
        //获取系统配置
        $options = Helper::options();

        if(!$options->plugin('TpRedis')->cache_driver){
            return;
        }
        //获取文章类型
        $type = $contents['type'];
        //获取路由信息
        $routeExists = (NULL != Typecho_Router::get($type));

        if (!is_null($routeExists)) {
            $db = Typecho_Db::get();
            $contents['cid'] = $class->cid;
            $contents['categories'] = $db->fetchAll($db->select()->from('table.metas')
                ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
                ->where('table.relationships.cid = ?', $contents['cid'])
                ->where('table.metas.type = ?', 'category')
                ->order('table.metas.order', Typecho_Db::SORT_ASC));
            $contents['category'] = urlencode(current(Typecho_Common::arrayFlatten($contents['categories'], 'slug')));
            $contents['slug'] = urlencode($contents['slug']);
            $contents['date'] = new Typecho_Date($contents['created']);
            $contents['year'] = $contents['date']->year;
            $contents['month'] = $contents['date']->month;
            $contents['day'] = $contents['date']->day;
        }

        //生成永久连接
        $path_info = $routeExists ? Typecho_Router::url($type, $contents) : '#';

        if (self::init($path_info)) self::delete($path_info);
    }

    /**
<<<<<<< HEAD
     * 评论审核批准时刷新缓存
     * @param array|int $cids 评论ID或ID数组
     * @throws Typecho_Exception
     */
    public static function comment_approve($cids)
    {
        if (self::init() && self::$plugin_config->refresh_on_approve == '1') {
            $db = Typecho_Db::get();
            $options = Helper::options();

            // 处理批量批准
            if (!is_array($cids)) {
                $cids = array($cids);
            }

            $paths = array('/'); // 始终刷新首页

            foreach ($cids as $cid) {
                // 获取评论所属文章CID
                $comment = $db->fetchRow($db->select('cid')->from('table.comments')->where('coid = ?', $cid));
                if ($comment && isset($comment['cid'])) {
                    $post = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $comment['cid']));
                    if ($post) {
                        // 生成文章路径
                        $route = Typecho_Router::get($post['type']);
                        if ($route) {
                            $post['categories'] = $db->fetchAll($db->select()->from('table.metas')
                                ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
                                ->where('table.relationships.cid = ?', $post['cid'])
                                ->where('table.metas.type = ?', 'category')
                                ->order('table.metas.order', Typecho_Db::SORT_ASC));
                            $post['category'] = urlencode(current(Typecho_Common::arrayFlatten($post['categories'], 'slug')));
                            $post['slug'] = urlencode($post['slug']);
                            $post['date'] = new Typecho_Date($post['created']);
                            $post['year'] = $post['date']->year;
                            $post['month'] = $post['date']->month;
                            $post['day'] = $post['date']->day;

                            $path = Typecho_Router::url($post['type'], $post, $options->index);
                            $paths[] = $path;
                        }
                    }
                }
            }

            // 删除缓存
            self::delete(array_unique($paths));
        }
    }

    /**
     * 评论更新（提交新评论时）
     * @param array $comment 评论数据
     */
    public static function comment_update($comment)
    {
        if (self::init()) {
            $req = new Typecho_Request();
            $pathInfo = $req->getPathInfo();
            $articleUrl = preg_replace('//comment$/i', '', $pathInfo);
            
            // 刷新文章和首页
            $paths = array($articleUrl, '/');
            self::delete($paths);
        }
=======
     * 评论更新
     *
     * @access public
     * @param array $comment 评论结构
     * @param Typecho_Widget $post 被评论的文章
     * @param array $result 返回的结果上下文
     * @param string $api api地址
     * @return void
     */
    public static function comment_update($comment)
    {
        $req = new Typecho_Request();
        // 获取评论的PATH_INFO
        $path_info = $req->getPathInfo();
        // 删除最后的 /comment就是需删除的key
        $article_url = preg_replace('/\/comment$/i','',$path_info);

        self::init($article_url);
        
        self::delete($article_url);
>>>>>>> origin/main
    }

    /**
     * 插件配置初始化
<<<<<<< HEAD
     * @param string $pathInfo
     * @return bool
     */
    public static function init($pathInfo = '')
    {
        try {
        if (is_null(self::$sys_config)) {
            self::$sys_config = Helper::options();
        }
            
            // 检查插件是否已启用
            $pluginOptions = self::$sys_config->plugin('TpRedis');
            if (!$pluginOptions) {
                return false;
            }
            
        if (is_null(self::$plugin_config)) {
                self::$plugin_config = $pluginOptions;
        }

            if (!self::$plugin_config || !isset(self::$plugin_config->cache_driver) || self::$plugin_config->cache_driver == '0') {
            return false;
        }

            if (empty($pathInfo)) {
            if (is_null(self::$request)) {
                self::$request = new Typecho_Request();
            }
            //获取路径信息
            $pathInfo = self::$request->getPathInfo();
            }

            //判断是否需要缓存
            if (!self::needCache($pathInfo)) {
                return false;
        }

        self::init_driver();
        return true;
        } catch (Exception $e) {
            // 记录错误日志但不抛出异常，避免影响网站访问
            error_log('TpRedis plugin init error: ' . $e->getMessage());
            return false;
        }
=======
     * @param $pathInfo
     * @return bool
     * @throws Typecho_Plugin_Exception
     */
    public static function init($pathInfo='')
    {
        if (is_null(self::$sys_config)) {
            self::$sys_config = Helper::options();
        }
        if (is_null(self::$plugin_config)) {
            self::$plugin_config = self::$sys_config->plugin('TpRedis');
        }

        if (self::$plugin_config->cache_driver == '0') {
            return false;
        }

        if(empty($pathInfo)){

            if (is_null(self::$request)) {
                self::$request = new Typecho_Request();
            }

            //获取路径信息
            $pathInfo = self::$request->getPathInfo();

        }
        //判断是否需要缓存
        if (!self::needCache($pathInfo)) {
            return false;
        }

        self::init_driver();

        return true;
>>>>>>> origin/main
    }

    /**
     * 插件驱动初始化
     * @return bool
     * @throws Typecho_Plugin_Exception
     */
<<<<<<< HEAD
    public static function init_driver()
    {
        if (is_null(self::$cache)) {
            try {
                require_once dirname(__FILE__) . '/driver/cache.interface.php';
                
                $driver = self::$plugin_config->cache_driver;
                $driverClass = 'typecho_' . $driver;
                require_once dirname(__FILE__) . "/driver/{$driverClass}.class.php";
                
                // 检查驱动类是否存在
                if (!class_exists($driverClass)) {
                    throw new Typecho_Plugin_Exception('缓存驱动类不存在：' . $driverClass);
                }
                
                // 检查必要的扩展
                if ($driver === 'redis' && !extension_loaded('redis')) {
                    throw new Typecho_Plugin_Exception('Redis扩展未安装，请安装Redis扩展或切换到MySQL驱动');
                }
                
                self::$cache = call_user_func(array($driverClass, 'getInstance'), self::$plugin_config);
                if (!self::$cache) {
                    throw new Typecho_Plugin_Exception('缓存驱动初始化失败');
                }
                
                return true;
            } catch (Exception $e) {
                // 记录错误日志
                error_log('TpRedis driver init error: ' . $e->getMessage());
                throw new Typecho_Plugin_Exception($e->getMessage());
            }
        }
        return true;
    }

    public static function set($path, $data)
    {
=======
    public static function init_driver(){
        if (is_null(self::$cache)) {
            $driver_name = self::$plugin_config->cache_driver;
            $class_name = "typecho_$driver_name";
            $file_path = __DIR__ . "/driver/$class_name.class.php";
            require_once __DIR__ . '/driver/cache.interface.php';
            require_once $file_path;
            self::$cache = call_user_func(array($class_name, 'getInstance'), self::$plugin_config);
        }
    }


    public static function set($path, $data)
    {

>>>>>>> origin/main
        if (!is_null(self::$key)) return self::$cache->set(self::$key, $data);
        $prefix = self::$request->getUrlPrefix();
        self::$key = md5($prefix . $path);

        return self::$cache->set(self::$key, $data);
    }

    public static function add($path, $data)
    {
<<<<<<< HEAD

=======
        if (!is_null(self::$key)) return self::$cache->add(self::$key, $data);
        $prefix = self::$request->getUrlPrefix();
        self::$key = md5($prefix . $path);

        return self::$cache->add(self::$key, $data);
>>>>>>> origin/main
    }

    public static function get($path)
    {
        if (!is_null(self::$key)) return self::$cache->get(self::$key);
        $prefix = self::$request->getUrlPrefix();
        self::$key = md5($prefix . $path);
        return self::$cache->get(self::$key);
    }

    /**
     * 删除指定路径
<<<<<<< HEAD
     * @param string|array $path 待删除路径或路径数组
     */
    public static function delete($path)
    {
        if (self::init()) {
            $prefixs = array(
                'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . (in_array($_SERVER['SERVER_PORT'], array(80, 443)) ? '' : ':' . $_SERVER['SERVER_PORT']))),
                'https://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . (in_array($_SERVER['SERVER_PORT'], array(80, 443)) ? '' : ':' . $_SERVER['SERVER_PORT'])))
            );

            $keys = is_array($path) ? $path : array($path);

            foreach ($keys as $v) {
                foreach ($prefixs as $prefix) {
                    self::$cache->delete(md5($prefix . $v));
                }
=======
     * @param string $path 待删除路径
     * @param null $del_home 是否删除首页缓存
     */
    public static function delete($path, $del_home = null)
    {
        $prefixs = array(
            'http'
            . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
                ($_SERVER['SERVER_NAME'] . (in_array($_SERVER['SERVER_PORT'], array(80, 443))
                        ? '' : ':' . $_SERVER['SERVER_PORT']))
            ),
            'https'
            . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
                ($_SERVER['SERVER_NAME'] . (in_array($_SERVER['SERVER_PORT'], array(80, 443))
                        ? '' : ':' . $_SERVER['SERVER_PORT']))
            ),
        );
        $keys = array();
        if (!is_array($path)) {
            $keys[] = $path;
        } else {
            $keys = $path;
        }


        foreach ($keys as $v) {
            foreach ($prefixs as $prefix) {
                @self::$cache->delete(md5($prefix . $v));
            }
        }

        if (is_null($del_home)) {
            foreach ($prefixs as $prefix) {
                @self::$cache->delete(md5($prefix . '/'));
>>>>>>> origin/main
            }
        }
    }
}
