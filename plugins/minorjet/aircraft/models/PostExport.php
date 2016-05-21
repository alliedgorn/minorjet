<?php namespace Minorjet\Aircraft\Models;

use Backend\Models\ExportModel;
use ApplicationException;

/**
 * Post Export Model
 */
class PostExport extends ExportModel
{
    public $table = 'minorjet_aircraft_aircrafts';

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'aircraft_user' => [
            'Backend\Models\User',
            'key' => 'user_id'
        ]
    ];

    public $belongsToMany = [
        'aircraft_categories' => [
            'Minorjet\Aircraft\Models\Category',
            'table' => 'minorjet_aircraft_aircrafts_categories',
            'key' => 'aircraft_id',
            'otherKey' => 'category_id'
        ]
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'author_email',
        'categories'
    ];

    public function exportData($columns, $sessionKey = null)
    {
        $result = self::make()
            ->with([
                'aircraft_user',
                'aircraft_categories'
            ])
            ->get()
            ->toArray()
        ;

        return $result;
    }

    public function getAuthorEmailAttribute()
    {
        if (!$this->aircraft_user) return '';
        return $this->aircraft_user->email;
    }

    public function getCategoriesAttribute()
    {
        if (!$this->aircraft_categories) return '';
        return $this->encodeArrayValue($this->aircraft_categories->lists('name'));
    }
}