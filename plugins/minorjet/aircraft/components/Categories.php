<?php namespace Minorjet\Aircraft\Components;

use Db;
use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Minorjet\Aircraft\Models\Category as CategoryModel;
use Minorjet\Aircraft\Models\Aircraft as AircraftModel;

class Categories extends ComponentBase
{
    /**
     * @var Collection A collection of categories to display
     */
    public $categories;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    /**
     * @var string Reference to the page name for linking to posts.
     */
    public $postPage;

    /**
     * @var string Reference to the current category slug.
     */
    public $currentCategorySlug;

    public function componentDetails()
    {
        return [
            'name'        => 'minorjet.aircraft::lang.settings.category_title',
            'description' => 'minorjet.aircraft::lang.settings.category_description'
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
            'categoryFilter' => [
                'title'       => 'minorjet.aircraft::lang.settings.posts_filter',
                'description' => 'minorjet.aircraft::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => ''
            ],
            'displayEmpty' => [
                'title'       => 'minorjet.aircraft::lang.settings.category_display_empty',
                'description' => 'minorjet.aircraft::lang.settings.category_display_empty_description',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'getRoot' => [
                'title'       => 'minorjet.aircraft::lang.settings.category_get_root',
                'description' => 'minorjet.aircraft::lang.settings.category_get_root_description',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'withAircrafts' => [
                'title'       => 'minorjet.aircraft::lang.settings.category_with_aircrafts',
                'description' => 'minorjet.aircraft::lang.settings.category_with_aircrafts_description',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'categoryPage' => [
                'title'       => 'minorjet.aircraft::lang.settings.category_page',
                'description' => 'minorjet.aircraft::lang.settings.category_page_description',
                'type'        => 'dropdown',
                'default'     => 'aircraft/category',
                'group'       => 'Links',
            ],
            'postPage' => [
                'title'       => 'minorjet.aircraft::lang.settings.posts_post',
                'description' => 'minorjet.aircraft::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'aircraft',
                'group'       => 'Links',
            ]
        ];
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->currentCategorySlug = $this->page['currentCategorySlug'] = $this->property('slug');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    protected function loadCategories()
    {   
        if ( !$this->property('categoryFilter') ) { return false; }

        $categories = CategoryModel::whereSlug($this->property('categoryFilter'));
        
        if (!$this->property('displayEmpty')) {
            $categories->whereExists(function($query) {
                $query->select(Db::raw(1))
                ->from('minorjet_aircraft_aircrafts_categories')
                ->join('minorjet_aircraft_aircrafts', 'minorjet_aircraft_aircrafts.id', '=', 'minorjet_aircraft_aircrafts_categories.aircraft_id')
                ->whereNotNull('minorjet_aircraft_aircrafts.published')
                ->where('minorjet_aircraft_aircrafts.published', '=', 1)
                ->whereRaw('minorjet_aircraft_categories.id = minorjet_aircraft_aircrafts_categories.category_id');
            });

            $categories->orWhere(function($query){
                $query->whereExists(function($query) {
                    $query->select(Db::raw(1))
                    ->from('minorjet_aircraft_categories as c')
                    ->whereRaw('c.nest_left > minorjet_aircraft_categories.nest_left')
                    ->whereRaw('c.nest_right < minorjet_aircraft_categories.nest_right');
                });
            });
        }

        $categories = $categories->first();
        
        if ( !$categories ) { return false; }

        if ( $this->property('getRoot') ) {
            $categories = $categories->getEagerRoot();
        }else {
            $categories = $categories->getChildren();
        }

        if ( $this->property('withAircrafts') ) {
            $categories = $this->getAircrafts($categories);
        }
        /*
         * Add a "url" helper attribute for linking to each category
         */
        return $this->linkCategories($categories);
    } 

    protected function getAircrafts($categories)
    {
        return $categories->each(function($category) {
            $category['aircraftList'] = AircraftModel::with('categories')->listFrontEnd([
                'categories'   => [$category->id],
                'sort'         => 'priority asc'
            ]);
            if ($category->children) {
                $this->getAircrafts($category->children);
            }
        });
    }

    protected function linkCategories($categories)
    {
        return $categories->each(function($category) {
            $category->setUrl($this->categoryPage, $this->controller);
            if ($category['aircraftList']) {
                $category['aircraftList']->each(function($aircraft) {
                    $aircraft->setUrl($this->postPage, $this->controller);
                });
            }
            if ($category->children) {
                $this->linkCategories($category->children);
            }
        });
    }
}
