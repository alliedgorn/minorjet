<?php namespace Minorjet\Aircraft\Components;

use Db;
use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use System\Models\RequestLog as SysRequestLog;

class RequestLog extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'minorjet.aircraft::lang.settings.requestlog_title',
            'description' => 'minorjet.aircraft::lang.settings.requestlog_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'minorjet.aircraft::lang.settings.category_slug',
                'description' => 'minorjet.aircraft::lang.settings.category_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
        ];
    }

    public function onRun()
    {
        if (!App::runningUnitTests()) {
            SysRequestLog::add(200);
        }
    }

}
