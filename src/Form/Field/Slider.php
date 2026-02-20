<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Slider extends Field
{
    protected static $css = [
        '/vendor/laravel-admin/AdminLTE/plugins/ion-rangeslider/css/ion.rangeSlider.min.css',
        '/vendor/laravel-admin/AdminLTE/plugins/ion-rangeslider/css/ion.rangeSlider.min.css',
    ];

    protected static $js = [
        '/vendor/laravel-admin/AdminLTE/plugins/ion-rangeslider/js/ion.rangeSlider.min.js',
    ];

    protected $options = [
        'type'     => 'single',
        'prettify' => false,
        'hasGrid'  => true,
    ];

    public function render()
    {
        $option = json_encode($this->options);

        $this->script = "$('{$this->getElementClassSelector()}').ionRangeSlider($option);";

        return parent::render();
    }
}
