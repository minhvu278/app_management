<?php

namespace Botble\AppManagement\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\AppManagement\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class AppVersion extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_versions';

    /**
     * @var array
     */
    protected $fillable = [
        'platform',
        'file',
        'status',
        'app_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
