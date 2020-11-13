<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console\Traits;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * 创建命令辅助
 * @package Jmhc\Restful\Console\Commands\Traits
 */
trait MakeTrait
{
    /**
     * 过滤字符串
     * @param string $str
     * @return string
     */
    protected function filterStr(string $str)
    {
        return str_replace(['/', '\\'], '', $str);
    }

    /**
     * 过滤路径
     * @param string $dir
     * @return array
     */
    protected function filterDir(string $dir)
    {
        return array_filter(
            explode(
                '/',
                str_replace('\\', '', $dir)
            )
        );
    }

    /**
     * 获取路径字符串
     * @param array $dir
     * @return string
     */
    protected function getDirStr(array $dir)
    {
        $res = '';
        foreach ($dir as $v) {
            $res .= ucfirst($v) . '/';
        }
        return $res;
    }

    /**
     * 过滤选项路径
     * @param string $dir
     * @return string
     */
    protected function filterOptionDir(string $dir)
    {
        return $this->getDirStr($this->filterDir($dir));
    }


    /**
     * 创建文件夹
     * @param string $dir
     * @return bool
     */
    protected function createDir(string $dir)
    {
        return ! is_dir($dir) && mkdir($dir, 0755, true);
    }

    /**
     * 获取命名空间
     * @param string $dir
     * @return string
     */
    protected function getNamespace(string $dir)
    {
        return 'App\\' . str_replace('/', '\\', rtrim($dir, '/'));
    }

    /**
     * 过滤参数名称
     * @param string $name
     * @param string $suffix
     * @return string
     */
    protected function filterArgumentName(string $name, string $suffix)
    {
        return Str::singular(preg_replace(
            sprintf('/%s$/i', $suffix),
            '',
            $this->filterStr($name)
        ));
    }

    /**
     * 获取命令行类名称
     * @param string $name
     * @return string
     */
    protected function getCommandClass(string $name)
    {
        return preg_replace('/\/+/', '\\', trim($name, '/'));
    }

    /**
     * 获取类的命名空间
     * @param string $name
     * @return string
     */
    protected function classNamespace(string $name)
    {
        $class = explode('\\', $name);
        array_pop($class);
        return implode('\\', $class);
    }

    /**
     * 运行完成
     */
    protected function runComplete()
    {
        $this->info(sprintf('Command %s run completed!', $this->name));
    }

    /**
     * 运行失败
     * @param string $msg
     */
    protected function runFail(string $msg)
    {
        $this->error(sprintf('Command %s run fail: %s', $this->name, $msg));
    }

    /**
     * 抛出异常
     * @param string $msg
     * @throws InvalidArgumentException
     */
    protected function throwThrowable(string $msg)
    {
        throw new InvalidArgumentException($msg);
    }
}
