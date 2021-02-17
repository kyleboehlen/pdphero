<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Journal\JournalEntry;

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

    }

    public function viewDay($date)
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
}
