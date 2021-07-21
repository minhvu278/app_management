<?php

namespace Botble\AppManagement\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\AppManagement\Http\Requests\AppVersionRequest;
use Botble\AppManagement\Repositories\Interfaces\AppVersionInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\AppManagement\Tables\AppVersionTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\AppManagement\Forms\AppVersionForm;
use Botble\AppManagement\Models\App;
use Botble\AppManagement\Models\AppVersion;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Str;

class AppVersionController extends BaseController
{
    /**
     * @var AppVersionInterface
     */
    protected $appVersionRepository;

    /**
     * @param AppVersionInterface $appVersionRepository
     */
    public function __construct(AppVersionInterface $appVersionRepository)
    {
        $this->appVersionRepository = $appVersionRepository;
    }

    /**
     * @param AppVersionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AppVersionTable $table)
    {
        page_title()->setTitle(trans('plugins/app-management::app-version.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/app-management::app-version.create'));

        return $formBuilder->create(AppVersionForm::class)->renderForm();
    }

    /**
     * @param AppVersionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(AppVersionRequest $request, BaseHttpResponse $response)
    {
        $this->appVersionRepository->handleStatus($request->status, $request->platform, $request->app_id);
        $appVersion = $this->appVersionRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(APP_VERSION_MODULE_SCREEN_NAME, $request, $appVersion));
        $this->appVersionRepository->uploadPlist($request->app_id);
        return $response
            ->setPreviousUrl(route('app-version.index'))
            ->setNextUrl(route('app-version.edit', $appVersion->id))
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
        $appVersion = $this->appVersionRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $appVersion));

        page_title()->setTitle(trans('plugins/app-management::app-version.edit') . ' "' . $appVersion->name . '"');

        return $formBuilder->create(AppVersionForm::class, ['model' => $appVersion])->renderForm();
    }

    /**
     * @param $id
     * @param AppVersionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, AppVersionRequest $request, BaseHttpResponse $response)
    {
        $this->appVersionRepository->handleStatus($request->status, $request->platform, $request->app_id);
        $appVersion = $this->appVersionRepository->findOrFail($id);

        $appVersion->fill($request->input());

        $this->appVersionRepository->createOrUpdate($appVersion);

        event(new UpdatedContentEvent(APP_VERSION_MODULE_SCREEN_NAME, $request, $appVersion));
        $this->uploadPlist($request->app_id);
        return $response
            ->setPreviousUrl(route('app-version.index'))
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
            $appVersion = $this->appVersionRepository->findOrFail($id);

            $this->appVersionRepository->delete($appVersion);

            event(new DeletedContentEvent(APP_VERSION_MODULE_SCREEN_NAME, $request, $appVersion));

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
            $appVersion = $this->appVersionRepository->findOrFail($id);
            $this->appVersionRepository->delete($appVersion);
            event(new DeletedContentEvent(APP_VERSION_MODULE_SCREEN_NAME, $request, $appVersion));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    // public function uploadPlist($app_id){
    //     $test = $this->plist();
    //     $appname = App::where('id', $app_id)->get();
    //     $namepath = Str::of($appname[0]->name)->slug('-');
    //     $filename = public_path('storage/') .  $namepath . '.plist';
    //     $myfile = fopen($filename, "w") or die("Unable to open file!");
    //     $resource = fwrite($myfile, $test);
    //     fclose($myfile);
    //     $data['url'] = isset($resource) ? $filename : '';
    //     return $data; 
    // }

    // public function plist()
    // {
    //     $plist = AppVersion::where('platform','ios')
    //             ->where('status','active')
    //             ->first();
    //     return  \Theme::scope('hello', compact('plist'))->render(); 
    // }
}
