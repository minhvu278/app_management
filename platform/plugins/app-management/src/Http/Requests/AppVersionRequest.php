<?php

namespace Botble\AppManagement\Http\Requests;

use Botble\AppManagement\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AppVersionRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'platform'   => 'required',
            'file' => 'required',
            'app_id' => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
