<?php

namespace Botble\Filescode\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Filescode\Http\Requests\FilescodeRequest;
use Botble\Filescode\Repositories\Interfaces\FilescodeInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Filescode\Tables\FilescodeTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Filescode\Forms\FilescodeForm;
use Botble\Base\Forms\FormBuilder;
use Artisan;

class FilescodeController extends BaseController
{
    /**
     * @var FilescodeInterface
     */
    protected $filescodeRepository;

    /**
     * @param FilescodeInterface $filescodeRepository
     */
    public function __construct(FilescodeInterface $filescodeRepository)
    {
        $this->filescodeRepository = $filescodeRepository;
    }

    /**
     * @param FilescodeTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(FilescodeTable $table)
    {
        page_title()->setTitle(trans('plugins/filescode::filescode.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/filescode::filescode.create'));

        return $formBuilder->create(FilescodeForm::class)->renderForm();
    }

    /**
     * @param FilescodeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(FilescodeRequest $request, BaseHttpResponse $response)
    {   
        set_time_limit(0);
        $input = $request->all();
        $input['path'] = public_path('storage/') . $request->input('file');
        $filescode = $this->filescodeRepository->createOrUpdate($input);
        Artisan::call("d2t:importAppleCode", ['file' => $filescode->id]);
        event(new CreatedContentEvent(FILESCODE_MODULE_SCREEN_NAME, $request, $filescode));

        return $response
            ->setPreviousUrl(route('filescode.index'))
            ->setNextUrl(route('filescode.edit', $filescode->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $filescode = $this->filescodeRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $filescode));

        page_title()->setTitle(trans('plugins/filescode::filescode.edit') . ' "' . $filescode->name . '"');

        return $formBuilder->create(FilescodeForm::class, ['model' => $filescode])->renderForm();
    }

    /**
     * @param int $id
     * @param FilescodeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, FilescodeRequest $request, BaseHttpResponse $response)
    {
        set_time_limit(0);
        
        $filescode = $this->filescodeRepository->findOrFail($id);

        $input = $request->all();
        $input['path'] = public_path('storage/') . $request->input('file');

        $filescode->fill($input);

        $this->filescodeRepository->createOrUpdate($filescode);

        Artisan::call("d2t:importAppleCode", ['file' => $filescode->id]);


        event(new UpdatedContentEvent(FILESCODE_MODULE_SCREEN_NAME, $request, $filescode));

        return $response
            ->setPreviousUrl(route('filescode.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $filescode = $this->filescodeRepository->findOrFail($id);

            $this->filescodeRepository->delete($filescode);

            event(new DeletedContentEvent(FILESCODE_MODULE_SCREEN_NAME, $request, $filescode));

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
            $filescode = $this->filescodeRepository->findOrFail($id);
            $this->filescodeRepository->delete($filescode);
            event(new DeletedContentEvent(FILESCODE_MODULE_SCREEN_NAME, $request, $filescode));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
