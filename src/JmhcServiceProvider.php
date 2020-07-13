<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console;

use Illuminate\Support\ServiceProvider;

class JmhcServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        MakeControllerCommand::class,
        MakeModelCommand::class,
        MakeServiceCommand::class,
        MakeValidateCommand::class,
        MakeFactoryCommand::class,
        MakeWithFileCommand::class,
    ];

    /**
     * @var string
     */
    protected $buildFileConfigPath;

    public function boot()
    {
        $this->buildFileConfigPath = __DIR__ . '/../config/jmhc-build-file.php';

        // 注册命令
        $this->commands($this->commands);

        // 合并配置
        $this->mergeConfig();

        // 发布文件
        $this->publishFiles();
    }

    /**
     * 合并配置
     */
    protected function mergeConfig()
    {
        // 合并 build-file 配置
        $this->mergeConfigFrom(
            $this->buildFileConfigPath,
            'jmhc-build-file'
        );
    }

    /**
     * 发布文件
     */
    protected function publishFiles()
    {
        // 发布配置文件
        $this->publishes([
            $this->buildFileConfigPath => config_path('jmhc-build-file.php'),
        ], 'jmhc-console');
    }
}
