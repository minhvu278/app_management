<?php

namespace Botble\AppManagement\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\AppManagement\Http\Requests\AppRequest;
use Botble\AppManagement\Models\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $users = DB::table('users')->pluck('username', 'id')->toArray();

        $this
            ->setupModel(new App)
            ->setValidatorClass(AppRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->add('package', 'text', [
                'label'      => 'Package',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'package',
                    'data-counter' => 255,
                ],
            ]);

            if (Auth::user()->isSuperUser()) {
                $this->add('manager_id', 'select', [
                    'label'      => 'Manager',
                    'label_attr' => ['class' => 'control-label required'],
                    'choices'    => $users,
                ]);
            }


    }
}
