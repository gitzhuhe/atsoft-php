# AtSoft/PHP

大扎好，我四渣渣辉，介四里没有挽过的船新版本，挤需体验三番钟，里造会干我一样，爱象节款游戏！

***通过各种借鉴，实现了一个简单粗暴的适合自己习惯的框架。***

### 安装

```
composer install at-soft/php
```

### 配置文件 Conf

php.ini 内配置变量区分运行环境，默认 qatest
```
SING_PHP.RUN_MODE = ${mode}   // 'qatest', 'online', 'local', 'pre'
```
默认读取 Conf/qatest内所有的文件为配置

**配置 online 时，全局 OL = true**

### APP结构

App 目录下 分为 module 目录

```
controller // 控制器
entity // 实体
mapper // mapper
service // 服务
router // 路由配置
```

### IOC

控制反转（Inversion of Control，缩写为IoC），是面向对象编程中的一种设计原则，可以用来减低计算机代码之间的耦合度。其中最常见的方式叫做依赖注入（Dependency Injection，简称DI），还有一种方式叫“依赖查找”（Dependency Lookup）。通过控制反转，对象在被创建的时候，由一个调控系统内所有对象的外界实体将其所依赖的对象的引用传递给它。也可以说，依赖被注入到对象中。

### 路由配置

```php
return [
    '/system/login' => [
        'method' => "POST",
        'module' => system\controller\loginController::class,
        'function' => 'doLodin',
        'desc' => '【后台】登录'
    ],
    '/system/logout' => [
        'method' => "POST",
        'module' => system\controller\loginController::class,
        'function' => 'logout',
        'desc' => '【后台】退出登录'
    ],
    '/system/changePWD' => [
        'method' => "POST",
        'module' => system\controller\loginController::class,
        'function' => 'changePWD',
        'desc' => '【后台】修改当前用户密码'
    ]
];
```


### DI

请使用 DI::make()创建。

**使用Di:make()就会自动验证表单。**

**如果不需要验证创建实体对象 使用 Di::entity()**

### AOP

待实现

### 代码生成器 Gen

代码生成器会自动增加 uid,inputtime,updatetime,display四个字段


```
php index.php gen ${table} ${module}
```
执行成功后对应生成APP结构文件

### 接口文档

根据实体文件，路由文件生成接口文档。

```
http://hostname/doc
```

### 实体文件说明（Entity）

很多数据都是由实体文件控制的

#### (1) 表单验证规则

使用Di:make()就会自动验证表单。
如果不需要验证创建实体对象 使用 Di::entity()

必填项可以根据字段判断，默认设置。后续可能会根据字段类型默认增加其他字段。

单行验证 ： 验证类型, 提示信息, 验证时机, 触发方法（Array）（对应到路由的 function 配置）。

在实体内定义验证规则

例：
```php
$rules = [
        'phone' => [Validate::phone, '请填写正确手机号', 3,[]],
        'name' => [Validate::required, '姓名必填', 3,[]],
        'IdCard' => [Validate::identity,'请输入正确身份证号码',3,[]],
    ];

```

规则，支持闭包
```
required                 	必须输入
email                    	邮箱
http                        网址
tel                         固定电话
phone                    	手机
zipCode                     邮政编码
num                      	数字范围 如：num:20,60
range                       长度范围(位数)如 :range:5,20
maxlen                   	最大长度如：maxlen:10
minlen                   	最小长度如：minlen:10
regexp                      正则如：regexp:/^\d{5,20}$/ 
confirm                  	两个字段值比对如：confirm:password2
china                   	验证中文
identity                	身份证
exists					    存在字段时验证失败
```

#### (2) 返回的字段控制

在实体文件内定义方法 getResultField

```php

class Demo extends Entity {
    public function getResultField(){
        return [
            'fetch'=>['id','thumb','children','title'],
        ];
    }
}

```

key是对应到路由的 function字段设置。

值是 可以返回的的字段。

其他未定义的默认全部返回。

#### (3) 定义数据库字段

防止额外数据插入数据库造成sql报错
```php
public $field = ['字段名'];
```


### Mapper

包含一些基础的数据库操作


***WrapperList将列表数据处理成实体***

***Wrapper将单条数据处理成实体***

例子如下：

```php
// 增加自定义的查询
class EsCategoryMapper extends BaseMapper
{
    public function __construct()
    {
        $this->table = "es_category";
    }

    public function getCategoryBtIds($ids)
    {
        $list = DB::select($this->table, '*', [
            'id' => $ids
        ]);
        if ($list) {
            $result = [];
            $list = $this->WrapperList(EsCategoryEntity::class, $list);
            foreach ($list as $value) {
                $result[$value->getId()] = $value;
            }
            return $result;
        }
        return [];
    }

}
```

```php
// 有些字段的数据格式需要处理
class EsGoodsMapper extends BaseMapper
{
    public function __construct()
    {
        $this->table = "es_goods";
    }

    public function Wrapper($class, $data)
    {
        $result = parent::Wrapper($class, $data);
        $result->setSet_promote(json_decode($result->getSet_promote(),true));
        $result->setIntegral_goods(json_decode($result->getIntegral_goods(),true));
        return $result;
    }

    public function WrapperList($class, $data)
    {
        $result =  parent::WrapperList($class, $data);
        foreach ($result as $value){
            $value->setSet_promote(json_decode($value->getSet_promote(),true));
            $value->setIntegral_goods(json_decode($value->getIntegral_goods(),true));
        }
        return $result;
    }

}

```
