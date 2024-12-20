# PinYin

Chinese Characters to Pinyin.

## 安装

使用 Composer 安装:

```shell
composer require jundayw/pinyin
```

[![Latest Stable Version](https://poser.pugx.org/jundayw/pinyin/v)](https://packagist.org/packages/jundayw/pinyin)
[![Total Downloads](https://poser.pugx.org/jundayw/pinyin/downloads)](https://packagist.org/packages/jundayw/pinyin)
[![Latest Unstable Version](https://poser.pugx.org/jundayw/pinyin/v/unstable)](https://packagist.org/packages/jundayw/pinyin)
[![License](https://poser.pugx.org/jundayw/pinyin/license)](https://packagist.org/packages/jundayw/pinyin)
[![PHP Version Require](https://poser.pugx.org/jundayw/pinyin/require/php)](https://packagist.org/packages/jundayw/pinyin)

### 原生 `PHP` 中使用：

```php
use Jundayw\PinYin\PinYin;

$config = include("../config/pinyin.php");

$pinyin = new PinYin($config);

$pinyin->name('单某某');
```

### 框架 `Laravel` 中使用：

```php
use Jundayw\PinYin\Facades\Alphabet;
use Jundayw\PinYin\PinYin;

// 门面使用方法
Alphabet::name('单某某', PinYin::PINYIN_NAME);

// 依赖注入
public function test(PinYin $pinyin) {
    return $pinyin->name('单某某');
}
```

发布配置文件

```shell
php artisan vendor:publish --tag=pinyin-config
```

### 框架 `ThinkPHP` 中使用（ThinkPHP6）：

```php
use Jundayw\PinYin\Services\Alphabet;
use Jundayw\PinYin\PinYin;

// 门面使用方法
Alphabet::name('单某某', PinYin::PINYIN_NAME);

// 依赖注入
public function test(PinYin $pinyin) {
    return $pinyin->name('单某某');
}
```

发布配置文件

```shell
php think vendor:publish
```

## 可用选项：

| 选项                        | 描述                                   |
|---------------------------|--------------------------------------|
| `PINYIN_TONE`             | UNICODE 式音调：`měi hǎo`                |
| `PINYIN_ASCII_TONE`       | 带数字式音调：  `mei3 hao3`                 |
| `PINYIN_NO_TONE`          | 无音调：`mei hao`                        | 
| `PINYIN_KEEP_NUMBER`      | 保留数字                                 | 
| `PINYIN_KEEP_ENGLISH`     | 保留英文                                 | 
| `PINYIN_KEEP_PUNCTUATION` | 保留标点                                 | 
| `PINYIN_UMLAUT_V`         | 使用 `v` 代替 `yu`, 例如：吕 `lyu` 将会转为 `lv` |

### 拼音数组

```php
$pinyin->convert('带着希望去旅行，比到达终点更美好');
// ["dai", "zhe", "xi", "wang", "qu", "lyu", "xing", "bi", "dao", "da", "zhong", "dian", "geng", "mei", "hao"]

$pinyin->convert('带着希望去旅行，比到达终点更美好', PinYin::PINYIN_TONE);
// ["dài","zhe","xī","wàng","qù","lǚ","xíng","bǐ","dào","dá","zhōng","diǎn","gèng","měi","hǎo"]

$pinyin->convert('带着希望去旅行，比到达终点更美好', PinYin::PINYIN_ASCII_TONE);
// ["dai4","zhe","xi1","wang4","qu4","lyu3","xing2","bi3","dao4","da2","zhong1","dian3","geng4","mei3","hao3"]
```

### 翻译姓名

姓名的姓的读音有些与普通字不一样，比如 ‘单’ 常见的音为 `dan`，而作为姓的时候读 `shan`。

```php
name(string $chinese, int $option = PinYin::PINYIN_NAME)
```

```php
$pinyin->name('单某某'); // ['shan', 'mou', 'mou']
$pinyin->name('单某某', PinYin::PINYIN_TONE); // ["shàn","mǒu","mǒu"]
```

### 生成用于链接的拼音字符串

```php
kebab(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT  | PinYin::PINYIN_KEEP_NUMBER | PinYin::PINYIN_KEEP_ENGLISH)
```

```php
$pinyin->kebab('带着希望去旅行'); // dai-zhe-xi-wang-qu-lyu-xing
$pinyin->kebab('带着希望去旅行', '.'); // dai.zhe.xi.wang.qu.lyu.xing
```

### 获取首字符字符串

```php
abbr(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT | PinYin::PINYIN_NO_TONE)
```

```php
$pinyin->abbr('带着希望去旅行'); // dzxwqlx
$pinyin->abbr('带着希望去旅行', '-'); // d-z-x-w-q-l-x

$pinyin->abbr('你好2018！', null, PinYin::PINYIN_KEEP_NUMBER); // nh2018
$pinyin->abbr('Happy New Year! 2018！', null, PinYin::PINYIN_KEEP_ENGLISH); // HNY2018
```

### 翻译汉语短语

```php
phrase(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT)
```

```php
$pinyin->phrase('新年快乐，2022 Happy New Year!');
// xin nian kuai le

// 数字、英文及标点符号不推荐使用 phrase 函数方法处理
$pinyin->phrase('新年快乐，2022 Happy New Year!', '-', PinYin::PINYIN_TONE | PinYin::PINYIN_KEEP_PUNCTUATION | PinYin::PINYIN_KEEP_ENGLISH | PinYin::PINYIN_KEEP_NUMBER);
// xīn-nián-kuài-lè,-2022-Happy-New-Year!
```

### 翻译整段文字为拼音

将会保留中文字符：`，。 ！ ？ ： “ ” ‘ ’` 并替换为对应的英文符号。

```php
sentence(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_NO_TONE | PinYin::PINYIN_KEEP_PUNCTUATION | PinYin::PINYIN_KEEP_ENGLISH | PinYin::PINYIN_KEEP_NUMBER)
```

```php
$pinyin->sentence('带着希望去旅行，比到达终点更美好！');
// dai zhe xi wang qu lyu xing, bi dao da zhong dian geng mei hao!

$pinyin->sentence('带着希望去旅行，比到达终点更美好！', null, PinYin::PINYIN_TONE);
// dài zhe xī wàng qù lǚ xíng, bǐ dào dá zhōng diǎn gèng měi hǎo!
```

## 配置文件格式

> 使用 `tab` 制表符作为分隔符

```php
[

    /*
    |--------------------------------------------------------------------------
    | DictLoader Driver
    |--------------------------------------------------------------------------
    |
    | Supported Drivers:
    | \Jundayw\PinYin\Support\FileDictLoader
    | \Jundayw\PinYin\Support\GeneratorFileDictLoader
    | \Jundayw\PinYin\Support\MemoryFileDictLoader
    |
    | Default:
    | \Jundayw\PinYin\Support\FileDictLoader
    |
    */

    'driver' => null,

    /*
    |--------------------------------------------------------------------------
    | 姓氏处理
    |--------------------------------------------------------------------------
    |
    */

    'surnames' => [
        '单' => '	shàn',
    ],

    /*
    |--------------------------------------------------------------------------
    | 单词处理
    |--------------------------------------------------------------------------
    | 注意：优先级高于短语处理
    |
    */

    'chars' => [
        '⺁' => '	fǎn',
    ],

    /*
    |--------------------------------------------------------------------------
    | 短语处理
    |--------------------------------------------------------------------------
    |
    */

    'words' => [
        '单田芳' => '	shàn	tián	fāng',
        '一路同行' => '	yí	lù	tóng	xíng',
    ],
];
```

可选字典驱动类型：

1、Jundayw\PinYin\Support\MemoryFileDictLoader

- 内存型，适用于服务器内存空间较富余
- 优点：转换快
- 将所有字典预先载入内存

2、Jundayw\PinYin\Support\FileDictLoader

- 小内存型（默认），适用于内存比较紧张的环境
- 优点：占用内存小，转换不如内存型快
- 将字典分片载入内存

3、Jundayw\PinYin\Support\GeneratorFileDictLoader

- I/O型，适用于虚拟机，内存限制比较严格环境
- 优点：非常微小内存消耗
- 缺点：转换慢，不如内存型转换快
- 不载入内存
- 将字典使用文件流打开逐行遍历并运用 `php5.5` 生成器 `yield` 特性分配单行内存

## 鸣谢

> 基于 [CC-CEDICT](http://cc-cedict.org/wiki/) 词典的中文转拼音工具，更准确的支持多音字的汉字转拼音解决方案。

> 项目源于 [overtrue/pinyin](https://github.com/overtrue/pinyin) ，因词库完善及补充不能满足业务时效性需求，此项目才应运而生。
