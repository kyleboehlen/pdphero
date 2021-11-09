<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Constants
use App\Helpers\Constants\Addiction\DateFormat;
use App\Helpers\Constants\Addiction\Method;

// Models
use App\Models\Addictions\Addiction;
use App\Models\Addictions\AddictionMilestone;

// Requests
use App\Http\Requests\Addictions\StoreRequest;
use App\Http\Requests\Addictions\StoreMilestoneRequest;
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
        $usage = null;
        $usage_color = null;
        $milestone_name = null;

        if($addiction->method_id == Method::MODERATION)
        {
            $usage = $addiction->usage()->get()->count();

            if($usage == 0)
            {
                $usage_color = 'green';
            }
            elseif($usage >= $addiction->moderated_amount)
            {
                $usage_color = 'red';
            }
            else
            {
                $usage_color = 'yellow';
            }
        }
        elseif($addiction->method_id == Method::ABSTINENCE)
        {
            $acheieved_milestones = $addiction->reachedMilestones()->get();
            
            if($acheieved_milestones->count() == 0)
            {
                $milestone_name = null;
            }
            else
            {
                $milestone_name = $acheieved_milestones->last()->name;
            }
        }

        $start_carbon = $addiction->getStartCarbon();
        $addiction->milestones->each(function ($milestone) use ($start_carbon){
            $milestone->carbon_reached = $milestone->dateFromCarbon(clone $start_carbon);
        });

        return view('addictions.details')->with([
            'addiction' => $addiction,
            'method' => Method::class,
            'usage' => $usage,
            'usage_color' => $usage_color,
            'milestone_name' => $milestone_name,
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

        // Create default milestones
        buildDefaultMilestones($addiction);

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
            $addiction->moderated_date_format_id = $request->get('moderation-date-format');
        }
        else
        {
            $addiction->moderated_amount = null;
            $addiction->moderated_period = null;
            $addiction->moderated_date_format_id = null;
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

    public function milestones(Addiction $addiction)
    {
        $addiction->load('milestones');

        return view('addictions.milestones')->with([
            'addiction' => $addiction,
        ]);
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

    public function milestoneForm(Addiction $addiction)
    {
        $date_formats = config('addictions.date_formats');

        return view('addictions.create-milestone')->with([
            'addiction' => $addiction,
            'date_formats' => $date_formats,
        ]);
    }

    public function storeMilestone(StoreMilestoneRequest $request, Addiction $addiction)
    {
        $amount = $request->get('milestone-amount');
        $milestone_date_format = $request->get('milestone-date-format');
        $max = config('addictions.date_formats')[$milestone_date_format]['max'];
        if($amount > $max)
        {
            return redirect()->back()->withErrors([
                'milestone-amount' => 'Max value for ' . config('addictions.date_formats')[$milestone_date_format]['name'] . ' is ' . $max,
            ]);
        }
        
        // Create new addiciton
        $milestone = new AddictionMilestone([
            'addiction_id' => $addiction->id,
            'name' => $request->get('name'),
            'amount' => $amount,
            'date_format_id' => $milestone_date_format,
            'reward' => $request->get('reward'),
        ]);

        if(!$milestone->save())
        {
            // Log error
            Log::error('Failed to store new addiction milestone.', [
                'user->id' => $user->id,
                'milestone' => $milestone->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create the milestone, please try again.'
            ]);
        }

        return redirect()->route('addiction.milestones', ['addiction' => $addiction->uuid]);
    }

    public function destroyMilestone(AddictionMilestone $milestone)
    {
        $milestone->load('addiction');

        if(!$milestone->delete())
        {
            Log::error('Failed to delete addiction milestone', $milestone->toArray());
            return redirect()->back();
        }

        return redirect()->route('addiction.milestones', ['addiction' => $milestone->addiction->uuid]);
    }
}
