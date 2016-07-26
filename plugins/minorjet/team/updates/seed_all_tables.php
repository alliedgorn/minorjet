<?php namespace Minorjet\Aircraft\Updates;

use Carbon\Carbon;
use Minorjet\Aircraft\Models\Aircraft;
use Minorjet\Aircraft\Models\Category;
use October\Rain\Database\Updates\Seeder;

class SeedAllTables extends Seeder
{

    public function run()
    {
//         Aircraft::create([
//             'title' => 'First post',
//             'slug' => 'first-post',
//             'content' => '
// This is your first ever **post**! It might be a good idea to update this post with some more relevant content.

// You can edit this content by selecting **Aricraft** from the administration back-end menu.

// *Enjoy the good times!*
//             ',
//             'excerpt' => 'The first ever post is here. It might be a good idea to update this post with some more relevant content.',
//             'published_at' => Carbon::now(),
//             'published' => true
//         ]);

//         Category::create([
//             'name' => trans('minorjet.aircraft::lang.categories.uncategorized'),
//             'slug' => 'uncategorized',
//         ]);
    }

}
