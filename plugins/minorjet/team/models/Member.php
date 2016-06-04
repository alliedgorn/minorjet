<?php namespace Minorjet\Team\Models;

use App;
use Str;
use Html;
use Lang;
use Model;
use Markdown;
use ValidationException;
use Minorjet\Team\Classes\TagProcessor;
use Backend\Models\User;
use Carbon\Carbon;
use DB;

class Member extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'minorjet_team_members';

    /*
     * Validation
     */
    public $rules = [
        'firstname' => 'required',
        'lastname' => 'required',
        'position' => 'required'
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['published_at'];

    /**
     * The attributes on which the post list can be ordered
     * @var array
     */
    public static $allowedSortingOptions = array(
        'firstname asc' => 'First Name (ascending)',
        'firstname desc' => 'First Name (descending)',
        'lastname asc' => 'Last Name (ascending)',
        'lastname desc' => 'Last Name (descending)',
        'position asc' => 'Position (ascending)',
        'position desc' => 'Position (descending)',
        'priority asc' => 'Priority (ascending)',
        'priority desc' => 'Priority (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'random' => 'Random' 
    );

    /*
     * Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User']
    ];

    public $attachOne = [
        'member_image' => ['System\Models\File']
    ];

    public static function formatHtml($input, $preview = false)
    {
        $result = Markdown::parse(trim($input));

        if ($preview) {
            $result = str_replace('<pre>', '<pre class="prettyprint">', $result);
        }

        $result = TagProcessor::instance()->processTags($result, $preview);

        return $result;
    }

    //
    // Scopes
    //

    public function scopeIsActive($query)
    {
        return $query
            ->whereNotNull('active')
            ->where('active', true)
        ;
    }

    /**
     * Lists posts for the front end
     * @param  array $options Display options
     * @return self
     */
    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page'       => 1,
            'perPage'    => 30,
            'sort'       => 'created_at',
            'search'     => '',
            'active'  => true
        ], $options));

        $searchableFields = ['firstname', 'lastname', 'position'];

        if ($active) {
            $query->isActive();
        }

        /*
         * Sorting
         */
        if (!is_array($sort)) {
            $sort = [$sort];
        }

        foreach ($sort as $_sort) {

            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);
                if (count($parts) < 2) {
                    array_push($parts, 'desc');
                }
                list($sortField, $sortDirection) = $parts;
                if ($sortField == 'random') {
                    $sortField = DB::raw('RAND()');
                }
                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        return $query->paginate($perPage, $page);
    }

}
