<?php

namespace Botble\AppManagement\Tables;

use Auth;
use BaseHelper;
use Botble\AppManagement\Repositories\Interfaces\AppVersionInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\AppManagement\Models\AppVersion;
use Botble\AppManagement\Enums\BaseStatusEnum;
use Html;

class AppVersionTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * AppVersionTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AppVersionInterface $appVersionRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AppVersionInterface $appVersionRepository)
    {
        $this->repository = $appVersionRepository;
        $this->setOption('id', 'plugins-app-version-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['app-version.edit', 'app-version.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('app-version.edit')) {
                    return $item->platform;
                }
                return Html::link(route('app-version.edit', $item->id), $item->platform);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('app-version.edit', 'app-version.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'app_versions.id',
            'app_versions.platform',
            'app_versions.created_at',
            'app_versions.file',
            'app_versions.status',
            'app_versions.app_id',
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
                'name'  => 'app_versions.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'platform' => [
                'name'  => 'app_versions.platform',
                'title' => 'Platform',
                'class' => 'text-left',
            ],
            'file' => [
                'name'  => 'app_versions.file',
                'title' => 'File',
                'class' => 'text-left',
            ],
            'app_id' => [
                'name'  => 'app_versions.app_id',
                'title' => 'App',
                'width' => '20px',
            ],
            'created_at' => [
                'name'  => 'app_versions.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'app_versions.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('app-version.create'), 'app-version.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, AppVersion::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('app-version.deletes'), 'app-version.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'app_versions.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'app_versions.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'app_versions.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }
}
