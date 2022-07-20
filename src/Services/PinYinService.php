<?php

namespace Jundayw\PinYin\Services;

use think\Service;
use Jundayw\PinYin\PinYin;

class PinYinService extends Service
{
    public function register()
    {
        $config = $this->app->config->get('pinyin', []);
        $this->app->bind(PinYin::class, new PinYin($config));
    }
}
