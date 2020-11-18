## 目录

- [安装配置](#%E5%AE%89%E8%A3%85%E9%85%8D%E7%BD%AE)
- [使用说明](#%E4%BD%BF%E7%94%A8%E8%AF%B4%E6%98%8E)
    - [创建控制器](#%E5%88%9B%E5%BB%BA%E6%8E%A7%E5%88%B6%E5%99%A8)
    - [创建模型](#%E5%88%9B%E5%BB%BA%E6%A8%A1%E5%9E%8B)
    - [创建服务层(逻辑层)](#%E5%88%9B%E5%BB%BA%E6%9C%8D%E5%8A%A1%E5%B1%82%E9%80%BB%E8%BE%91%E5%B1%82)
    - [创建验证器](#%E5%88%9B%E5%BB%BA%E9%AA%8C%E8%AF%81%E5%99%A8)
    - [通过文件创建所需文件](#%E9%80%9A%E8%BF%87%E6%96%87%E4%BB%B6%E5%88%9B%E5%BB%BA%E6%89%80%E9%9C%80%E6%96%87%E4%BB%B6)
    - [生成工厂文件](#%E7%94%9F%E6%88%90%E5%B7%A5%E5%8E%82%E6%96%87%E4%BB%B6)

## 使用说明

> 安装后可直接配置环境变量使用
> 
> 环境变量值参考：[env](docs/ENV.md)

使用以下命令安装：
```
composer require jmhc/laravel-console
```
发布文件[可选]：
```php
php artisan vendor:publish --tag=jmhc-console
```

### 创建控制器

> 创建的控制器默认继承基础控制器 BaseController
>
> `--controller-extends-custom` 参数修改继承基础控制器

```php
// 创建 Test 控制器位于 app/Http/Controllers/Test.php
php artisan jmhc-api:make-controller test
// 创建 Test 控制器修改继承父类
php artisan jmhc-api:make-controller test --controller-extends-custom App/BaseController
// 创建 Test 控制器并添加后缀，位于 app/Http/Controllers/TestController.php
php artisan jmhc-api:make-controller test -s
...
```

### 创建模型

> 不传 name 将会从数据库读取所有表创建
>
> 覆盖创建模型时使用抽象语法树保证模型代码不丢失
>
> `--model-extends-custom` 参数修改继承基础模型

```php
// 创建公用模型位于 app/Common/Models 并排除 test，foos 表
php artisan jmhc-api:make-model --dir Common/Models -t test -t foos
// 创建 Test 模型位于 app/Http/Models/Test.php
php artisan jmhc-api:make-model test
// 创建 Test 模型修改继承父类
php artisan jmhc-api:make-model test --model-extends-custom App\BaseModel
// 创建 Test 模型并添加后缀，位于 app/Http/Models/TestModel.php
php artisan jmhc-api:make-model test -s
...
```

### 创建服务层(逻辑层)

> 创建的服务默认继承基础服务 BaseService
>
> `--service-extends-custom` 参数修改继承基础服务

```php
// 创建 Test 服务位于 app/Http/Services/Test.php
php artisan jmhc-api:make-service test
// 创建 Test 服务修改继承父类
php artisan jmhc-api:make-service test --service-extends-custom App\BaseService
// 创建 Test 服务并添加后缀，位于 app/Http/Services/TestService.php
php artisan jmhc-api:make-service test -s
...
```

### 创建验证器

> 创建的验证器默认继承基础验证器 BaseValidate
>
> `--validate-extends-custom` 参数修改继承基础验证器

```php
// 创建 Test 验证器位于 app/Http/Validates/Test.php
php artisan jmhc-api:make-validate test
// 创建 Test 验证器修改继承父类
php artisan jmhc-api:make-validate test --validate-extends-custom App/BaseValidate
// 创建 Test 验证器并添加后缀，位于 app/Http/Validates/TestValidate.php
php artisan jmhc-api:make-validate test -s
...
```

### 通过文件创建所需文件

> 此命令通过 `config('jmhc-console.tables')` 获取需要创建的文件名称
>
> 使用 `*-extends-custom` 修改对应继承父类

```php
// 生成控制器、模型、服务、验证器、迁移、填充
php artisan jmhc-api:make-with-file --controller --model --service --validate --migration --seeder
// 覆盖生成所有文件
php artisan jmhc-api:make-with-file -f
// 覆盖生成控制器
php artisan jmhc-api:make-with-file --force-controller
...
```

### 生成工厂文件

```php
// 通过指定目录创建factory,位于 app/Http/Common/Factory/Service.php
php artisan jmhc-api:make-factory service --scan-dir Http/Services --scan-dir Http/Index/Services

// 通过指定目录创建factory,并增加后缀、保存至其他路径,位于 app/Http/Commons/Factory/ServiceFactory.php
php artisan jmhc-api:make-factory service --scan-dir Http/Services --dir Commons/Factory -s
...
```
