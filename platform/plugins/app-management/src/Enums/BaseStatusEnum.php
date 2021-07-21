<?php

namespace Botble\AppManagement\Enums;

use Botble\Base\Supports\Enum;
use Html;

class BaseStatusEnum extends Enum
{
    public const ACTIVE = 'active';
    public const DEACTIVE = 'deactive';

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
            case self::ACTIVE:
                return Html::tag('span', 'active', ['class' => 'label-success status-label'])
                    ->toHtml();
            case self::DEACTIVE:
                return Html::tag('span', 'deactive', ['class' => 'label-warning status-label'])
                    ->toHtml();
            default:
                return parent::toHtml();
        }
    }
}
