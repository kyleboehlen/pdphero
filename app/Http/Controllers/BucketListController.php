<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Constants
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Bucketlist\BucketlistCategory;
use App\Models\Bucketlist\BucketlistItem;

// Requests
use App\Http\Requests\Bucketlist\StoreRequest;
use App\Http\Requests\Bucketlist\StoreCategoryRequest;
use App\Http\Requests\Bucketlist\UpdateRequest;

class BucketListController extends Controller
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
        $this->middleware('bucketlist.uuid');
        $this->middleware('bucketlist.category.uuid');
        $this->middleware('verified');
        $this->middleware('membership');
    }

    /**
     * Home To-Do page
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // Get logged in user
        $user = $request->user();

        // Load user's bucketlist
        $bucketlist_items =
            BucketlistItem::where('user_id', $user->id)->where('completed', 0) // Just incomplete bucketlist items
                ->with('category')->orderBy('name')->get();

        // Get all the users categories
        $categories = $user->bucketlistCategories()->get();

        // Build filter drop down
        $category_filter_array = $bucketlist_items->whereNotNull('category')->pluck('category')->unique()->pluck('id')->toArray();

        // Return to-do view
        return view('bucketlist.list')->with([
            'bucketlist_items' => $bucketlist_items,
            'user' => $user,
            'setting' => Setting::class,
            'categories' => $categories,
            'category_filter_array' => $category_filter_array,
        ]);
    }

    public function viewDetails(BucketlistItem $bucketlist_item)
    {
        // Load category
        $bucketlist_item->load('category');

        // Return view details page
        return view('bucketlist.details')->with([
            'item' => $bucketlist_item,
        ]);
    }

    public function viewCompleted(Request $request)
    {
        // Get logged in user
        $user = $request->user();

        // Load user's bucketlist
        $bucketlist_items =
            BucketlistItem::where('user_id', $user->id)->where('completed', 1) // Just completed bucketlist items
                ->orderBy('updated_at', 'desc')->get();

        // Return to-do view
        return view('bucketlist.completed')->with([
            'bucketlist_items' => $bucketlist_items,
        ]);
    }

    public function create()
    {
        // Return the create bucketlist item form
        return view('bucketlist.create');
    }

    public function store(StoreRequest $request)
    {
        // Create new bucketlist item
        $bucketlist_item = new BucketlistItem([
            'name' => $request->get('name'),
            'details' => $request->get('details'),
            'user_id' => $request->user()->id,
        ]);

        // Set category
        $category_uuid = $request->get('category');
        if($category_uuid != 'no-category')
        {
            $bucketlist_item->category_id = BucketlistCategory::where('uuid', $category_uuid)->first()->id;
        }

        if(!$bucketlist_item->save())
        {
            // Log error
            Log::error('Failed to store new Bucketlist item.', [
                'user->id' => $user->id,
                'bucketlist_item' => $bucketlist_item->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create the bucketlist item, please try again.'
            ]);
        }

        return redirect()->route('bucketlist');
    }

    public function edit(BucketlistItem $bucketlist_item)
    {
        // Return view to edit bucketlist item
        return view('bucketlist.edit')->with([
            'item' => $bucketlist_item,
        ]);
    }

    public function update(UpdateRequest $request, BucketlistItem $bucketlist_item)
    {
        // Update bucketlist item
        $bucketlist_item->name = $request->get('name');
        $bucketlist_item->details = $request->get('details');

        // Set category
        $category_uuid = $request->get('category');
        if($category_uuid != 'no-category')
        {
            $bucketlist_item->category_id = BucketlistCategory::where('uuid', $category_uuid)->first()->id;
        }
        else
        {
            $bucketlist_item->category_id = null;
        }

        if(!$bucketlist_item->save())
        {
            // Log error
            Log::error('Failed to update Bucketlist item.', [
                'bucketlist_item' => $bucketlist_item->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to update the bucketlist item, please try again.'
            ]);
        }

        return redirect()->route('bucketlist.view.details', ['bucketlist_item' => $bucketlist_item->uuid]);
    }

    public function editCategories(Request $request)
    {
        // Get users categories
        $categories = $request->user()->bucketlistCategories()->get();

        // Return edit view
        return view('bucketlist.categories')->with([
            'categories' => $categories,
        ]);
    }

    public function storeCategory(StoreCategoryRequest $request)
    {
        // Create category
        $category = new BucketlistCategory([
            'name' => $request->get('name'),
            'user_id' => $request->user()->id,
        ]);

        // Save/log errors
        if(!$category->save())
        {
            Log::error('Failed to save bucketlist category', $category->toArray());
            return redirect()->back();
        }

        return redirect()->route('bucketlist.edit.categories');
    }

    public function destroyCategory(Request $request, BucketlistCategory $category)
    {
        // Remove category from items
        BucketlistItem::where('user_id', $request->user()->id)->where('category_id', $category->id)->update(['category_id' => null]);

        // Delete category
        if(!$category->delete())
        {
            Log::error('Failed to delete bucketlist category', $category->toArray());
            return redirect()->back();
        }

        return redirect()->route('bucketlist.edit.categories');
    }

    public function markCompleted(BucketlistItem $bucketlist_item, $view_details = false)
    {
        $bucketlist_item->completed = true;

        // Todo: need to complete out any associated ad hoc goal action items

        if(!$bucketlist_item->save())
        {
            // Log error
            Log::error('Failed to mark bucketlist item completed', ['uuid' => $bucketlist_item->uuid]);
            return redirect()->back();
        }

        if($view_details)
        {
            return redirect()->route('bucketlist.view.details', ['bucketlist_item' => $bucketlist_item->uuid]);
        }

        return redirect()->route('bucketlist');
    }

    public function markIncomplete(BucketlistItem $bucketlist_item, $view_details = false)
    {
        $bucketlist_item->completed = false;

        // Todo: need to incomplete out any associated ad hoc goal action items

        if(!$bucketlist_item->save())
        {
            // Log error
            Log::error('Failed to mark bucketlist item incomplete', ['uuid' => $bucketlist_item->uuid]);
            return redirect()->back();
        }

        if($view_details)
        {
            return redirect()->route('bucketlist.view.details', ['bucketlist_item' => $bucketlist_item->uuid]);
        }

        return redirect()->route('bucketlist');
    }

    public function destroy(BucketlistItem $bucketlist_item)
    {
        if(!$bucketlist_item->delete())
        {
            Log::error('Failed to delete bucketlist item', $bucketlist_item->toArray());
            return redirect()->back();
        }

        return redirect()->route('bucketlist');
    }
}
