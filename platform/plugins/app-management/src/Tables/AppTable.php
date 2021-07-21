<?php

namespace Botble\AppManagement\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\AppManagement\Repositories\Interfaces\AppInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\AppManagement\Models\App;
use Html;

class AppTable extends TableAbstract
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
     * AppTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AppInterface $appRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AppInterface $appRepository)
    {
        $this->repository = $appRepository;
        $this->setOption('id', 'plugins-app-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['app.edit', 'app.destroy'])) {
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
                if (!Auth::user()->hasPermission('app.edit')) {
                    return $item->name;
                }
                return Html::link(route('app.edit', $item->id), $item->name);
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
                return $this->getOperations('app.edit', 'app.destroy', $item);
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
            'apps.id',
            'apps.name',
            'apps.package',
            'apps.manager_id',
            'apps.created_at',
        ];

        $query = $model->select($select);

        if (!Auth::user()->isSuperUser()) {
            $query = $query->where('manager_id', Auth::user()->getKey());
        }

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
                'name'  => 'apps.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name'  => 'apps.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'package' => [
                'name'  => 'apps.package',
                'title' => 'package',
                'class' => 'text-left',
            ],
            'manager_id' => [
                'name'  => 'apps.manager_id',
                'title' => 'manager',
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'apps.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('app.create'), 'app.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, App::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('app.deletes'), 'app.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'apps.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'apps.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'apps.created_at' => [
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
