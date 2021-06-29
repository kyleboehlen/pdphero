<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

// Models
use App\Models\Journal\JournalCategory;

class GenerateDefaultJournalCategories
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        // I know that you still worry, but I'm okay. If you're not happy, please don't stay.
        // What kind of sad boi shit is this??
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        // Create default categories
        foreach(config('journal.default_categories') as $category_name)
        {
            $default_category = new JournalCategory([
                'user_id' => $event->user->id,
                'name' => $category_name,
            ]);

            $default_category->save();
        }
    }
}
