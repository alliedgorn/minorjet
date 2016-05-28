<?php namespace Minorjet\Aircraft\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Minorjet\Aircraft\Models\Aircraft as AircraftModel;

class AircraftWithFeatures extends ComponentBase
{
    /**
     * @var RainLab\Blog\Models\Post The post model used for display.
     */
    public $post;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    public function componentDetails()
    {
        return [
            'name'        => 'minorjet.aircraft::lang.settings.aircraft_with_features_title',
            'description' => 'minorjet.aircraft::lang.settings.aircraft_with_features_description'
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
            ],
            'categoryPage' => [
                'title'       => 'minorjet.aircraft::lang.settings.post_category',
                'description' => 'minorjet.aircraft::lang.settings.post_category_description',
                'type'        => 'dropdown',
                'default'     => 'aircraft/category',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->post = $this->page['post'] = $this->loadPost();
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');
        $post = AircraftModel::isPublished()->where('slug', $slug)->first();
        if ( $post ) {
            $post['features'] = $post->features()->isPublished()->get();
        }

        /*
         * Add a "url" helper attribute for linking to each category
         */
        if ($post && $post->categories->count()) {
            $post->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }

        return $post;
    }
}
