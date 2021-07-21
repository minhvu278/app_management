<?php

namespace Botble\Filescode\Providers;

use Botble\Filescode\Models\Filescode;
use Illuminate\Support\ServiceProvider;
use Botble\Filescode\Repositories\Caches\FilescodeCacheDecorator;
use Botble\Filescode\Repositories\Eloquent\FilescodeRepository;
use Botble\Filescode\Repositories\Interfaces\FilescodeInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class FilescodeServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(FilescodeInterface::class, function () {
            return new FilescodeCacheDecorator(new FilescodeRepository(new Filescode));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/filescode')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Filescode::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-filescode',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/filescode::filescode.name',
                'icon'        => 'fa fa-list',
                'url'         => route('filescode.index'),
                'permissions' => ['filescode.index'],
            ]);
        });
    }
}
