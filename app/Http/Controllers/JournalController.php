<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Journal\JournalCategory;
use App\Models\Journal\JournalEntry;

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
        return view('journal.list');
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
