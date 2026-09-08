<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectList;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private ProjectList $list;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->project = Project::create([
            'user_id'      => $this->user->id,
            'project_name' => 'Demo Project',
            'slug'         => 'demo-project',
        ]);

        $this->list = ProjectList::create([
            'project_id' => $this->project->project_id,
            'list_name'  => 'Backlog',
        ]);
    }

    public function test_authenticated_user_can_create_task_in_list(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/lists/{$this->list->project_list_id}/tasks", [
            'task_title'       => 'Initial Task',
            'task_description' => 'Test description',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.task_title', 'Initial Task')
            ->assertJsonPath('data.project_list_id', $this->list->project_list_id);

        $this->assertDatabaseHas('tasks', [
            'project_list_id' => $this->list->project_list_id,
            'task_title'      => 'Initial Task',
        ]);
    }

    public function test_cannot_create_task_in_another_users_list(): void
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $otherProject = Project::create([
            'user_id'      => $otherUser->id,
            'project_name' => 'Other Project',
            'slug'         => 'other-project',
        ]);
        $otherList = ProjectList::create([
            'project_id' => $otherProject->project_id,
            'list_name'  => 'Other Backlog',
        ]);

        $response = $this->postJson("/api/lists/{$otherList->project_list_id}/tasks", [
            'task_title' => 'Sneaky Task',
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Project list not found or unauthorized']);
    }

    public function test_authenticated_user_can_update_task(): void
    {
        Sanctum::actingAs($this->user);

        $task = Task::create([
            'project_list_id'  => $this->list->project_list_id,
            'task_title'       => 'Old Title',
            'task_description' => 'Old Description',
        ]);

        $response = $this->postJson("/api/tasks/{$task->task_id}", [
            'task_title'       => 'New Title',
            'task_description' => 'Updated Description',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.task_title', 'New Title')
            ->assertJsonPath('data.task_description', 'Updated Description');

        $this->assertDatabaseHas('tasks', [
            'task_id'    => $task->task_id,
            'task_title' => 'New Title',
        ]);
    }

    public function test_user_can_update_task_using_put(): void
    {
        Sanctum::actingAs($this->user);

        $task = Task::create([
            'project_list_id'  => $this->list->project_list_id,
            'task_title'       => 'Old Title',
        ]);

        $response = $this->putJson("/api/tasks/{$task->task_id}", [
            'task_title' => 'Updated via PUT',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.task_title', 'Updated via PUT');
    }

    public function test_cannot_move_task_to_list_belonging_to_another_user(): void
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $otherProject = Project::create([
            'user_id'      => $otherUser->id,
            'project_name' => 'Foreign Project',
            'slug'         => 'foreign-project',
        ]);
        $otherList = ProjectList::create([
            'project_id' => $otherProject->project_id,
            'list_name'  => 'Foreign List',
        ]);

        $task = Task::create([
            'project_list_id' => $this->list->project_list_id,
            'task_title'      => 'Task to move',
        ]);

        $response = $this->postJson("/api/tasks/{$task->task_id}", [
            'project_list_id' => $otherList->project_list_id,
        ]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'Target project list not found or unauthorized']);
    }

    public function test_authenticated_user_can_delete_task(): void
    {
        Sanctum::actingAs($this->user);

        $task = Task::create([
            'project_list_id' => $this->list->project_list_id,
            'task_title'      => 'Task to Delete',
        ]);

        $response = $this->deleteJson("/api/tasks/{$task->task_id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', [
            'task_id' => $task->task_id,
        ]);
    }

    public function test_cannot_delete_another_users_task(): void
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $otherProject = Project::create([
            'user_id'      => $otherUser->id,
            'project_name' => 'Foreign Project',
            'slug'         => 'foreign-project',
        ]);
        $otherList = ProjectList::create([
            'project_id' => $otherProject->project_id,
            'list_name'  => 'Foreign List',
        ]);
        $foreignTask = Task::create([
            'project_list_id' => $otherList->project_list_id,
            'task_title'      => 'Foreign Task',
        ]);

        $response = $this->deleteJson("/api/tasks/{$foreignTask->task_id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('tasks', ['task_id' => $foreignTask->task_id]);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->postJson("/api/lists/{$this->list->project_list_id}/tasks", [
            'task_title' => 'Unauthenticated Task',
        ]);

        $response->assertStatus(401);
    }
}
