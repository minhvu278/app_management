<?php

use Botble\Widget\AbstractWidget;

class DemoWidget extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $frontendTemplate = 'frontend';

    /**
     * @var string
     */
    protected $backendTemplate = 'backend';

    /**
     * @var string
     */
    protected $widgetDirectory = 'demo';

    /**
     * Demo constructor.
     */
    public function __construct()
    {
        parent::__construct([
            'name'        => __('Demo'),
            'description' => __('Widget description'),
        ]);
    }
}
