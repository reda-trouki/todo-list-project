<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Task repository implementation following Repository pattern
 *
 * This class handles all database operations related to tasks,
 * providing a clean abstraction layer between the business logic
 * and data access layer.
 */
class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get all tasks for a specific user with optional filters
     * This includes both tasks created by the user and tasks assigned to the user
     *
     * @param int $userId
     * @param array $filters Available filters: status, priority
     * @return Collection
     */
    public function getTasksByUser(int $userId, array $filters = []): Collection
    {
        $query = Task::with(['user:id,full_name as name,email', 'assignedTo:id,full_name as name,email'])
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)  // Tasks created by user
                  ->orWhere('assigned_to', $userId); // Tasks assigned to user
            });

        // Apply status filter if provided
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply priority filter if provided
        if (isset($filters['priority']) && !empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Apply sorting - default by created_at desc
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $query->orderBy($sortBy, $sortDirection);

        return $query->get();
    }

    /**
     * Find a task by ID for a specific user
     * This includes both tasks created by the user and tasks assigned to the user
     *
     * @param int $taskId
     * @param int $userId
     * @return Task|null
     */
    public function findTaskForUser(int $taskId, int $userId): ?Task
    {
        return Task::with(['user:id,full_name as name,email', 'assignedTo:id,full_name as name,email'])
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)  // Tasks created by user
                  ->orWhere('assigned_to', $userId); // Tasks assigned to user
            })
            ->find($taskId);
    }

    /**
     * Create a new task
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        $task = Task::create($data);
        return $task->load(['user:id,full_name as name,email', 'assignedTo:id,full_name as name,email']);
    }

    /**
     * Update a task
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->load(['user:id,full_name as name,email', 'assignedTo:id,full_name as name,email']);

        // Return fresh instance to get updated values
        return $task->fresh();
    }

    /**
     * Delete a task
     *
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Get tasks count by user
     *
     * @param int $userId
     * @return int
     */
    public function getTasksCountByUser(int $userId): int
    {
        return Task::forUser($userId)->count();
    }

    /**
     * Get tasks count by status for a user
     *
     * @param int $userId
     * @return array
     */
    public function getTasksCountByStatus(int $userId): array
    {
        return Task::forUser($userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get overdue tasks for a user
     *
     * @param int $userId
     * @return Collection
     */
    public function getOverdueTasks(int $userId): Collection
    {
        return Task::forUser($userId)
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed'])
            ->orderBy('due_date')
            ->get();
    }
}
