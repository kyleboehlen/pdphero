<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Journal\JournalCategory;
use App\Models\Journal\JournalEntry;

// Requests
use App\Http\Requests\Journal\StoreCategoryRequest;

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

    }

    public function storeEntry()
    {

    }

    public function editEntry(JournalEntry $journal_entry)
    {

    }

    public function updateEntry(JournalEntry $journal_entry)
    {

    }

    public function destroyEntry(JournalEntry $journal_entry)
    {

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
