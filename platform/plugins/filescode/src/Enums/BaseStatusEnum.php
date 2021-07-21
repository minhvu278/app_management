<?php

namespace Botble\Filescode\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static BaseStatusEnum DRAFT()
 * @method static BaseStatusEnum PUBLISHED()
 * @method static BaseStatusEnum PENDING()
 */
class BaseStatusEnum extends Enum
{
    public const PROCESSING = 'Processing';
    public const PROCESSED = 'Processed';
    public const PROCESSERROR = 'Process Error';

    /**
     * @var string
     */
    public static $langPath = 'core/base::enums.statuses';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::PROCESSING:
                return Html::tag('span', 'Processing', ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::PROCESSED:
                return Html::tag('span', 'Processed', ['class' => 'label-success status-label'])
                    ->toHtml();
            case self::PROCESSERROR:
                return Html::tag('span', 'Error', ['class' => 'label-warning status-label'])
                    ->toHtml();
            default:
                return parent::toHtml();
        }
    }
}
