<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    /**
     * Get all tasks for a specific user
     *
     * @param int $userId
     * @param array $filters
     * @return Collection
     */
    public function getTasksByUser(int $userId, array $filters = []): Collection;

    /**
     * Find a task by ID for a specific user
     *
     * @param int $taskId
     * @param int $userId
     * @return Task|null
     */
    public function findTaskForUser(int $taskId, int $userId): ?Task;

    /**
     * Create a new task
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task;

    /**
     * Update a task
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task;

    /**
     * Delete a task
     *
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool;

    /**
     * Get tasks count by user
     *
     * @param int $userId
     * @return int
     */
    public function getTasksCountByUser(int $userId): int;

    /**
     * Get tasks count by status for a user
     *
     * @param int $userId
     * @return array
     */
    public function getTasksCountByStatus(int $userId): array;

    /**
     * Get overdue tasks for a user
     *
     * @param int $userId
     * @return Collection
     */
    public function getOverdueTasks(int $userId): Collection;
}
