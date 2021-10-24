<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Constants
use App\Helpers\Constants\Addiction\Method;

// Models
use App\Models\Addictions\Addiction;

// Requests
use App\Http\Requests\Addictions\StoreRequest;
use App\Http\Requests\Addictions\UpdateRequest;

class AddictionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('first_visit.messages');
        $this->middleware('addiction.uuid');
        $this->middleware('addiction.milestone.uuid');
        $this->middleware('addiction.relapse.uuid');
        $this->middleware('verified');
        $this->middleware('membership');
    }

    /**
     * Addictions list view
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $addictions = Addiction::where('user_id', $request->user()->id)->get();

        return view('addictions.list')->with([
            'addictions' => $addictions,
        ]);
    }

    public function details(Addiction $addiction)
    {
        return view('addictions.details')->with([
            'addiction' => $addiction,
        ]);
    }

    public function create()
    {
        return view('addictions.create');
    }

    public function store(StoreRequest $request)
    {
        // Create new addiciton
        $addiction = new Addiction([
            'user_id' => $request->user()->id,
            'name' => $request->get('name'),
            'method_id' => $request->get('method'),
            'details' => $request->get('details'),
            'start_date' => $request->get('start-date'),
        ]);

        // Set moderation limits
        if($addiction->method_id == Method::MODERATION)
        {
            $addiction->moderated_amount = $request->get('moderation-amount');
            $addiction->moderated_period = $request->get('moderation-period');
            $addiction->moderated_date_format = $request->get('moderation-date-format');
        }

        if(!$addiction->save())
        {
            // Log error
            Log::error('Failed to store new Addiction.', [
                'user->id' => $user->id,
                'addiction' => $addiction->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create the addiction, please try again.'
            ]);
        }

        return redirect()->route('addiction.details', ['addiction' => $addiction->uuid]);
    }

    public function edit(Addiction $addiction)
    {
        return view('addictions.edit')->with([
            'addiction' => $addiction,
        ]);
    }

    public function update(UpdateRequest $request, Addiction $addiction)
    {
        // Update addiction
        $addiction->name = $request->get('name');
        $addiction->method_id = $request->get('method');
        $addiction->details = $request->get('details');
        $addiction->start_date = $request->get('start-date');

        // Set moderation limits
        if($addiction->method_id == Method::MODERATION)
        {
            $addiction->moderated_amount = $request->get('moderation-amount');
            $addiction->moderated_period = $request->get('moderation-period');
            $addiction->moderated_date_format = $request->get('moderation-date-format');
        }
        else
        {
            $addiction->moderated_amount = null;
            $addiction->moderated_period = null;
            $addiction->moderated_date_format = null;
        }

        if(!$addiction->save())
        {
            // Log error
            Log::error('Failed to update Addiction.', [
                'user->id' => $user->id,
                'addiction' => $addiction->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to update the addiction, please try again.'
            ]);
        }

        return redirect()->route('addiction.details', ['addiction' => $addiction->uuid]);
    }

    public function destroy(Addiction $addiction)
    {
        if(!$addiction->delete())
        {
            Log::error('Failed to delete addiction', $addiction->toArray());
            return redirect()->back();
        }

        return redirect()->route('addictions');
    }
}
