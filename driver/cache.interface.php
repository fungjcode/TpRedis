<?php

/**
 * 缓存接口定义
 * 
 * @package TpRedis
 * @author 风之翼灵
 * @version 1.0.1
 * @link http://www.fungj.com
 */
interface TpCache
{
    /**
     * 初始化缓存
     * 
     * @param object $option 配置选项
     * @return void
     */
    public function init($option);

    /**
     * 添加缓存
     * 
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $expire 过期时间(秒)
     * @return bool 是否成功
     */
    public function add($key, $value, $expire=null);

    /**
     * 删除缓存
     * 
     * @param string $key 缓存键
     * @return bool 是否成功
     */
    public function delete($key);

    /**
     * 设置缓存
     * 
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $expire 过期时间(秒)
     * @return bool 是否成功
     */
    public function set($key, $value, $expire=null);

    /**
     * 获取缓存
     * 
     * @param string $key 缓存键
     * @return mixed 缓存值或false
     */
    public function get($key);

    /**
     * 清空所有缓存
     * 
     * @return bool 是否成功
     */
    public function flush();
}