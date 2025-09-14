<?php

namespace App\Services\Contracts;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TaskServiceInterface
{
    /**
     * Get all tasks for a user with optional filters
     *
     * @param User $user
     * @param array $filters
     * @return Collection
     */
    public function getUserTasks(User $user, array $filters = []): Collection;

    /**
     * Create a new task for a user
     *
     * @param User $user
     * @param array $data
     * @return Task
     */
    public function createTask(User $user, array $data): Task;

    /**
     * Get a specific task for a user
     *
     * @param int $taskId
     * @param User $user
     * @return Task|null
     */
    public function getTaskForUser(int $taskId, User $user): ?Task;

    /**
     * Update a task
     *
     * @param Task $task
     * @param User $user
     * @param array $data
     * @return Task
     * @throws \Exception
     */
    public function updateTask(Task $task, User $user, array $data): Task;

    /**
     * Delete a task
     *
     * @param Task $task
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteTask(Task $task, User $user): bool;

    /**
     * Get user task statistics
     *
     * @param User $user
     * @return array
     */
    public function getUserTaskStatistics(User $user): array;
}
