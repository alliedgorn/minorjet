<?php

return [
    'plugin' => [
        'name' => 'Team',
        'description' => 'Team members managing plugin.'
    ],
    'team' => [
        'menu_label' => 'Team',
        'menu_description' => 'Manage Team',
        'members' => 'Member',
        'create_member' => 'member',
        'tab' => 'Team',
        'delete_confirm' => 'Are you sure?',
        'chart_active' => 'Active',
        'chart_inactive' => 'Inactive',
        'chart_total' => 'Total'
    ],
    'members' => [
        'list_title' => 'Manage members',
        'hide_active' => 'Hide active',
        'new_member' => 'New member'
    ],
    'member' => [
        'firstname' => 'First Name',
        'firstname_placeholder' => 'New member first name',
        'lastname' => 'Last Name',
        'lastname_placeholder' => 'New member last name',
        'position' => 'Position',
        'position_placeholder' => 'New member position',
        'priority' => 'Priority',
        'created' => 'Created',
        'created_date' => 'Created date',
        'updated' => 'Updated',
        'updated_date' => 'Updated date',
        'active' => 'Active',
        'tab_manage' => 'Manage',
        'member_image' => 'Image',
        'delete_confirm' => 'Do you really want to delete this member?',
        'close_confirm' => 'The member is not saved.',
        'return_to_members' => 'Return to members list'
    ],
    'settings' => [
        'members_title' => 'Member List',
        'members_description' => 'Displays a list of members on the page.',
        'members_pagination' => 'Page number',
        'members_pagination_description' => 'This value is used to determine what page the user is on.',
        'members_per_page' => 'Members per page',
        'members_per_page_validation' => 'Invalid format of the members per page value',
        'members_no_members' => 'No members message',
        'members_no_members_description' => 'Message to display in the member list in case if there are no members. This property is used by the default component partial.',
        'members_order' => 'Member order',
        'members_order_description' => 'Attribute on which the members should be ordered',
    ]
];
