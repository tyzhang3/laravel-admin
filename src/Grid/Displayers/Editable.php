<?php

namespace Encore\Admin\Grid\Displayers;

use Encore\Admin\Admin;
use Illuminate\Support\Arr;

class Editable extends AbstractDisplayer
{
    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Type of editable.
     *
     * @var string
     */
    protected $type = '';

    /**
     * Options of editable function.
     *
     * @var array
     */
    protected $options = [
        'emptytext'  => '<i class="fas fa-pen"></i>',
    ];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Add options for editable.
     *
     * @param array $options
     */
    public function addOptions($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Add attributes for editable.
     *
     * @param array $attributes
     */
    public function addAttributes($attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    /**
     * Text type editable.
     */
    public function text()
    {
    }

    /**
     * Textarea type editable.
     */
    public function textarea()
    {
    }

    /**
     * Select type editable.
     *
     * @param array|\Closure $options
     */
    public function select($options = [])
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $source = [];

        foreach ($options as $value => $text) {
            $source[] = compact('value', 'text');
        }

        $this->addOptions(compact('source'));
    }

    /**
     * Date type editable.
     */
    public function date()
    {
        $this->combodate();
    }

    /**
     * Datetime type editable.
     */
    public function datetime()
    {
        $this->combodate('YYYY-MM-DD HH:mm:ss');
    }

    /**
     * Year type editable.
     */
    public function year()
    {
        $this->combodate('YYYY');
    }

    /**
     * Month type editable.
     */
    public function month()
    {
        $this->combodate('MM');
    }

    /**
     * Day type editable.
     */
    public function day()
    {
        $this->combodate('DD');
    }

    /**
     * Time type editable.
     */
    public function time()
    {
        $this->combodate('HH:mm:ss');
    }

    /**
     * Combodate type editable.
     *
     * @param string $format
     */
    public function combodate($format = 'YYYY-MM-DD')
    {
        $this->type = 'combodate';

        $this->addOptions([
            'format'     => $format,
            'viewformat' => $format,
            'template'   => $format,
            'combodate'  => [
                'maxYear' => 2035,
            ],
        ]);
    }

    /**
     * @param array $arguments
     */
    protected function buildEditableOptions(array $arguments = [])
    {
        $this->type = Arr::get($arguments, 0, 'text');

        call_user_func_array([$this, $this->type], array_slice($arguments, 1));
    }

    /**
     * @return string
     */
    public function display()
    {
        $this->buildEditableOptions(func_get_args());

        $data = [
            'key'      => $this->getKey(),
            'value'    => $this->getValue(),
            'display'  => $this->getValue(),
            'name'     => $this->getPayloadName(),
            'resource' => $this->getResource(),
            'trigger'  => "ie-trigger-{$this->getClassName()}",
            'target'   => "ie-template-{$this->getClassName()}",
        ];

        switch ($this->type) {
            case 'textarea':
                return Admin::component('admin::grid.inline-edit.textarea', $data + [
                    'rows' => (int) Arr::get($this->options, 'rows', 5),
                ]);
            case 'select':
                $options = $this->resolveSelectOptions();
                return Admin::component('admin::grid.inline-edit.select', $data + [
                    'display' => Arr::get($options, (string) $this->getValue(), ''),
                    'options' => $options,
                ]);
            case 'date':
                return Admin::component('admin::grid.inline-edit.datetime', $data + [
                    'format' => 'YYYY-MM-DD',
                    'locale' => config('app.locale'),
                ]);
            case 'datetime':
                return Admin::component('admin::grid.inline-edit.datetime', $data + [
                    'format' => 'YYYY-MM-DD HH:mm:ss',
                    'locale' => config('app.locale'),
                ]);
            case 'year':
                return Admin::component('admin::grid.inline-edit.datetime', $data + [
                    'format' => 'YYYY',
                    'locale' => config('app.locale'),
                ]);
            case 'month':
                return Admin::component('admin::grid.inline-edit.datetime', $data + [
                    'format' => 'MM',
                    'locale' => config('app.locale'),
                ]);
            case 'day':
                return Admin::component('admin::grid.inline-edit.datetime', $data + [
                    'format' => 'DD',
                    'locale' => config('app.locale'),
                ]);
            case 'time':
                return Admin::component('admin::grid.inline-edit.datetime', $data + [
                    'format' => 'HH:mm:ss',
                    'locale' => config('app.locale'),
                ]);
            case 'combodate':
                return Admin::component('admin::grid.inline-edit.datetime', $data + [
                    'format' => Arr::get($this->options, 'format', 'YYYY-MM-DD'),
                    'locale' => config('app.locale'),
                ]);
            case 'text':
            default:
                return Admin::component('admin::grid.inline-edit.input', $data + [
                    'mask' => [],
                ]);
        }
    }

    protected function resolveSelectOptions(): array
    {
        $resolved = [];
        $source = Arr::get($this->options, 'source', []);

        foreach ($source as $option) {
            if (isset($option['value'])) {
                $resolved[(string) $option['value']] = (string) Arr::get($option, 'text', '');
            }
        }

        return $resolved;
    }
}
