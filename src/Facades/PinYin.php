<?php

namespace Jundayw\PinYin\Facades;

use Illuminate\Support\Facades\Facade;

class PinYin extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'pinyin';
    }
}
