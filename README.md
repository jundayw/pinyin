基于 [CC-CEDICT](http://cc-cedict.org/wiki/) 词典的中文转拼音工具，更准确的支持多音字的汉字转拼音解决方案。

## 安装

使用 Composer 安装:

```
composer require jundayw/pinyin
```

## 可用选项：

|      选项      | 描述                                                |
| -------------  | ---------------------------------------------------|
| `PINYIN_TONE`  | UNICODE 式音调：`měi hǎo`                    |
| `PINYIN_ASCII_TONE`  | 带数字式音调：  `mei3 hao3`                    |
| `PINYIN_NO_TONE`    |  无音调：`mei hao` | 
| `PINYIN_KEEP_NUMBER`    | 保留数字  | 
| `PINYIN_KEEP_ENGLISH`   | 保留英文   | 
| `PINYIN_KEEP_PUNCTUATION`   |  保留标点  | 
| `PINYIN_UMLAUT_V` | 使用 `v` 代替 `yu`, 例如：吕 `lyu` 将会转为 `lv` |

## 使用

可选转换方案：

- 内存型，适用于服务器内存空间较富余，优点：转换快
- 小内存型(默认)，适用于内存比较紧张的环境，优点：占用内存小，转换不如内存型快
- I/O型，适用于虚拟机，内存限制比较严格环境。优点：非常微小内存消耗。缺点：转换慢，不如内存型转换快,php >= 5.5

### 拼音数组

```php
use Jundayw\PinYin\PinYin;

// Laravel 框架以由服务提供者注入配置信息
// 手动载入配置仅为原生 PHP 项目需要
$config = include("../config/pinyin.php");

$pinyin = new PinYin($config);

$pinyin->convert('带着希望去旅行，比到达终点更美好');
// ["dai", "zhe", "xi", "wang", "qu", "lyu", "xing", "bi", "dao", "da", "zhong", "dian", "geng", "mei", "hao"]

$pinyin->convert('带着希望去旅行，比到达终点更美好', PinYin::PINYIN_TONE);
// ["dài","zhe","xī","wàng","qù","lǚ","xíng","bǐ","dào","dá","zhōng","diǎn","gèng","měi","hǎo"]

$pinyin->convert('带着希望去旅行，比到达终点更美好', PinYin::PINYIN_ASCII_TONE);
//["dai4","zhe","xi1","wang4","qu4","lyu3","xing2","bi3","dao4","da2","zhong1","dian3","geng4","mei3","hao3"]
```

- 内存型: 将所有字典预先载入内存（Jundayw\PinYin\Support\MemoryFileDictLoader）
- 小内存型: 将字典分片载入内存（Jundayw\PinYin\Support\FileDictLoader）
- I/O型: 不载入内存，将字典使用文件流打开逐行遍历并运用php5.5生成器(yield)特性分配单行内存（Jundayw\PinYin\Support\GeneratorFileDictLoader）

### 生成用于链接的拼音字符串

```php
permalink(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT  | PinYin::PINYIN_KEEP_NUMBER | PinYin::PINYIN_KEEP_ENGLISH)
```

```php
$pinyin->permalink('带着希望去旅行'); // dai-zhe-xi-wang-qu-lyu-xing
$pinyin->permalink('带着希望去旅行', '.'); // dai.zhe.xi.wang.qu.lyu.xing
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

### 翻译姓名

姓名的姓的读音有些与普通字不一样，比如 ‘单’ 常见的音为 `dan`，而作为姓的时候读 `shan`。

```php
name(string $chinese, int $option = PinYin::PINYIN_NAME)
```

```php
$pinyin->name('单某某'); // ['shan', 'mou', 'mou']
$pinyin->name('单某某', PinYin::PINYIN_TONE); // ["shàn","mǒu","mǒu"]
```

## 配置文件格式

>使用 `tab` 制表符作为分隔符

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

## 鸣谢

项目源于 [overtrue/pinyin](https://github.com/overtrue/pinyin) ，因词库完善及补充不能满足业务时效性需求，此项目才应运而生。
