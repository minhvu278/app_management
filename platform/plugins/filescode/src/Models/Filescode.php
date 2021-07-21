<?php

namespace Botble\Filescode\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Filescode\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Filescode extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'filescodes';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'file',
        'path',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
