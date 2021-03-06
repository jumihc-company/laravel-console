<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * 生成工厂
 * @package Jmhc\Restful\Console\Commands
 */
class MakeFactoryCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-factory';

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate the factory file';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Factory';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/factory.stub';

    /**
     * 注释
     * @var string
     */
    protected $annotation;

    /**
     * 参数 name
     * @var string
     */
    protected $argumentName;

    /**
     * 选项 scan-dir
     * @var array
     */
    protected $optionScanDir;

    /**
     * 选项 factory-extends-custom
     * @var string
     */
    protected $optionFactoryExtendsCustom;

    /**
     * 生成前操作
     */
    protected function buildBeforeHandle()
    {
        parent::buildBeforeHandle();

        // 注解内容
        $this->annotation = $this->getAnnotation($this->getScans());
    }

    /**
     * 执行额外命令
     */
    protected function extraCommands()
    {}

    /**
     * 获取生成内容
     * @return string
     */
    protected function getBuildContent()
    {
        $content = file_get_contents($this->stubPath);

        // 替换
        $this->replaceNamespace($content, $this->namespace)
            ->replaceClass($content, $this->class)
            ->replaceUses($content, $this->uses)
            ->replaceAnnotations($content, $this->annotation)
            ->replaceExtends($content, $this->extends);

        return $content;
    }

    /**
     * 获取扫描结果
     * @return array
     */
    protected function getScans()
    {
        // app 目录
        $appPath = app_path();

        $scans = [];
        foreach ($this->optionScanDir as $dir) {
            foreach (glob($dir . '*.php') as $file) {
                if (strpos($file, $appPath) !== 0) {
                    continue;
                }

                $_class = rtrim(basename($file), '\.php');
                $_file = str_replace([$appPath, sprintf('/%s.php', $_class)], '', $file);
                $scans[] = [
                    'namespace' => sprintf(
                        '\\App%s\\%s',
                        str_replace('/', '\\', $_file),
                        $_class
                    ),
                    'method' => lcfirst($_class),
                ];
            }
        }

        return $scans;
    }

    /**
     * 获取注解
     * @param array $scans
     * @return string
     */
    protected function getAnnotation(array $scans)
    {
        if (empty($scans)) {
            return '';
        }

        $str = '/**';
        foreach ($scans as $scan) {
            $str .= "\n" . sprintf(' * @method static %s %s(bool $refresh = false, array $params = [])', $scan['namespace'], $scan['method']);
        }

        return "\n" . $str . "\n * @package " . $this->namespace . "\n */";
    }

    /**
     * 获取保存文件夹
     * @return string
     */
    protected function getSaveDir()
    {
        return $this->filterOptionDir($this->optionDir);
    }

    /**
     * 设置参数、选项
     */
    protected function setArgumentOption()
    {
        $this->argumentName = $this->filterArgumentName(
            $this->argument('name'),
            $this->entityName
        );
        $this->optionScanDir = array_map(function ($v) {
            return app_path($this->filterOptionDir($v));
        }, $this->option('scan-dir'));
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionForce = $this->option('force');
        $this->optionSuffix = $this->option('suffix');
        $this->optionFactoryExtendsCustom = $this->getCommandClass($this->option('factory-extends-custom'));

        // 引入、继承类
        $this->uses = "\n\nuse " . $this->optionFactoryExtendsCustom . ';';
        $this->extends = ' extends ' . class_basename($this->optionFactoryExtendsCustom);
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, $this->entityName . ' name');

        $this->addOption('scan-dir', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'File scanning path, relative to app directory');
        $this->addOption('dir', null, InputOption::VALUE_REQUIRED, 'File saving path, relative to app directory', $this->makeFactoryOptionDirDefault());
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing files');
        $this->addOption('suffix', 's', InputOption::VALUE_NONE, sprintf('Add the `%s` suffix', $this->entityName));
        $this->addOption('factory-extends-custom', null, InputOption::VALUE_REQUIRED, 'The custom factory inherits its parent class', $this->optionFactoryExtendsCustomDefault());
    }
}
