<?php

declare(strict_types=1);

interface TpCache
{
    /**
     * 初始化缓存驱动
     * @param mixed $option 配置选项
     * @return void
     */
    public function init($option): void;

    /**
     * 添加缓存，如果键已存在则返回false
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int|null $expire 过期时间（秒）
     * @return bool
     */
    public function add(string $key, $value, ?int $expire = null): bool;

    /**
     * 删除缓存
     * @param string $key 缓存键
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * 设置缓存
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int|null $expire 过期时间（秒）
     * @return bool
     */
    public function set(string $key, $value, ?int $expire = null): bool;

    /**
     * 获取缓存
     * @param string $key 缓存键
     * @return mixed|null 缓存值，如果不存在返回null
     */
    public function get(string $key);

    /**
     * 清空所有缓存
     * @return bool
     */
    public function flush(): bool;
}