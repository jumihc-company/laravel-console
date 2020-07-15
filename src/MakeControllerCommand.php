<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console;

use Symfony\Component\Console\Input\InputOption;

/**
 * 生成控制器
 * @package Jmhc\Restful\Console\Commands
 */
class MakeControllerCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-controller';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Controller';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/controller.stub';

    /**
     * 生成前操作
     */
    protected function buildBeforeHandle()
    {
        parent::buildBeforeHandle();

        // 命名空间相同重置引入
        if ($this->classNamespace($this->optionControllerExtendsCustom) === $this->namespace) {
            $this->uses = '';
        }
    }

    /**
     * 设置参数、选项
     */
    protected function setArgumentOption()
    {
        parent::setArgumentOption();

        // 引入、继承类
        $this->uses = "\n\nuse " . $this->optionControllerExtendsCustom . ';';
        $this->extends = ' extends ' . class_basename($this->optionControllerExtendsCustom);
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name');
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Generate the validate file with the same name');
    }
}
