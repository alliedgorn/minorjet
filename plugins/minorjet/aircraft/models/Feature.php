<?php namespace Minorjet\Aircraft\Models;

use App;
use Str;
use Html;
use Lang;
use Model;
use Markdown;
use ValidationException;
use Minorjet\Aircraft\Classes\TagProcessor;
use Backend\Models\User;
use Carbon\Carbon;
use DB;

class Feature extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'minorjet_aircraft_features';

    /*
     * Validation
     */
    public $rules = [
        'title' => 'required',
        'content' => 'required'
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
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        'published_at asc' => 'Published (ascending)',
        'published_at desc' => 'Published (descending)',
        'random' => 'Random'
    );

    /*
     * Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User']
    ];

    public $belongsToMany = [
        'aircrafts' => [
            'Minorjet\Aircraft\Models\Aircraft',
            'table' => 'minorjet_aircraft_aircrafts_features',
            'order' => 'title'
        ]
    ];

    public $attachOne = [
        'featured_images' => ['System\Models\File', 'order' => 'sort_order'],
        'content_images' => ['System\Models\File']
    ];

    /**
     * @var array The accessors to append to the model's array form.
     */
    protected $appends = ['summary', 'has_summary'];

    public $preview = null;

    public function afterValidate()
    {
        if ($this->published && !$this->published_at) {
            throw new ValidationException([
               'published_at' => Lang::get('minorjet.aircraft::lang.feature.published_validation')
            ]);
        }
    }

    public function beforeSave()
    {
        $this->content_html = self::formatHtml($this->content);
    }

    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller)
    {
        $params = [
            'id' => $this->id,
            'slug' => $this->slug,
        ];

        if (array_key_exists('aircrafts', $this->getRelations())) {
            $params['aircraft'] = $this->aircrafts->count() ? $this->aircrafts->first()->slug : null;
        }

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    /**
     * Used to test if a certain user has permission to edit post,
     * returns TRUE if the user is the owner or has other posts access.
     * @param User $user
     * @return bool
     */
    public function canEdit(User $user)
    {
        return ($this->user_id == $user->id) || $user->hasAnyAccess(['minorjet.aircraft.access_other_aircrafts']);
    }

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

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<', Carbon::now())
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
            'aircrafts' => null,
            'aircraft'   => null,
            'search'     => '',
            'published'  => true
        ], $options));

        $searchableFields = ['title', 'slug', 'excerpt', 'content'];

        if ($published) {
            $query->isPublished();
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

        /*
         * Aircrafts
         */
        if ($aircrafts !== null) {
            if (!is_array($aircrafts)) $aircrafts = [$aircrafts];
            $query->whereHas('aircrafts', function($q) use ($aircrafts) {
                $q->whereIn('id', $aircrafts);
            });
        }

        /*
         * Aircraft, including children
         */
        if ($aircraft !== null) {
            $aircraft = Aircraft::find($aircraft);

            $aircrafts = $aircraft->getAllChildrenAndSelf()->lists('id');
            $query->whereHas('aircrafts', function($q) use ($aircrafts) {
                $q->whereIn('id', $aircrafts);
            });
        }

        return $query->paginate($perPage, $page);
    }

    /**
     * Allows filtering for specifc aircrafts
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $aircrafts List of aircraft ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterAircrafts($query, $aircrafts)
    {
        return $query->whereHas('aircrafts', function($q) use ($aircrafts) {
            $q->whereIn('id', $aircrafts);
        });
    }

    //
    // Summary / Excerpt
    //

    /**
     * Used by "has_summary", returns true if this post uses a summary (more tag)
     * @return boolean
     */
    public function getHasSummaryAttribute()
    {
        return strlen($this->getSummaryAttribute()) < strlen($this->content_html);
    }

    /**
     * Used by "summary", if no excerpt is provided, generate one from the content.
     * Returns the HTML content before the <!-- more --> tag or a limited 600
     * character version.
     *
     * @return string
     */
    public function getSummaryAttribute()
    {
        $excerpt = array_get($this->attributes, 'excerpt');
        if (strlen(trim($excerpt))) {
            return $excerpt;
        }

        $more = '<!-- more -->';
        if (strpos($this->content_html, $more) !== false) {
            $parts = explode($more, $this->content_html);
            return array_get($parts, 0);
        }

        return Str::limit(Html::strip($this->content_html), 600);
    }
}
