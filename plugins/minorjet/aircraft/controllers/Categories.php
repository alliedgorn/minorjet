<?php namespace Minorjet\Aircraft\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Minorjet\Aircraft\Models\Category;
use Flash;

class Categories extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ReorderController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = ['minorjet.aircraft.access_categories'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Minorjet.Aircraft', 'aircraft', 'categories');
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $categoryId) {
                if ((!$category = Category::find($categoryId)))
                    continue;

                $category->delete();
            }

            Flash::success('Successfully deleted those categories.');
        }

        return $this->listRefresh();
    }
}