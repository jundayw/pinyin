<?php

namespace Jundayw\PinYin\Services;

use think\Facade;
use Jundayw\PinYin\PinYin;
use Jundayw\PinYin\Traits\Macroable;

/**
 * @method static array convert(string $chinese, int $option = PinYin::PINYIN_DEFAULT)
 * @method static array name(string $chinese, int $option = PinYin::PINYIN_NAME)
 * @method static string kebab(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT)
 * @method static string abbr(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT)
 * @method static string phrase(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT)
 * @method static string sentence(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_NO_TONE)
 *
 * @method static void macro($name, $macro)
 * @method static void mixin($mixin, $replace = true)
 * @method static bool hasMacro($name)
 * @method static void flushMacros()
 *
 * @see PinYin
 * @see Macroable
 */
class Alphabet extends Facade
{
    protected static function getFacadeClass()
    {
        return PinYin::class;
    }
}
