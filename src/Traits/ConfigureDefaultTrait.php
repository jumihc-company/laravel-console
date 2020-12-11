<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console\Traits;

use Jmhc\Console\Contracts\ConfigureDefaultInterface;

/**
 * 配置默认值辅助
 * @package Jmhc\Console\Traits
 */
trait ConfigureDefaultTrait
{
    /**
     * make-controller 命令选项 dir 默认值
     * @return string
     */
    public function makeControllerOptionDirDefault()
    {
        return $this->getOptionDirDefault('make_controller_dir', ConfigureDefaultInterface::MAKE_CONTROLLER_DIR);
    }

    /**
     * 选项 controller-extends-custom 默认值
     * @return string
     */
    public function optionControllerExtendsCustomDefault()
    {
        return $this->getOptionExtendsCustomDefault('controller_extends_custom', ConfigureDefaultInterface::CONTROLLER_EXTENDS_CUSTOM);
    }

    /**
     * make-model 命令选项 dir 默认值
     * @return string
     */
    public function makeModelOptionDirDefault()
    {
        return $this->getOptionDirDefault('make_model_dir', ConfigureDefaultInterface::MAKE_MODEL_DIR);
    }

    /**
     * 选项 model-extends-custom 默认值
     * @return string
     */
    public function optionModelExtendsCustomDefault()
    {
        return $this->getOptionExtendsCustomDefault('model_extends_custom', ConfigureDefaultInterface::MODEL_EXTENDS_CUSTOM);
    }

    /**
     * make-service 命令选项 dir 默认值
     * @return string
     */
    public function makeServiceOptionDirDefault()
    {
        return $this->getOptionDirDefault('make_service_dir', ConfigureDefaultInterface::MAKE_SERVICE_DIR);
    }

    /**
     * 选项 service-extends-custom 默认值
     * @return string
     */
    public function optionServiceExtendsCustomDefault()
    {
        return $this->getOptionExtendsCustomDefault('service_extends_custom', ConfigureDefaultInterface::SERVICE_EXTENDS_CUSTOM);
    }

    /**
     * make-validate 命令选项 dir 默认值
     * @return string
     */
    public function makeValidateOptionDirDefault()
    {
        return $this->getOptionDirDefault('make_validate_dir', ConfigureDefaultInterface::MAKE_VALIDATE_DIR);
    }

    /**
     * 选项 validate-extends-custom 默认值
     * @return string
     */
    public function optionValidateExtendsCustomDefault()
    {
        return $this->getOptionExtendsCustomDefault('validate_extends_custom', ConfigureDefaultInterface::VALIDATE_EXTENDS_CUSTOM);
    }

    /**
     * make-factory 命令选项 dir 默认值
     * @return string
     */
    public function makeFactoryOptionDirDefault()
    {
        return $this->getOptionDirDefault('make_factory_dir', ConfigureDefaultInterface::MAKE_FACTORY_DIR);
    }

    /**
     * 选项 factory-extends-custom 默认值
     * @return string
     */
    public function optionFactoryExtendsCustomDefault()
    {
        return $this->getOptionExtendsCustomDefault('factory_extends_custom', ConfigureDefaultInterface::FACTORY_EXTENDS_CUSTOM);
    }

    /**
     * make-with-file 命令选项 dir 默认值
     * @return string
     */
    public function makeWithFileOptionDirDefault()
    {
        return $this->getOptionDirDefault('make_with_file_dir', ConfigureDefaultInterface::MAKE_WITH_FILE_DIR);
    }

    /**
     * 获取选项 dir 默认值
     * @param string $key
     * @param string $default
     * @return string
     */
    private function getOptionDirDefault(string $key, string $default)
    {
        $config = config('jmhc-console.' . $key, '');
        return ! empty($config) ? $config : $default;
    }

    /**
     * 获取选项 extends-custom 默认值
     * @param string $key
     * @param string $default
     * @return string
     */
    private function getOptionExtendsCustomDefault(string $key, string $default)
    {
        $config = config('jmhc-console.' . $key, '');
        return class_exists($config) ? $config : $default;
    }
}
