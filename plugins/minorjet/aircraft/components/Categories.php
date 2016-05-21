<?php namespace Minorjet\Aircraft\Components;

use Db;
use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Minorjet\Aircraft\Models\Category as CategoryModel;

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
            'displayEmpty' => [
                'title'       => 'minorjet.aircraft::lang.settings.category_display_empty',
                'description' => 'minorjet.aircraft::lang.settings.category_display_empty_description',
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
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->currentCategorySlug = $this->page['currentCategorySlug'] = $this->property('slug');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    protected function loadCategories()
    {
        $categories = CategoryModel::orderBy('name');
        if (!$this->property('displayEmpty')) {
            $categories->whereExists(function($query) {
                $query->select(Db::raw(1))
                ->from('minorjet_aircraft_aircrafts_categories')
                ->join('minorjet_aircraft_aircrafts', 'minorjet_aircraft_aircrafts.id', '=', 'minorjet_aircraft_aircrafts_categories.aircraft_id')
                ->whereNotNull('minorjet_aircraft_aircrafts.published')
                ->where('minorjet_aircraft_aircrafts.published', '=', 1)
                ->whereRaw('minorjet_aircraft_categories.id = minorjet_aircraft_aircrafts_categories.category_id');
            });
        }

        $categories = $categories->getNested();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        return $this->linkCategories($categories);
    }

    protected function linkCategories($categories)
    {
        return $categories->each(function($category) {
            $category->setUrl($this->categoryPage, $this->controller);

            if ($category->children) {
                $this->linkCategories($category->children);
            }
        });
    }
}
