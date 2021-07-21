<?php

namespace Botble\AppManagement\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\AppManagement\Http\Requests\AppRequest;
use Botble\AppManagement\Repositories\Interfaces\AppInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\AppManagement\Tables\AppTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\AppManagement\Forms\AppForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;

class AppController extends BaseController
{
    /**
     * @var AppInterface
     */
    protected $appRepository;

    /**
     * @param AppInterface $appRepository
     */
    public function __construct(AppInterface $appRepository)
    {
        $this->appRepository = $appRepository;
    }

    /**
     * @param AppTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AppTable $table)
    {
        page_title()->setTitle(trans('plugins/app-management::app.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/app-management::app.create'));

        return $formBuilder->create(AppForm::class)->renderForm();
    }

    /**
     * @param AppRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(AppRequest $request, BaseHttpResponse $response)
    {
        $data = $request->input();
        if (!array_key_exists("manager_id", $data)) {
            $data["manager_id"] = Auth::user()->getKey();
        }

        $app = $this->appRepository->createOrUpdate($data);

        event(new CreatedContentEvent(APP_MODULE_SCREEN_NAME, $request, $app));

        return $response
            ->setPreviousUrl(route('app.index'))
            ->setNextUrl(route('app.edit', $app->id))
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
        $app = $this->appRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $app));

        page_title()->setTitle(trans('plugins/app-management::app.edit') . ' "' . $app->name . '"');

        return $formBuilder->create(AppForm::class, ['model' => $app])->renderForm();
    }

    /**
     * @param $id
     * @param AppRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, AppRequest $request, BaseHttpResponse $response)
    {
        $app = $this->appRepository->findOrFail($id);

        $app->fill($request->input());

        $this->appRepository->createOrUpdate($app);

        event(new UpdatedContentEvent(APP_MODULE_SCREEN_NAME, $request, $app));

        return $response
            ->setPreviousUrl(route('app.index'))
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
            $app = $this->appRepository->findOrFail($id);

            $this->appRepository->delete($app);

            event(new DeletedContentEvent(APP_MODULE_SCREEN_NAME, $request, $app));

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
            $app = $this->appRepository->findOrFail($id);
            $this->appRepository->delete($app);
            event(new DeletedContentEvent(APP_MODULE_SCREEN_NAME, $request, $app));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
