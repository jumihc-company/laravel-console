<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console\Contracts;

/**
 * 配置默认值
 * @package Jmhc\Console\Contracts
 */
interface ConfigureDefaultInterface
{
    // make-controller 命令路径
    const MAKE_CONTROLLER_DIR = 'Http/Controllers/';
    // 控制器继承自定义
    const CONTROLLER_EXTENDS_CUSTOM = 'Jmhc\Restful\Controllers\BaseController';
    // make-model 命令路径
    const MAKE_MODEL_DIR = 'Http/Models/';
    // 模型继承自定义
    const MODEL_EXTENDS_CUSTOM = 'Jmhc\Restful\Models\BaseModel';
    // make-service 命令路径
    const MAKE_SERVICE_DIR = 'Http/Services/';
    // 服务继承自定义
    const SERVICE_EXTENDS_CUSTOM = 'Jmhc\Restful\Services\BaseService';
    // make-validate 命令路径
    const MAKE_VALIDATE_DIR = 'Http/Validates/';
    // 验证器继承自定义
    const VALIDATE_EXTENDS_CUSTOM = 'Jmhc\Restful\Validates\BaseValidate';
    // make-factory 命令路径
    const MAKE_FACTORY_DIR = 'Common/Factory/';
    // 工厂继承自定义
    const FACTORY_EXTENDS_CUSTOM = 'Jmhc\Restful\Factory\BaseFactory';
    // make-with-file 命令路径
    const MAKE_WITH_FILE_DIR = 'Http/';
}
