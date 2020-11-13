<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Jmhc\Console\Traits\ConfigureDefaultTrait;
use Jmhc\Console\Traits\MakeTrait;
use Symfony\Component\Console\Input\InputOption;

/**
 * 通过关联文件生成
 * @package Jmhc\Restful\Console\Commands
 */
class MakeWithFileCommand extends Command
{
    use MakeTrait;
    use ConfigureDefaultTrait;

    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-with-file';

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate some file with file';

    /**
     * 选项 dir
     * @var string
     */
    protected $optionDir;

    /**
     * 选项 suffix
     * @var string
     */
    protected $optionSuffix;

    /**
     * 选项 controller
     * @var string
     */
    protected $optionController;

    /**
     * 是否覆盖控制器
     * @var bool
     */
    protected $isForceController;

    /**
     * 选项 model
     * @var string
     */
    protected $optionModel;

    /**
     * 是否覆盖模型
     * @var bool
     */
    protected $isForceModel;

    /**
     * 选项 service
     * @var string
     */
    protected $optionService;

    /**
     * 是否覆盖服务
     * @var bool
     */
    protected $isForceService;

    /**
     * 选项 validate
     * @var string
     */
    protected $optionValidate;

    /**
     * 是否覆盖验证器
     * @var bool
     */
    protected $isForceValidate;

    /**
     * 选项 migration
     * @var string
     */
    protected $optionMigration;

    /**
     * 选项 seeder
     * @var string
     */
    protected $optionSeeder;

    /**
     * 选项 model-extends-pivot
     * @var bool
     */
    protected $optionModelExtendsPivot;

    /**
     * 选项 model-casts-force
     * @var bool
     */
    protected $optionModelCastsForce;

    /**
     * 选项 controller-extends-custom
     * @var string
     */
    protected $optionControllerExtendsCustom;

    /**
     * 选项 model-extends-custom
     * @var string
     */
    protected $optionModelExtendsCustom;

    /**
     * 选项 service-extends-custom
     * @var string
     */
    protected $optionServiceExtendsCustom;

    /**
     * 选项 validate-extends-custom
     * @var string
     */
    protected $optionValidateExtendsCustom;

    public function handle()
    {
        // 设置参数、选项
        $this->setArgumentOption();

        // 读取生成文件配置
        $tables = config('jmhc-console.tables', []);

        // 数据表不存在
        if (empty($tables)) {
            // 运行完成
            $this->runComplete();
            return;
        }

        // 过滤名称
        $tables = $this->filterTables($tables);

        // 生成文件
        foreach ($tables as $table) {
            $this->buildFile($table);
        }

        // 运行完成
        $this->runComplete();
    }

    /**
     * 过滤表名
     * @param array $tables
     * @return array
     */
    protected function filterTables(array $tables)
    {
        // 数据表前缀
        $prefix = app('db.connection')->getConfig('prefix');

        return array_values(array_filter(array_unique(array_map(function ($table) use ($prefix) {
            return str_replace($prefix, '', $table);
        }, $tables))));
    }

    /**
     * 创建文件
     * @param string $name
     */
    protected function buildFile(string $name)
    {
        // 命令参数
        $arguments = [
            'name' => $name,
            '--suffix' => $this->optionSuffix,
            '--model-extends-pivot' => $this->optionModelExtendsPivot,
            '--model-casts-force' => $this->optionModelCastsForce,
            '--controller-extends-custom' => $this->optionControllerExtendsCustom,
            '--model-extends-custom' => $this->optionModelExtendsCustom,
            '--service-extends-custom' => $this->optionServiceExtendsCustom,
            '--validate-extends-custom' => $this->optionValidateExtendsCustom,
        ];

        // 创建控制器
        if ($this->optionController) {
            $arguments['--force'] = $this->isForceController;
            $arguments['--dir'] = $this->optionDir . 'Controllers/';
            $this->call('jmhc-api:make-controller', $arguments);
        }

        // 创建模型
        if ($this->optionModel) {
            $arguments['--force'] = $this->isForceModel;
            $arguments['--dir'] = $this->optionDir . 'Models/';
            $this->call('jmhc-api:make-model', $arguments);
        }

        // 创建服务
        if ($this->optionService) {
            $arguments['--force'] = $this->isForceService;
            $arguments['--dir'] = $this->optionDir . 'Services/';
            $this->call('jmhc-api:make-service', $arguments);
        }

        // 创建验证器
        if ($this->optionValidate) {
            $arguments['--force'] = $this->isForceValidate;
            $arguments['--dir'] = $this->optionDir . 'Validates/';
            $this->call('jmhc-api:make-validate', $arguments);
        }

        // 创建迁移
        if ($this->optionMigration) {
            try {
                $this->call('make:migration', [
                    'name' => sprintf(
                        'create_%s_table',
                        Str::plural(Str::snake($name))
                    )
                ]);
            } catch (InvalidArgumentException $e) {}
        }

        // 创建填充
        if ($this->optionSeeder) {
            $this->call('make:seeder', [
                'name' => sprintf(
                    '%sTableSeeder',
                    Str::plural(ucfirst($name))
                )
            ]);
        }
    }

    /**
     * 设置参数、选项
     */
    protected function setArgumentOption()
    {
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionSuffix = $this->option('suffix');
        $this->optionController = $this->option('controller');
        $this->isForceController = $this->option('force') || $this->option('force-controller');
        $this->optionModel = $this->option('model');
        $this->isForceModel = $this->option('force') || $this->option('force-model');
        $this->optionService = $this->option('service');
        $this->isForceService = $this->option('force') || $this->option('force-service');
        $this->optionValidate = $this->option('validate');
        $this->isForceValidate = $this->option('force') || $this->option('force-validate');
        $this->optionMigration = $this->option('migration');
        $this->optionSeeder = $this->option('seeder');
        $this->optionModelExtendsPivot = $this->option('model-extends-pivot');
        $this->optionModelCastsForce = $this->option('model-casts-force');
        $this->optionControllerExtendsCustom = $this->getCommandClass($this->option('controller-extends-custom'));
        $this->optionModelExtendsCustom = $this->getCommandClass($this->option('model-extends-custom'));
        $this->optionServiceExtendsCustom = $this->getCommandClass($this->option('service-extends-custom'));
        $this->optionValidateExtendsCustom = $this->getCommandClass($this->option('validate-extends-custom'));
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        $this->addOption('dir', null, InputOption::VALUE_REQUIRED, 'File saving path, relative to app directory', $this->makeWithFileOptionDirDefault());
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file');
        $this->addOption('force-controller', null, InputOption::VALUE_NONE, 'Overwrite existing controller file');
        $this->addOption('force-model', null, InputOption::VALUE_NONE, 'Overwrite existing model file');
        $this->addOption('force-service', null, InputOption::VALUE_NONE, 'Overwrite existing service file');
        $this->addOption('force-validate', null, InputOption::VALUE_NONE, 'Overwrite existing validate file');
        $this->addOption('suffix', 's', InputOption::VALUE_NONE, sprintf('Add suffix'));
        $this->addOption('controller', null, InputOption::VALUE_NONE, 'Generate the controller file with the same name');
        $this->addOption('model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name');
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Generate the validate file with the same name');
        $this->addOption('migration', null, InputOption::VALUE_NONE, 'Generate the migration file with the same name');
        $this->addOption('seeder', null, InputOption::VALUE_NONE, 'Generate the seeder file with the same name');
        $this->addOption('model-extends-pivot', null, InputOption::VALUE_NONE, 'The model extends Jmhc\Restful\Models\BasePivot');
        $this->addOption('controller-extends-custom', null, InputOption::VALUE_REQUIRED, 'The custom controller inherits its parent class', $this->optionControllerExtendsCustomDefault());
        $this->addOption('model-extends-custom', null, InputOption::VALUE_REQUIRED, 'The custom model inherits its parent class', $this->optionModelExtendsCustomDefault());
        $this->addOption('service-extends-custom', null, InputOption::VALUE_REQUIRED, 'The custom service inherits its parent class', $this->optionServiceExtendsCustomDefault());
        $this->addOption('validate-extends-custom', null, InputOption::VALUE_REQUIRED, 'The custom validate inherits its parent class', $this->optionValidateExtendsCustomDefault());
    }
}
