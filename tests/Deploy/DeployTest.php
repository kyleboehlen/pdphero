<?php

namespace Tests\Deploy;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Artisan;
use Schema;
use Hash;
use DB;

// Models
use App\Models\ToDo\ToDoPriority;
use App\Models\ToDo\ToDoTypes;

class DeployTest extends TestCase
{
    /**
     * Migrate database and verify tables exsist
     *
     * @test
     */
    public function migrateTest()
    {
        // Migrate database
        Artisan::call('migrate');

        // Check if tables exsist
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertTrue(Schema::hasTable('migrations'));
        $this->assertTrue(Schema::hasTable('password_resets'));
        $this->assertTrue(Schema::hasTable('users'));
    }

    /**
     * Seed the database and verify categories seeded
     * 
     * @test
     */
    public function seedTest()
    {
        // Seed the database
        Artisan::call('db:seed');

        // Verify ToDo Priorities seeded
        $this->assertEquals(configArrayFromSeededCollection(ToDoPriority::all()), config('todo.priorities'));

        // Verify ToDo Types seeded
        $this->assertEquals(configArrayFromSeededCollection(ToDoTypes::all()), config('todo.types'));
    }
}
