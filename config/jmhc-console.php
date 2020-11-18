<?php

use Jmhc\Console\Contracts\ConfigureDefaultInterface;

return [
    // make-controller 命令路径
    'make_controller_dir' => env('JMHC_CONSOLE_MAKE_CONTROLLER_DIR', ConfigureDefaultInterface::MAKE_CONTROLLER_DIR),
    // 控制器继承自定义
    'controller_extends_custom' => env('JMHC_CONSOLE_CONTROLLER_EXTENDS_CUSTOM', ConfigureDefaultInterface::CONTROLLER_EXTENDS_CUSTOM),
    // make-model 命令路径
    'make_model_dir' => env('JMHC_CONSOLE_MAKE_MODEL_DIR', ConfigureDefaultInterface::MAKE_MODEL_DIR),
    // 模型继承自定义
    'model_extends_custom' => env('JMHC_CONSOLE_MODEL_EXTENDS_CUSTOM', ConfigureDefaultInterface::MODEL_EXTENDS_CUSTOM),
    // make-service 命令路径
    'make_service_dir' => env('JMHC_CONSOLE_MAKE_SERVICE_DIR', ConfigureDefaultInterface::MAKE_SERVICE_DIR),
    // 服务继承自定义
    'service_extends_custom' => env('JMHC_CONSOLE_SERVICE_EXTENDS_CUSTOM', ConfigureDefaultInterface::SERVICE_EXTENDS_CUSTOM),
    // make-validate 命令路径
    'make_validate_dir' => env('JMHC_CONSOLE_MAKE_VALIDATE_DIR', ConfigureDefaultInterface::MAKE_VALIDATE_DIR),
    // 验证器继承自定义
    'validate_extends_custom' => env('JMHC_CONSOLE_VALIDATE_EXTENDS_CUSTOM', ConfigureDefaultInterface::VALIDATE_EXTENDS_CUSTOM),
    // make-factory 命令路径
    'make_factory_dir' => env('JMHC_CONSOLE_MAKE_FACTORY_DIR', ConfigureDefaultInterface::MAKE_FACTORY_DIR),
    // 工厂继承自定义
    'factory_extends_custom' => env('JMHC_CONSOLE_FACTORY_EXTENDS_CUSTOM', ConfigureDefaultInterface::FACTORY_EXTENDS_CUSTOM),
    // make-with-file 命令路径
    'make_with_file_dir' => env('JMHC_CONSOLE_MAKE_WITH_FILE_DIR', ConfigureDefaultInterface::MAKE_WITH_FILE_DIR),
    // 数据表名,可不带前缀
    'tables' => [],
];
