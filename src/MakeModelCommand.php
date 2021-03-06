<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console;

use Illuminate\Support\Str;
use Jmhc\Console\Ast\ModelUpdateVisitor;
use Jmhc\Console\Traits\ReplaceModelTrait;
use Jmhc\Support\Helper\DBHelper;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * 生成模型
 * @package Jmhc\Restful\Console\Commands
 */
class MakeModelCommand extends MakeCommand
{
    use ReplaceModelTrait;

    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-model';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Model';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/model.stub';

    /**
     * 参数 name 模式
     * @var int
     */
    protected $argumentNameMode = InputArgument::OPTIONAL;

    /**
     * 导入对象
     * @var string
     */
    protected $uses;

    /**
     * 继承对象
     * @var string
     */
    protected $extends;

    /**
     * 选项 connection
     * @var string
     */
    protected $optionConnection;

    /**
     * 选项 table
     * @var array
     */
    protected $optionTable;

    /**
     * 数据库辅助类
     * @var DBHelper
     */
    protected $dbHelper;

    /**
     * 表前缀
     * @var string
     */
    protected $prefix;

    /**
     * 表名称
     * @var string
     */
    protected $table;

    public function __construct()
    {
        parent::__construct();

        $this->dbHelper = DBHelper::getInstance([
            'name' => $this->optionConnection,
        ]);
        $this->prefix = $this->dbHelper->getPrefix();
    }

    protected function mainHandle()
    {
        if (! empty($this->argumentName)) {
            return $this->buildModel($this->argumentName);
        }

        return $this->buildModels();
    }

    /**
     * 生成所有模型文件
     * @return bool
     */
    protected function buildModels()
    {
        // 获取所有表
        $tables = $this->dbHelper->getAllTables(false);
        foreach ($tables as $table) {
            if (in_array($table, $this->optionTable)) {
                continue;
            }
            $this->buildModel($table);
        }

        return true;
    }

    /**
     * 生成模型文件
     * @param string $name
     * @return bool
     */
    protected function buildModel(string $name)
    {
        // 生成类名称
        $this->class = $this->getClass($name);
        // 表名称
        $this->table = $name;

        // 保存文件
        $this->saveFilePath = $this->dir . $this->class . '.php';

        // 存在且不覆盖
        if (file_exists($this->saveFilePath) && ! $this->optionForce) {
            return false;
        }

        // 生成前操作
        $this->buildBeforeHandle();

        // 生成操作
        $this->buildHandle();

        // 执行额外命令
        $this->extraCommands();

        return true;
    }

    /**
     * 获取生成内容
     * @return string
     */
    protected function getBuildContent()
    {
        $table = Str::snake($this->table);
        [$annotation, $fillable, $dates, $casts] = $this->getReplaceData($this->dbHelper->getAllColumns($table));

        // 存在按节点替换
        if (file_exists($this->saveFilePath)) {
            $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
            $stmts = $parser->parse(file_get_contents($this->saveFilePath));

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new ModelUpdateVisitor($annotation, $fillable, $dates, $casts, $this->optionModelCastsForce));
            $stmts = $traverser->traverse($stmts);

            return (new Standard())->prettyPrintFile($stmts);
        }

        // 替换
        $content = file_get_contents($this->stubPath);
        $this->replaceNamespace($content, $this->namespace)
            ->replaceClass($content, $this->class)
            ->replaceUses($content, $this->uses)
            ->replaceExtends($content, $this->extends)
            ->replaceAnnotations($content, $annotation)
            ->replaceTable($content, sprintf("'%s'", $table))
            ->replaceFillable($content, $fillable)
            ->replaceDates($content, $dates)
            ->replaceCasts($content, $casts);

        return $content;
    }

    /**
     * 获取替换数据
     * @param array $columns
     * @return array
     */
    protected function getReplaceData(array $columns)
    {
        $annotation = '';
        $fillable = '[';
        $dates = '[';
        $casts = '[';

        foreach ($columns as $column) {
            $_dataType = $this->formatDataType($column['data_type']);

            // 注释
            $annotation .= "\n" . sprintf(' * @property %s $%s', $this->formatPropertyType($_dataType), $column['column_name']);

            // 批量赋值字段
            $fillable .= sprintf("'%s', ", $column['column_name']);

            // 时间字段
            if (! in_array($column['column_name'], ['created_at', 'updated_at']) && in_array($_dataType, ['datetime', 'timestamp', 'time'])) {
                $dates .= sprintf("'%s', ", $column['column_name']);
            }

            // 类型转换
            if ($_dataType == 'json') {
                $casts .= sprintf(
                    "'%s' => '%s', ",
                    $column['column_name'],
                    $_dataType
                );
            }
        }

        return [
            $this->formatAnnotation($annotation),
            rtrim($fillable, ', ') . ']',
            rtrim($dates, ', ') . ']',
            rtrim($casts, ', ') . ']'
        ];
    }

    /**
     * 格式化注解
     * @param string $annotation
     * @return string
     */
    protected function formatAnnotation(string $annotation)
    {
        if (empty($annotation)) {
            return '';
        }

        return "\n/**" . $annotation . "\n * @package " . $this->namespace . "\n */";
    }

    /**
     * 格式化数据类型
     * @param string $type
     * @return string|null
     */
    protected function formatDataType(string $type)
    {
        switch ($type) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                return 'integer';
            case 'decimal':
            case 'float':
            case 'double':
            case 'real':
                return 'float';
            case 'bool':
            case 'boolean':
                return 'boolean';
            case 'datetime':
            case 'timestamp':
            case 'time':
            case 'json':
                return $type;
            default:
                return 'string';
        }
    }

    /**
     * 格式化属性类型
     * @param string $type
     * @return string
     */
    protected function formatPropertyType(string $type)
    {
        switch ($type) {
            case 'integer':
                return 'int';
            case 'datetime':
            case 'timestamp':
            case 'time':
                return '\Carbon\Carbon';
            case 'json':
                return 'array';
            default:
                return $type;
        }
    }

    /**
     * 设置参数、选项
     */
    protected function setArgumentOption()
    {
        parent::setArgumentOption();

        $this->optionConnection = $this->option('connection') ?? 'mysql';
        $this->optionTable = array_map(function ($v) {
            return str_replace($this->prefix, '', $v);
        }, $this->option('table'));

        // 继承中间模型
        if ($this->optionModelExtendsPivot) {
            $this->optionModelExtendsCustom = 'Jmhc\Restful\Models\BasePivot';
        }

        // 引入、继承类
        $this->uses = "\n\nuse " . $this->optionModelExtendsCustom . ';';
        $this->extends = ' extends ' . class_basename($this->optionModelExtendsCustom);
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('controller', null, InputOption::VALUE_NONE, 'Generate the controller file with the same name');
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Generate the validate file with the same name');

        $this->addOption('connection', 'c', InputOption::VALUE_OPTIONAL, 'Specify database links', 'mysql');
        $this->addOption('table', 't', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Exclude table names');
    }
}
