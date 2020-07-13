<?php
/**
 * User: YL
 * Date: 2020/07/13
 */

namespace Jmhc\Console;

use Symfony\Component\Console\Input\InputOption;

class MakeValidateCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-validate';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Validate';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/validate.stub';

    /**
     * 设置参数、选项
     */
    protected function setArgumentOption()
    {
        parent::setArgumentOption();

        // 引入、继承类
        $this->uses = PHP_EOL . 'use ' . $this->optionValidateExtendsCustom . ';';
        $this->extends = ' extends ' . class_basename($this->optionValidateExtendsCustom);
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('controller', null, InputOption::VALUE_NONE, 'Generate the controller file with the same name');
        $this->addOption('model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name');
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
    }
}
