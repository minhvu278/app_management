<?php

namespace Botble\AppManagement\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface AppVersionInterface extends RepositoryInterface
{
    public function handleStatus($status, $platform, $appId);
    public function uploadPlist($app_id);
}
