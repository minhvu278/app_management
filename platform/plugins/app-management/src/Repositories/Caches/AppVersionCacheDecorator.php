<?php

namespace Botble\AppManagement\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\AppManagement\Repositories\Interfaces\AppVersionInterface;

class AppVersionCacheDecorator extends CacheAbstractDecorator implements AppVersionInterface
{
    public function handleStatus($status, $platform, $appId)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function uploadPlist($app_id)    
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
