<?php namespace Minorjet\Aircraft\Controllers;

use Flash;
use Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use ApplicationException;
use Minorjet\Aircraft\Models\Feature;

class Features extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['minorjet.aircraft.access_other_aircrafts', 'minorjet.aircraft.access_aircrafts'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Minorjet.Aircraft', 'aircraft', 'features');
    }

    public function index()
    {
        $this->vars['postsTotal'] = Feature::count();
        $this->vars['postsPublished'] = Feature::isPublished()->count();
        $this->vars['postsDrafts'] = $this->vars['postsTotal'] - $this->vars['postsPublished'];

        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        BackendMenu::setContextSideMenu('features');

        $this->bodyClass = 'compact-container';
        $this->addCss('/plugins/minorjet/aircraft/assets/css/rainlab.blog-preview.css');
        $this->addJs('/plugins/minorjet/aircraft/assets/js/post-form.js');

        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null)
    {
        $this->bodyClass = 'compact-container';
        $this->addCss('/plugins/minorjet/aircraft/assets/css/rainlab.blog-preview.css');
        $this->addJs('/plugins/minorjet/aircraft/assets/js/post-form.js');

        return $this->asExtension('FormController')->update($recordId);
    }

    public function listExtendQuery($query)
    {
        if (!$this->user->hasAnyAccess(['minorjet.aircraft.access_other_aircrafts'])) {
            $query->where('user_id', $this->user->id);
        }
    }

    public function formExtendQuery($query)
    {
        if (!$this->user->hasAnyAccess(['minorjet.aircraft.access_other_aircrafts'])) {
            $query->where('user_id', $this->user->id);
        }
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $postId) {
                if ((!$post = Feature::find($postId)) || !$post->canEdit($this->user))
                    continue;

                $post->delete();
            }

            Flash::success('Successfully deleted items.');
        }

        return $this->listRefresh();
    }

    /**
     * {@inheritDoc}
     */
    public function listInjectRowClass($record, $definition = null)
    {
        if (!$record->published)
            return 'safe disabled';
    }

    public function formBeforeCreate($model)
    {
        $model->user_id = $this->user->id;
    }

    public function onRefreshPreview()
    {
        $data = post('Feature');

        $previewHtml = Feature::formatHtml($data['content'], true);

        return [
            'preview' => $previewHtml
        ];
    }

}