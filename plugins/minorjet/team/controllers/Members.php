<?php namespace Minorjet\Team\Controllers;

use Flash;
use Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use ApplicationException;
use Minorjet\Team\Models\Member;

class Members extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['minorjet.team.access_members'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Minorjet.Team', 'team', 'members');
    }

    public function index()
    {
        $this->vars['membersTotal'] = Member::count();
        $this->vars['membersActive'] = Member::isActive()->count();
        $this->vars['membersInactive'] = $this->vars['membersTotal'] - $this->vars['membersActive'];

        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        BackendMenu::setContextSideMenu('new_member');

        $this->bodyClass = 'compact-container';
        $this->addJs('/plugins/minorjet/team/assets/js/member-form.js');

        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null)
    {
        $this->bodyClass = 'compact-container';
        $this->addJs('/plugins/minorjet/team/assets/js/member-form.js');

        return $this->asExtension('FormController')->update($recordId);
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $postId) {
                if ((!$post = Member::find($postId)) || !$post->canEdit($this->user))
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
        if (!$record->active)
            return 'safe disabled';
    }

    public function formBeforeCreate($model)
    {
        $model->user_id = $this->user->id;
    }

}