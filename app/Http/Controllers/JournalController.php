<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

// Constants
use App\Helpers\Constants\Habits\HistoryType as HabitHistoryType;

// Models
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;
use App\Models\Habits\HabitHistory;
use App\Models\Journal\JournalCategory;
use App\Models\Journal\JournalEntry;
use App\Models\ToDo\ToDo;

// Requests
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
        $this->middleware('verified');
        // To-do: Add subscription middleware
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
                    ->where('completed', 1)
                    ->whereBetween('updated_at', $between_array)
                    ->get()->count();

            // Get habits
            $array['habit_count'] =
                HabitHistory::select('habit_id')
                    ->whereIn('habit_id', $habit_ids)
                    ->where('type_id', HabitHistoryType::COMPLETED)
                    ->whereBetween('day', $between_array)
                    ->groupBy('habit_id')->get()->count();

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

    }

    public function viewEntry(JournalEntry $journal_entry)
    {
        return view('journal.entry')->with([
            'journal_entry' => $journal_entry,
        ]);
    }

    public function viewSearch()
    {

    }

    public function search()
    {

    }

    public function editCategories(Request $request)
    {
        // Get users categories
        $categories = $request->user()->journalCategories;

        // Return edit view
        return view('journal.categories')->with([
            'categories' => $categories,
        ]);
    }

    public function storeCategory(StoreCategoryRequest $request)
    {
        // Create category
        $category = new JournalCategory([
            'name' => $request->name,
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
        // Create new entry
        $entry = new JournalEntry();

        // Set user
        $entry->user_id = $request->user()->id;

        // Set title
        $entry->title = $request->get('title');

        // Set category
        $category_uuid = $request->get('category');
        if($category_uuid != 0)
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
