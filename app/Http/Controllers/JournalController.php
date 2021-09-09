<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

// Constants
use App\Helpers\Constants\Habits\Type as HabitType;
use App\Helpers\Constants\Habits\HistoryType as HabitHistoryType;
use App\Helpers\Constants\User\Setting;
use App\Helpers\Constants\ToDo\Type as ToDoType;

// Jobs
use App\Jobs\CalculateHabitStrength;

// Models
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\Bucketlist\BucketlistItem;
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;
use App\Models\Habits\Habits;
use App\Models\Habits\HabitHistory;
use App\Models\Journal\JournalCategory;
use App\Models\Journal\JournalEntry;
use App\Models\ToDo\ToDo;

// Requests
use App\Http\Requests\Journal\SearchRequest;
use App\Http\Requests\Journal\StoreRequest;
use App\Http\Requests\Journal\StoreCategoryRequest;
use App\Http\Requests\Journal\UpdateRequest;

class JournalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('journal.entry.uuid');
        $this->middleware('journal.category.uuid');
        $this->middleware('todo.uuid');
        $this->middleware('verified');
        $this->middleware('membership');
    }

    public function index()
    {
        // Send to default list view
        return redirect()->route('journal.view.list');
    }

    // View functions
    public function viewList($month = null, $year = null)
    {
        // Create carbon for user's now
        $user = \Auth::user();
        $timezone = $user->timezone ?? 'America/Denver';
        $user_now = Carbon::now($timezone);

        // Set default month to current month
        if(is_null($month))
        {
            $month = strtolower($user_now->format('F'));
        }

        // Set default year to current year
        if(is_null($year))
        {
            $year = $user_now->format('Y');
        }

        // Build month drop down -- honestly I'm sure there's a more elegant way to loop this but.... for the sake K.I.S.S. and efficiency
        $month_dropdown = [
            'january' => 'January',
            'february' => 'February',
            'march' => 'March',
            'april' => 'April',
            'may' => 'May',
            'june' => 'June',
            'july' => 'July',
            'august' => 'August',
            'september' => 'September',
            'october' => 'October',
            'november' => 'November',
            'december' => 'December',
        ];

        // Build year dropdown, again... don't over think this
        $year_dropdown = array();
        $oldest_year = Carbon::parse($user->created_at)->setTimezone($timezone)->format('Y');
        for($i_year = $oldest_year; $i_year <= $user_now->format('Y'); $i_year++)
        {
            array_push($year_dropdown, $i_year);
        }

        // Create a carbon period to iterate through the month
        $month_carbon = Carbon::createFromFormat('j F Y', '1 ' . ucwords($month) . ' ' . $year, $timezone);
        $carbon_period = new CarbonPeriod($month_carbon->format('Y-m-d'), '1 day', (clone $month_carbon)->endOfMonth()->format('Y-m-d'));
        $content_array = array(); // Create array to hold dates and item counts
        $habit_ids = $user->habits()->get()->pluck('id')->toArray(); // For habit histories
        $goal_ids = $user->goals()->get()->pluck('id')->toArray(); // For action items
        foreach($carbon_period as $date)
        {
            // Convert from user timezone to UTC
            $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $date, $timezone);
            $start_timestamp = (clone $carbon)->setTimezone('UTC')->toDateTimeString();
            $end_timestamp = (clone $carbon)->endOfDay()->setTimezone('UTC')->toDateTimeString();
            $between_array = [$start_timestamp, $end_timestamp];

            // Set array and date
            $array = [
                'display_date' => $carbon->format('n/j/y'),
                'route_date' => $carbon->format('Y-m-d'),
            ];

            // Get Todos
            $array['todo_count'] =
                ToDo::where('user_id', $user->id)
                    ->where('type_id', TodoType::TODO_ITEM)
                    ->where('completed', 1)
                    ->whereBetween('updated_at', $between_array)
                    ->get()->count();

            // Get habits
            $array['habit_count'] =
                HabitHistory::select('habit_id')
                    ->whereIn('habit_id', $habit_ids)
                    ->groupBy('habit_id')
                    ->where('type_id', HabitHistoryType::COMPLETED)
                    ->where('day', $carbon->format('Y-m-d'))
                    ->get()->count();

            // Get goals
            $array['goal_count'] =
                Goal::where('user_id', $user->id)
                    ->where('achieved', 1)
                    ->whereBetween('updated_at', $between_array)
                    ->get()->count();

            // Get action items
            $array['action_item_count'] =
                GoalActionItem::whereIn('goal_id', $goal_ids)
                    ->where('achieved', 1)
                    ->whereBetween('updated_at', $between_array)
                    ->get()->count();

            // Get entries
            $array['journal_entry_count'] =
                JournalEntry::where('user_id', $user->id)
                    ->whereBetween('created_at', $between_array)
                    ->get()->count();

            // Get affirmations
            $array['affirmations_count'] =
                AffirmationsReadLog::where('user_id', $user->id)
                    ->whereBetween('read_at', $between_array)
                    ->get()->count();

            $array['bucketlist_item_count'] =
                BucketlistItem::where('user_id', $user->id)
                    ->where('completed', 1)
                    ->whereBetween('updated_at', $between_array)
                    ->get()->count();

            foreach($array as $key => $value)
            {
                if(strpos($key, 'count') !== false)
                {
                    if($value > 0)
                    {
                        array_push($content_array, $array);
                        break;
                    }
                }
            }
        }

        return view('journal.list')->with([
            'month' => $month,
            'month_dropdown' => $month_dropdown,
            'year' => $year,
            'year_dropdown' => $year_dropdown,
            'content_array' => $content_array,
        ]);
    }

    public function viewDay($date = null)
    {
        // Create carbon with user's timezone for passed date
        $user = \Auth::user();
        $timezone = $user->timezone ?? 'America/Denver';
        $carbon = Carbon::createFromFormat('Y-m-d', $date, $timezone);

        // Create start/end timestamps for that day
        $start_timestamp = (clone $carbon)->startOfDay()->setTimezone('UTC')->toDateTimeString();
        $end_timestamp = (clone $carbon)->endOfDay()->setTimezone('UTC')->toDateTimeString();
        $between_array = [$start_timestamp, $end_timestamp];

        // Get content
        $habit_ids = $user->habits()->get()->pluck('id')->toArray(); // For habit histories
        $goal_ids = $user->goals()->get()->pluck('id')->toArray(); // For action items

        // Get habits
        $completed_habits =
            HabitHistory::whereIn('habit_id', $habit_ids)
                ->where('type_id', HabitHistoryType::COMPLETED)
                ->where('day', $carbon->format('Y-m-d'))
                ->with('habit')
                ->get();
        $skipped_habits =
            HabitHistory::whereIn('habit_id', $habit_ids)
                ->where('type_id', HabitHistoryType::SKIPPED)
                ->where('day', $carbon->format('Y-m-d'))
                ->with('habit')
                ->get();
        $missed_habits =
            HabitHistory::whereIn('habit_id', $habit_ids)
                ->where('type_id', HabitHistoryType::MISSED)
                ->where('day', $carbon->format('Y-m-d'))
                ->whereNotNull('notes')
                ->with('habit')
                ->get();

        // And affirmations
        $affirmations_count =
            AffirmationsReadLog::where('user_id', $user->id)
                ->whereBetween('read_at', $between_array)
                ->get()->count();

        // Get Todos
        $todos =
            ToDo::where('user_id', $user->id)
                ->where('type_id', TodoType::TODO_ITEM)
                ->where('completed', 1)
                ->whereBetween('updated_at', $between_array)
                ->orderBy('updated_at', 'asc')
                ->get();

        // Get goals
        $goals =
            Goal::where('user_id', $user->id)
                ->where('achieved', 1)
                ->whereBetween('updated_at', $between_array)
                ->orderBy('updated_at', 'asc')
                ->get();

        // Get action items
        $action_items =
            GoalActionItem::whereIn('goal_id', $goal_ids)
                ->where('achieved', 1)
                ->whereBetween('updated_at', $between_array)
                ->orderBy('updated_at', 'asc')
                ->get();

        // Get entries
        $journal_entries =
            JournalEntry::where('user_id', $user->id)
                ->whereBetween('created_at', $between_array)
                ->orderBy('created_at', 'asc')
                ->get();

        // Get bucketlist items
        $bucketlist_items =
            BucketlistItem::where('user_id', $user->id)
                ->where('completed', 1)
                ->whereBetween('updated_at', $between_array)
                ->orderBy('updated_at', 'asc')
                ->get();

        // Build the filter dropdown
        $filter_dropdown = array();

        if($affirmations_count > 0)
        {
            $filter_dropdown['affirmations'] = 'Affirmations';
        }

        if($completed_habits->count() > 0 || $skipped_habits->count() > 0)
        {
            $filter_dropdown['habit'] = 'Habits';
        }

        if($todos->count() > 0)
        {
            $filter_dropdown['todo'] = 'To-Do Items';
        }

        if($goals->count() > 0)
        {
            $filter_dropdown['goal'] = 'Goals';
        }

        if($action_items->count() > 0)
        {
            $filter_dropdown['action-item'] = 'Action Items';
        }

        if($journal_entries->count() > 0)
        {
            $filter_dropdown['journal-entry'] = 'Journal Entries';
        }

        if($bucketlist_items->count() > 0)
        {
            $filter_dropdown['bucketlist-item'] = 'Bucketlist Items';
        }

        if(count($filter_dropdown) > 1)
        {
            $filter_dropdown = array_merge(['all' => 'Show All'], $filter_dropdown);
        }

        // Order all these fucking models into a timeline
        $timeline_array = array(); // This could probably be cleaned up by running a foreach over an array('collection' => $collection, 'property' => 'created_at/updated_at')
        while(
            $todos->count() > 0 ||
            $goals->count() > 0 ||
            $action_items->count() > 0 ||
            $journal_entries->count() > 0 ||
            $bucketlist_items->count() > 0)
        {
            $obj = null;

            if($todos->count() > 0)
            {
                if(is_null($obj))
                {
                    $obj = $todos->first();
                    $obj_carbon = Carbon::parse($obj->updated_at)->setTimezone($timezone);
                    $obj_collection = $todos;
                }
                else
                {
                    $compare_carbon = Carbon::parse($todos->first()->updated_at)->setTimezone($timezone);
                    if($compare_carbon->lessThan($obj_carbon))
                    {
                        $obj = $todos->first();
                        $obj_carbon = $compare_carbon;
                        $obj_collection = $todos;
                    }
                }
            }

            if($goals->count() > 0)
            {
                if(is_null($obj))
                {
                    $obj = $goals->first();
                    $obj_carbon = Carbon::parse($obj->updated_at)->setTimezone($timezone);
                    $obj_collection = $goals;
                }
                else
                {
                    $compare_carbon = Carbon::parse($goals->first()->updated_at)->setTimezone($timezone);
                    if($compare_carbon->lessThan($obj_carbon))
                    {
                        $obj = $goals->first();
                        $obj_carbon = $compare_carbon;
                        $obj_collection = $goals;
                    }
                }
            }

            if($action_items->count() > 0)
            {
                if(is_null($obj))
                {
                    $obj = $action_items->first();
                    $obj_carbon = Carbon::parse($obj->updated_at)->setTimezone($timezone);
                    $obj_collection = $action_items;
                }
                else
                {
                    $compare_carbon = Carbon::parse($action_items->first()->updated_at)->setTimezone($timezone);
                    if($compare_carbon->lessThan($obj_carbon))
                    {
                        $obj = $action_items->first();
                        $obj_carbon = $compare_carbon;
                        $obj_collection = $action_items;
                    }
                }
            }

            if($journal_entries->count() > 0)
            {
                if(is_null($obj))
                {
                    $obj = $journal_entries->first();
                    $obj_carbon = Carbon::parse($obj->created_at)->setTimezone($timezone);
                    $obj_collection = $journal_entries;
                }
                else
                {
                    $compare_carbon = Carbon::parse($journal_entries->first()->created_at)->setTimezone($timezone);
                    if($compare_carbon->lessThan($obj_carbon))
                    {
                        $obj = $journal_entries->first();
                        $obj_carbon = $compare_carbon;
                        $obj_collection = $journal_entries;
                    }
                }
            }

            if($bucketlist_items->count() > 0)
            {
                if(is_null($obj))
                {
                    $obj = $bucketlist_items->first();
                    $obj_carbon = Carbon::parse($obj->updated_at)->setTimezone($timezone);
                    $obj_collection = $bucketlist_items;
                }
                else
                {
                    $compare_carbon = Carbon::parse($bucketlist_items->first()->updated_at)->setTimezone($timezone);
                    if($compare_carbon->lessThan($obj_carbon))
                    {
                        $obj = $bucketlist_items->first();
                        $obj_carbon = $compare_carbon;
                        $obj_collection = $bucketlist_items;
                    }
                }
            }

            $obj->display_time = $obj_carbon->format('g:i A');
            array_push($timeline_array, $obj);
            $obj_collection->forget($obj_collection->keys()->first());
        }

        return view('journal.day')->with([
            'date' => $date,
            'completed_habits' => $completed_habits,
            'skipped_habits' => $skipped_habits,
            'missed_habits' => $missed_habits,
            'affirmations_count' => $affirmations_count,
            'filter_dropdown' => $filter_dropdown,
            'timeline_array' => $timeline_array,
        ]);
    }

    public function viewEntry(JournalEntry $journal_entry)
    {
        return view('journal.entry')->with([
            'journal_entry' => $journal_entry,
        ]);
    }

    public function viewToDo(ToDo $todo)
    {
        return view('journal.todo')->with([
            'todo' => $todo,
        ]);
    }

    public function search(SearchRequest $request)
    {
        // Get search term
        $keywords = $request->get('keywords');

        // Create UTC timestamps
        $user = $request->user();
        $timezone = $user->timezone ?? 'America/Denver';
        $start_timestamp = Carbon::createFromFormat('Y-m-d', $request->get('start-date'), $timezone)->startOfDay()->setTimezone('UTC')->toDateTimeString();
        $end_timestamp = Carbon::createFromFormat('Y-m-d', $request->get('end-date'), $timezone)->endOfDay()->setTimezone('UTC')->toDateTimeString();

        // Search
        $journal_entries = JournalEntry::where('user_id', $user->id)->whereBetween('created_at', [$start_timestamp, $end_timestamp])->where(function($q) use ($keywords){
            return $q->where('title', 'like', '%' . $keywords . '%')->orWhere('body', 'like', '%' . $keywords . '%');
        })->orderBy('created_at')->get();

        return view('journal.search')->with([
            'journal_entries' => $journal_entries,
            'keywords' => $keywords,
        ]);
    }

    public function editCategories(Request $request)
    {
        // Get users categories
        $categories = $request->user()->journalCategories()->get();

        // Return edit view
        return view('journal.categories')->with([
            'categories' => $categories,
        ]);
    }

    public function storeCategory(StoreCategoryRequest $request)
    {
        // Create category
        $category = new JournalCategory([
            'name' => $request->get('name'),
            'user_id' => $request->user()->id,
        ]);

        // Save/log errors
        if(!$category->save())
        {
            Log::error('Failed to save journal category', $category->toArray());
            return redirect()->back();
        }

        return redirect()->route('journal.edit.categories');
    }

    public function createEntry()
    {
        return view('journal.create');
    }

    public function storeEntry(StoreRequest $request)
    {
        // Get User
        $user = $request->user();

        // Create new entry
        $entry = new JournalEntry();

        // Set user
        $entry->user_id = $request->user()->id;

        // Set title
        $entry->title = $request->get('title');

        // Set category
        $category_uuid = $request->get('category');
        if($category_uuid != 'no-category')
        {
            $category_id = JournalCategory::where('uuid', $category_uuid)->first()->id;
            $entry->category_id = $category_id;
        }

        // Set mood
        foreach(config('journal.moods') as $id => $mood)
        {
            if($request->has("mood-$id"))
            {
                $entry->mood_id = $id;
            }
        }

        // Set body
        $entry->body = $request->get('body');

        if(!$entry->save())
        {
            // Log error
            Log::error('Failed to store new Journal entry.', [
                'user->id' => $user->id,
                'entry' => $entry->toArray(),
                'request' => $request->all()
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create Journal entry, please try again.'
            ]);
        }

        // Update journaling habit strength
        if($user->getSettingValue(Setting::HABITS_SHOW_JOURNALING_HABIT))
        {
            // Get the journaling habit if exists
            $habit = Habits::where('user_id', $user->id)->where('type_id', HabitType::JOURNALING_HABIT)->first();
            if(!is_null($habit))
            {
                // Queue building strength habit
                $queued_habit_strength = new CalculateHabitStrength($habit);
                $this->dispatch($queued_habit_strength);
            }
        }

        return redirect()->route('journal.view.entry', ['journal_entry' => $entry->uuid]);
    }

    public function editEntry(JournalEntry $journal_entry)
    {
        return view('journal.edit')->with([
            'journal_entry' => $journal_entry,
        ]);
    }

    public function updateEntry(UpdateRequest $request, JournalEntry $journal_entry)
    {
        // Set title
        $journal_entry->title = $request->get('title');

        // Set category
        $category_uuid = $request->get('category');
        if($category_uuid != 0)
        {
            $category_id = JournalCategory::where('uuid', $category_uuid)->first()->id;
            $journal_entry->category_id = $category_id;
        }
        else
        {
            $journal_entry->category_id = null;
        }

        // Set mood
        foreach(config('journal.moods') as $id => $mood)
        {
            if($request->has("mood-$id"))
            {
                $journal_entry->mood_id = $id;
            }
        }

        // Set body
        $journal_entry->body = $request->get('body');

        if(!$journal_entry->save())
        {
            // Log error
            Log::error('Failed to update journal entry.', [
                'journal_entry' => $journal_entry->toArray(),
                'request' => $request->all()
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to update this Journal entry, please try again.'
            ]);
        }

        return redirect()->route('journal.view.entry', ['journal_entry' => $journal_entry->uuid]);
    }

    public function destroyEntry(JournalEntry $journal_entry)
    {
        if(!$journal_entry->delete())
        {
            Log::error('Failed to delete journal entry', $journal_entry->toArray());
            return redirect()->back();
        }

        return redirect()->route('journal.view.list');
    }

    public function destroyCategory(Request $request, JournalCategory $category)
    {
        // Remove category from entries
        JournalEntry::where('user_id', $request->user()->id)->where('category_id', $category->id)->update(['category_id' => null]);

        // Delete category
        if(!$category->delete())
        {
            Log::error('Failed to delete journal category', $category->toArray());
            return redirect()->back();
        }

        return redirect()->route('journal.edit.categories');
    }

    public function colorGuide()
    {
        return view('journal.colors');
    }
}
