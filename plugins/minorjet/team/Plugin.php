<?php namespace Minorjet\Team;

use Backend;
use Controller;
use System\Classes\PluginBase;
use Minorjet\Team\Classes\TagProcessor; 
use Minorjet\Team\Models\Member;
use Event;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'minorjet.team::lang.plugin.name',
            'description' => 'minorjet.team::lang.plugin.description',
            'author'      => 'Wutthikorn Kongprasopkij, Nuttapong Surasathien',
            'icon'        => 'icon-users',
            'homepage'    => 'https://bearpowr.com'
        ];
    }

    public function registerComponents()
    {
        return [
            'Minorjet\Team\Components\Members' => 'members'
        ];
    }

    public function registerPermissions()
    {
        return [
            'minorjet.team.access_members'   => ['tab' => 'minorjet.team::lang.team.tab', 'label' => 'minorjet.team::lang.team.access_members']
        ];
    }

    public function registerNavigation()
    {
        return [
            'team' => [
                'label'       => 'minorjet.team::lang.team.menu_label',
                'url'         => Backend::url('minorjet/team/members'),
                'icon'        => 'icon-users',
                'permissions' => ['minorjet.team.*'],
                'order'       => 500,

                'sideMenu' => [
                    'new_member' => [
                        'label'       => 'minorjet.team::lang.members.new_member',
                        'icon'        => 'icon-user-plus',
                        'url'         => Backend::url('minorjet/team/members/create'),
                        'permissions' => ['minorjet.member.access_members']
                    ],
                    'members' => [
                        'label'       => 'minorjet.team::lang.team.members',
                        'icon'        => 'icon-user',
                        'url'         => Backend::url('minorjet/team/members'),
                        'permissions' => ['minorjet.team.access_members']
                    ]
                ]
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
    }
}
