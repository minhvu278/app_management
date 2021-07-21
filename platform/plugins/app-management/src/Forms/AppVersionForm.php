<?php

namespace Botble\AppManagement\Forms;

use Botble\AppManagement\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\AppManagement\Http\Requests\AppVersionRequest;
use Botble\AppManagement\Models\AppVersion;

class AppVersionForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $app = \DB::table('apps')->pluck('name', 'id')->toArray();

        $this
            ->setupModel(new AppVersion)
            ->setValidatorClass(AppVersionRequest::class)
            ->withCustomFields()
            ->add('platform', 'select', [
                'label'      => 'Platform',
                'label_attr' => ['class' => 'control-label required'],
                'choices'    => [
                    'ios' => __('ios'),
                    'android' => __('android'),
                ],
            ])
            ->add('file', 'mediaFile', [
                'label'      => 'File apk or ipa',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                ],
            ])
            ->add('app_id', 'select', [
                'label'      => 'App',
                'label_attr' => ['class' => 'control-label required'],
                'choices'    => $app,
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
