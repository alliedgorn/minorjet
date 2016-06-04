<?php namespace Minorjet\Team\Components;

use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Minorjet\Team\Models\Member as MemberModel;

class Members extends ComponentBase
{
    /**
     * A collection of members to display
     * @var Collection
     */
    public $members;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noMembersMessage;

    /**
     * If the member list should be ordered by another attribute.
     * @var string
     */
    public $sortOrder;

    public function componentDetails()
    {
        return [
            'name'        => 'minorjet.team::lang.settings.members_title',
            'description' => 'minorjet.team::lang.settings.members_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'minorjet.team::lang.settings.members_pagination',
                'description' => 'minorjet.team::lang.settings.members_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'membersPerPage' => [
                'title'             => 'minorjet.team::lang.settings.members_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'minorjet.team::lang.settings.members_per_page_validation',
                'default'           => '10',
            ],
            'noMembersMessage' => [
                'title'        => 'minorjet.team::lang.settings.members_no_members',
                'description'  => 'minorjet.team::lang.settings.members_no_members_description',
                'type'         => 'string',
                'default'      => 'No posts found',
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'minorjet.team::lang.settings.members_order',
                'description' => 'minorjet.team::lang.settings.members_order_description',
                'type'        => 'dropdown',
                'default'     => 'priority desc'
            ]
        ];
    }

    public function getSortOrderOptions()
    {
        return MemberModel::$allowedSortingOptions;
    }

    public function onRun()
    {
        $this->prepareVars();

        $this->members = $this->page['members'] = $this->listMembers();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->posts->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->noMembersMessage = $this->page['noMembersMessage'] = $this->property('noMembersMessage');
    }

    protected function listMembers()
    {
        /*
         * List all the members
         */
        $members = MemberModel::listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'sort'       => $this->property('sortOrder'),
            'perPage'    => $this->property('membersPerPage')
        ]);

        return $members;
    }

}
