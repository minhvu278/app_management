<?php

namespace Botble\AppManagement\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\AppManagement\Http\Requests\AppManagementRequest;
use Botble\AppManagement\Repositories\Interfaces\AppManagementInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\AppManagement\Tables\AppManagementTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\AppManagement\Forms\AppManagementForm;
use Botble\Base\Forms\FormBuilder;

class AppManagementController extends BaseController
{
    /**
     * @var AppManagementInterface
     */
    protected $appManagementRepository;

    /**
     * @param AppManagementInterface $appManagementRepository
     */
    public function __construct(AppManagementInterface $appManagementRepository)
    {
        $this->appManagementRepository = $appManagementRepository;
    }

    /**
     * @param AppManagementTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AppManagementTable $table)
    {
        page_title()->setTitle(trans('plugins/app-management::app-management.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/app-management::app-management.create'));

        return $formBuilder->create(AppManagementForm::class)->renderForm();
    }

    /**
     * @param AppManagementRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(AppManagementRequest $request, BaseHttpResponse $response)
    {
        $appManagement = $this->appManagementRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(APP_MANAGEMENT_MODULE_SCREEN_NAME, $request, $appManagement));

        return $response
            ->setPreviousUrl(route('app-management.index'))
            ->setNextUrl(route('app-management.edit', $appManagement->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $appManagement = $this->appManagementRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $appManagement));

        page_title()->setTitle(trans('plugins/app-management::app-management.edit') . ' "' . $appManagement->name . '"');

        return $formBuilder->create(AppManagementForm::class, ['model' => $appManagement])->renderForm();
    }

    /**
     * @param $id
     * @param AppManagementRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, AppManagementRequest $request, BaseHttpResponse $response)
    {
        $appManagement = $this->appManagementRepository->findOrFail($id);

        $appManagement->fill($request->input());

        $this->appManagementRepository->createOrUpdate($appManagement);

        event(new UpdatedContentEvent(APP_MANAGEMENT_MODULE_SCREEN_NAME, $request, $appManagement));

        return $response
            ->setPreviousUrl(route('app-management.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $appManagement = $this->appManagementRepository->findOrFail($id);

            $this->appManagementRepository->delete($appManagement);

            event(new DeletedContentEvent(APP_MANAGEMENT_MODULE_SCREEN_NAME, $request, $appManagement));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $appManagement = $this->appManagementRepository->findOrFail($id);
            $this->appManagementRepository->delete($appManagement);
            event(new DeletedContentEvent(APP_MANAGEMENT_MODULE_SCREEN_NAME, $request, $appManagement));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
