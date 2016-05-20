<?php namespace Minorjet\Aircraft;

use Backend;
use Controller;
use System\Classes\PluginBase;
use RainLab\Blog\Classes\TagProcessor;
use Minorjet\Blog\Models\Category;
use Event;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'minorjet.aircraft::lang.plugin.name',
            'description' => 'rainlab.blog::lang.plugin.description',
            'author'      => 'Wutthikorn Kongprasopkij',
            'icon'        => 'icon-plane',
            'homepage'    => 'https://bearpowr.com'
        ];
    }

    public function registerComponents()
    {
        return [
            'RainLab\Blog\Components\Post'       => 'blogPost', 
            'RainLab\Blog\Components\Posts'      => 'blogPosts',
            'RainLab\Blog\Components\Categories' => 'blogCategories'
        ]; 
    } 

    public function registerPermissions()
    { 
        return [
            'minorjet.aircraft.access_contents'      => ['tab' => 'minorjet.aircraft::lang.aircraft.tab', 'label' => 'minorjet.aircraft::lang.aircraft.access_contents'],
            'minorjet.aircraft.access_categories'    => ['tab' => 'minorjet.aircraft::lang.aircraft.tab', 'label' => 'minorjet.aircraft::lang.aircraft.access_categories'],
            'minorjet.aircraft.access_other_posts'   => ['tab' => 'minorjet.aircraft::lang.aircraft.tab', 'label' => 'minorjet.aircraft::lang.aircraft.access_other_posts'],
            'minorjet.aircraft.access_import_export' => ['tab' => 'minorjet.aircraft::lang.aircraft.tab', 'label' => 'minorjet.aircraft::lang.aircraft.access_import_export']
        ];
    }

    public function registerNavigation()
    { 
        return [
            'aircraft' => [
                'label'       => 'minorjet.aircraft::lang.aircraft.menu_label',
                'url'         => Backend::url('minorjet/aircraft/contents'),
                'icon'        => 'icon-plane',
                'permissions' => ['minorjet.aircraft.*'],
                'order'       => 500,

                'sideMenu' => [
                    'new_content' => [
                        'label'       => 'minorjet.aircraft::lang.contents.new_content',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('minorjet/aircraft/contents/create'),
                        'permissions' => ['minorjet.aircraft.access_contents']
                    ],
                    'contents' => [
                        'label'       => 'minorjet.aircraft::lang.aircraft.contents',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('minorjet/aircraft/contents'),
                        'permissions' => ['minorjet.aircraft.access_contents']
                    ],
                    'categories' => [
                        'label'       => 'minorjet.aircraft::lang.aircraft.categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('minorjet/aircraft/categories'),
                        'permissions' => ['minorjet.aircraft.access_categories']
                    ]
                ]
            ]
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'RainLab\Blog\FormWidgets\Preview' => [
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
