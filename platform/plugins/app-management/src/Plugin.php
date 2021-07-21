<?php

namespace Botble\AppManagement;

use Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('app_managements');
        Schema::dropIfExists('apps');
        Schema::dropIfExists('app_versions');
    }
}
