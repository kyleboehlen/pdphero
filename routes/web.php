<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AffirmationsController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\HabitsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ToDoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth
Auth::routes(['verify' => true]);

// Root route, controls whether or not user gets sent to about or home
Route::get('/', [HomeController::class, 'index'])->name('root');

// Main about page
Route::get('about', [AboutController::class, 'index'])->name('about');

// Home route
Route::group(['prefix' => 'home', 'middleware' => ['auth', 'verified']], function(){
    // View tools page
    Route::get('/', [HomeController::class, 'home'])->name('home');

    // Edit/update routes
    Route::get('edit', [HomeController::class, 'edit'])->name('home.edit');
    Route::post('update', [HomeController::class, 'update'])->name('home.update');
});

// Journal
Route::prefix('journal')->group(function(){
    // Root
    Route::get('/', [ProfileController::class, 'index'])->name('journal');
});

// Goals
Route::prefix('goals')->group(function(){
    // Root
    Route::get('/', [GoalController::class, 'index'])->name('goals');

    // Toggle Completed routes
    Route::prefix('toggle-completed')->group(function(){
        Route::post('goal/{goal}', [GoalController::class, 'toggleCompletedGoal'])->name('goals.toggle-completed.goal');
        Route::post('action-item/{action_item}', [GoalController::class, 'toggleCompletedActionItem'])->name('goals.toggle-completed.action-item');
    });

    // View routes
    Route::prefix('view')->group(function(){
        Route::get('goal/{goal}', [GoalController::class, 'viewGoal'])->name('goals.view.goal');
        Route::get('action-item/{action_item}', [GoalController::class, 'viewActionItem'])->name('goals.view.action-item');
    });

    // Create routes
    Route::prefix('create')->group(function(){
        Route::get('goal', [GoalController::class, 'createGoal'])->name('goals.create.goal');
        Route::get('action-item', [GoalController::class, 'createActionItem'])->name('goals.create.action-item');
    });

    // Store routes
    Route::prefix('store')->group(function(){
        Route::post('goal', [GoalController::class, 'storeGoal'])->name('goals.store.goal');
        Route::post('action-item', [GoalController::class, 'storeActionItem'])->name('goals.store.action-item');
    });
    
    // Edit routes
    Route::prefix('edit')->group(function(){
        Route::get('goal/{goal}', [GoalController::class, 'editGoal'])->name('goals.edit.goal');
        Route::get('action-item/{action_item}', [GoalController::class, 'editActionItem'])->name('goals.edit.action-item');
    });

    // Update routes
    Route::prefix('update')->group(function(){
        Route::post('goal/{goal}', [GoalController::class, 'updateGoal'])->name('goals.update.goal');
        Route::post('action-item/{action_item}', [GoalController::class, 'updateActionItem'])->name('goals.update.action-item');
    });

    // Destroy routes
    Route::prefix('destroy')->group(function(){
        Route::post('goal/{goal}', [GoalController::class, 'destroyGoal'])->name('goals.destroy.goal');
        Route::post('action-item/{action_item}', [GoalController::class, 'updateActionItem'])->name('goals.destroy.action-item');
    });
});

// Habits
Route::prefix('habits')->group(function(){
    // Root
    Route::get('/', [HabitsController::class, 'index'])->name('habits');

    // View details/history
    Route::get('view/{habit}', [HabitsController::class, 'view'])->name('habits.view');

    // View the habits color guide
    Route::get('colors', [HabitsController::class, 'colorGuide'])->name('habits.colors');

    // Add form/add routes
    Route::get('create', [HabitsController::class, 'create'])->name('habits.create');
    Route::post('store', [HabitsController::class, 'store'])->name('habits.store');

    // Edit/Update routes
    Route::get('edit/{habit}', [HabitsController::class, 'edit'])->name('habits.edit');
    Route::post('update/{habit}', [HabitsController::class, 'update'])->name('habits.update');

    // Delete
    Route::post('destroy/{habit}', [HabitsController::class, 'destroy'])->name('habits.destroy');

    // Update habit history
    Route::post('history/{habit}', [HabitsController::class, 'history'])->name('habits.history');
});

// Affirmations
Route::prefix('affirmations')->group(function(){
    // Index
    Route::get('/', [AffirmationsController::class, 'index'])->name('affirmations');

    // Add form/add route
    Route::get('create', [AffirmationsController::class, 'create'])->name('affirmations.create');
    Route::post('store', [AffirmationsController::class, 'store'])->name('affirmations.store');

    // Show, edit, update, and destroy routes for
    // individual affirmations
    Route::get('show/{affirmation}', [AffirmationsController::class, 'show'])->name('affirmations.show');
    Route::get('edit/{affirmation}', [AffirmationsController::class, 'edit'])->name('affirmations.edit');
    Route::post('update/{affirmation}', [AffirmationsController::class, 'update'])->name('affirmations.update');
    Route::post('destroy/{affirmation}', [AffirmationsController::class, 'destroy'])->name('affirmations.destroy');

    // This route handles verifying and making note that the affirmations list was read
    Route::post('read', [AffirmationsController::class, 'checkRead'])->name('affirmations.read.check');
    Route::get('read', [AffirmationsController::class, 'showRead'])->name('affirmations.read.show');
});

// Profile
Route::prefix('profile')->group(function(){
    // Root
    Route::get('/', [ProfileController::class, 'index'])->name('profile');

    // Edit routes
    Route::prefix('edit')->group(function(){
        // Show edit settings page
        Route::get('settings', [ProfileController::class, 'editSettings'])->name('profile.edit.settings');

        // Show edit name page
        Route::get('name', [ProfileController::class, 'editName'])->name('profile.edit.name');

        // Show edit nutshell page
        Route::get('nutshell', [ProfileController::class, 'editNutshell'])->name('profile.edit.nutshell');

        // Show edit values page
        Route::get('values', [ProfileController::class, 'editValues'])->name('profile.edit.values');

        // Show edit rules page
        Route::get('rules', [ProfileController::class, 'editRules'])->name('profile.edit.rules');

        // Show manage memebership page
        Route::get('membership', [ProfileController::class, 'editMembership'])->name('profile.edit.membership');
    });

    // Update routes
    Route::prefix('update')->group(function(){
        // Update settings route
        Route::post('settings/{id}', [ProfileController::class, 'updateSettings'])->name('profile.update.settings');

        // Update routes for profile-picture, name, values, nutshell, rules
        Route::post('name', [ProfileController::class, 'updateName'])->name('profile.update.name');
        Route::post('values', [ProfileController::class, 'updateValues'])->name('profile.update.values');
        Route::post('nutshell', [ProfileController::class, 'updateNutshell'])->name('profile.update.nutshell');
        Route::post('rules', [ProfileController::class, 'updateRules'])->name('profile.update.rules');

        // Profile picture upload throttled
        Route::middleware(['throttle:profile-pictures'])->group(function(){
            Route::post('picture', [ProfileController::class, 'updatePicture'])->name('profile.update.picture');
        });
    });

    // Delete route
    Route::prefix('destroy')->group(function(){
        // Value/rule
        Route::post('value', [ProfileController::class, 'destroyValue'])->name('profile.destroy.value');
        Route::post('rule', [ProfileController::class, 'destroyRule'])->name('profile.destroy.rule');

        // Sets all settings to default
        Route::post('settings', [ProfileController::class, 'destroySettings'])->name('profile.destroy.settings');
    });
});

// To-Do routes
Route::prefix('todo')->group(function(){
    // Root
    Route::get('/', [ToDoController::class, 'index'])->name('todo.list');

    // View the todo priority colors guide
    Route::get('colors', [ToDoController::class, 'colorGuide'])->name('todo.colors');

    // Show the create to do item form
    Route::get('create', [ToDoController::class, 'create'])->name('todo.create');

    // Submit the create to do item form
    Route::post('store', [ToDoController::class, 'store'])->name('todo.store');

    // Show the edit to do item form
    Route::get('edit/{todo}', [ToDoController::class, 'edit'])->name('todo.edit');

    // Submit the edit to do item form
    Route::post('update/{todo}', [ToDoController::class, 'update'])->name('todo.update');

    // Delete a to do item
    Route::post('destroy/{todo}', [ToDoController::class, 'destroy'])->name('todo.destroy');

    // Toggle a to do item's completed status
    Route::post('toggle-completed/{todo}', [ToDoController::class, 'toggleCompleted'])->name('todo.toggle-completed');
});