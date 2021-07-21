<?php

namespace Botble\AppManagement\Providers;

use Botble\AppManagement\Models\AppManagement;
use Illuminate\Support\ServiceProvider;
use Botble\AppManagement\Repositories\Caches\AppManagementCacheDecorator;
use Botble\AppManagement\Repositories\Eloquent\AppManagementRepository;
use Botble\AppManagement\Repositories\Interfaces\AppManagementInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class AppManagementServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(AppManagementInterface::class, function () {
            return new AppManagementCacheDecorator(new AppManagementRepository(new AppManagement));
        });
        $this->app->bind(\Botble\AppManagement\Repositories\Interfaces\AppInterface::class, function () {
            return new \Botble\AppManagement\Repositories\Caches\AppCacheDecorator(
                new \Botble\AppManagement\Repositories\Eloquent\AppRepository(new \Botble\AppManagement\Models\App)
            );
        });
        $this->app->bind(\Botble\AppManagement\Repositories\Interfaces\AppVersionInterface::class, function () {
            return new \Botble\AppManagement\Repositories\Caches\AppVersionCacheDecorator(
                new \Botble\AppManagement\Repositories\Eloquent\AppVersionRepository(new \Botble\AppManagement\Models\AppVersion)
            );
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/app-management')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([\Botble\AppManagement\Models\AppVersion::class]);
                \Language::registerModule([\Botble\AppManagement\Models\App::class]);
                \Language::registerModule([AppManagement::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-app-management',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/app-management::app-management.name',
                'icon'        => 'fa fa-list',
                'url'         => route('app-management.index'),
                'permissions' => ['app-management.index'],
            ]);
            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-app',
                'priority'    => 0,
                'parent_id'   => 'cms-plugins-app-management',
                'name'        => 'plugins/app-management::app.name',
                'icon'        => null,
                'url'         => route('app.index'),
                'permissions' => ['app.index'],
            ]);
            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-app-version',
                'priority'    => 0,
                'parent_id'   => 'cms-plugins-app-management',
                'name'        => 'plugins/app-management::app-version.name',
                'icon'        => null,
                'url'         => route('app-version.index'),
                'permissions' => ['app-version.index'],
            ]);
        });
    }
}
