<?php namespace Minorjet\Aircraft;

use Backend;
use Controller;
use System\Classes\PluginBase;
use Minorjet\Aircraft\Classes\TagProcessor; 
use Minorjet\Aircraft\Models\Category;
use Event;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'minorjet.aircraft::lang.plugin.name',
            'description' => 'minorjet.aircraft::lang.plugin.description',
            'author'      => 'Wutthikorn Kongprasopkij, Nuttapong Surasathien',
            'icon'        => 'icon-plane',
            'homepage'    => 'https://bearpowr.com'
        ];
    }

    public function registerComponents()
    {
        return [
            'Minorjet\Aircraft\Components\Aircraft'       => 'aircraft',
            'Minorjet\Aircraft\Components\AircraftWithFeatures'       => 'aircraftWithFeatures',
            'Minorjet\Aircraft\Components\Aircrafts'      => 'aircraftList',
            'Minorjet\Aircraft\Components\Categories' => 'aircraftCategories'
        ];
    }

    public function registerPermissions()
    {
        return [
            'minorjet.aircraft.access_aircrafts'        => ['tab' => 'minorjet.aircraft::lang.blog.tab', 'label' => 'minorjet.aircraft::lang.blog.access_aircrafts'],
            'minorjet.aircraft.access_categories'       => ['tab' => 'minorjet.aircraft::lang.blog.tab', 'label' => 'minorjet.aircraft::lang.blog.access_categories'],
            'minorjet.aircraft.access_other_aircrafts'  => ['tab' => 'minorjet.aircraft::lang.blog.tab', 'label' => 'minorjet.aircraft::lang.blog.access_other_aircrafts'],
            'minorjet.aircraft.access_import_export'    => ['tab' => 'minorjet.aircraft::lang.blog.tab', 'label' => 'minorjet.aircraft::lang.blog.access_import_export']
        ];
    }

    public function registerNavigation()
    {
        return [
            'aircraft' => [
                'label'       => 'minorjet.aircraft::lang.blog.menu_label',
                'url'         => Backend::url('minorjet/aircraft/aircrafts'),
                'icon'        => 'icon-plane',
                'permissions' => ['minorjet.aircraft.*'],
                'order'       => 500,

                'sideMenu' => [
                    'new_aircraft' => [
                        'label'       => 'minorjet.aircraft::lang.aircrafts.new_aircraft',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('minorjet/aircraft/aircrafts/create'),
                        'permissions' => ['minorjet.aircraft.access_aircrafts']
                    ],
                    'aircrafts' => [
                        'label'       => 'minorjet.aircraft::lang.blog.aircrafts',
                        'icon'        => 'icon-plane',
                        'url'         => Backend::url('minorjet/aircraft/aircrafts'),
                        'permissions' => ['minorjet.aircraft.access_aircrafts']
                    ],
                    'categories' => [
                        'label'       => 'minorjet.aircraft::lang.blog.categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('minorjet/aircraft/categories'),
                        'permissions' => ['minorjet.aircraft.access_categories']
                    ],
                    'features' => [
                        'label'       => 'minorjet.aircraft::lang.blog.features',
                        'icon'        => 'icon-cubes',
                        'url'         => Backend::url('minorjet/aircraft/features'),
                        'permissions' => ['minorjet.aircraft.access_aircrafts']
                    ]
                ]
            ]
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'Minorjet\Aircraft\FormWidgets\Preview' => [
                'label' => 'Preview',
                'code'  => 'preview'
            ]
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        /*
         * Register the image tag processing callback
         */
        TagProcessor::instance()->registerCallback(function($input, $preview){
            if (!$preview) return $input;

            return preg_replace('|\<img src="image" alt="([0-9]+)"([^>]*)\/>|m',
                '<span class="image-placeholder" data-index="$1">
                    <span class="upload-dropzone">
                        <span class="label">Click or drop an image...</span>
                        <span class="indicator"></span>
                    </span>
                </span>',
            $input);
        });
    }

    public function boot()
    {
        /*
         * Register menu items for the RainLab.Pages plugin
         */
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'aircraft-category' => 'Aircraft Category',
                'all-aircraft-categories' => 'All Aircraft Categories'
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'aircraft-category' || $type == 'all-aircraft-categories')
                return Category::getMenuTypeInfo($type);
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'aircraft-category' || $type == 'all-aircraft-categories')
                return Category::resolveMenuItem($item, $url, $theme);
        });
    }
}
