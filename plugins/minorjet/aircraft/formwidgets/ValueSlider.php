<?php namespace Minorjet\Aircraft\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Backend\Classes\FormField;

class ValueSlider extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name'        => 'Value Slider',
            'description' => 'Renders a value slider.'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function loadAssets()
    {
        $this->addJs('../../../assets/js/jquery-ui.js');
        $this->addCss('../../../assets/css/jquery-ui.min.css');
        $this->addCss('css/minorjet-slider.css', 'core');
        $this->addJs('js/minorjet-slider.js', 'core');
    }

    public function render() {
        $this->prepareVars();
        return $this->makePartial('valueslider');
    }

    public function prepareVars() {

        $this->vars['ratio'] = $this->model->content_ratio ? $this->model->content_ratio : 6;
        $this->vars['min'] = $this->config->min;
        $this->vars['max'] = $this->config->max;
        $this->vars['name'] = $this->formField->getName();
 
    }


}