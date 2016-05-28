<?php namespace Minorjet\Aircraft\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Minorjet\Aircraft\Models\Aircraft as AircraftModel;

class AircraftCarousel extends ComponentBase
{
    /**
     * @var RainLab\Blog\Models\Post The post model used for display.
     */
    public $post;

    /**
     * @var string Reference to the page name for linking to categories.
     */

    public function componentDetails()
    {
        return [
            'name'        => 'minorjet.aircraft::lang.settings.carousel',
            'description' => 'minorjet.aircraft::lang.settings.carousel_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'minorjet.aircraft::lang.settings.post_slug',
                'description' => 'minorjet.aircraft::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ]
        ];
    }


    public function onRun()
    {
        $this->post = $this->page['post'] = $this->loadPost();
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');
        $post = AircraftModel::isPublished()->where('slug', $slug)->first();

        return $post;
    }
}
