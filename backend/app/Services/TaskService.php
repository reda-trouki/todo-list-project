<?php

namespace App\Services;

use App\Events\TaskCreated;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\Contracts\TaskServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

/**
 * Task service implementation following SOLID principles
 *
 * Single Responsibility: Handles business logic for tasks
 * Open/Closed: Can be extended without modification
 * Liskov Substitution: Can be replaced by any TaskServiceInterface implementation
 * Interface Segregation: Uses focused interfaces
 * Dependency Inversion: Depends on abstraction (TaskRepositoryInterface)
 */
class TaskService implements TaskServiceInterface
{
    protected TaskRepositoryInterface $taskRepository;

    /**
     * Constructor - Dependency injection following DI principle
     *
     * @param TaskRepositoryInterface $taskRepository
     */
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get all tasks for a user with optional filters
     *
     * @param User $user
     * @param array $filters
     * @return Collection
     */
    public function getUserTasks(User $user, array $filters = []): Collection
    {
        return $this->taskRepository->getTasksByUser($user->id, $filters);
    }

    /**
     * Create a new task for a user
     *
     * Business rules:
     * - User must be authenticated
     * - Title is required
     * - Due date must be in the future or today
     *
     * @param User $user
     * @param array $data
     * @return Task
     */
    public function createTask(User $user, array $data): Task
    {
        // Apply business logic validation
        $this->validateTaskData($data);

        // Set default values following business rules
        $taskData = array_merge([
            'status' => 'pending',
            'priority' => 'medium',
            'user_id' => $user->id,
        ], $data);

        // Create the task
        $task = $this->taskRepository->create($taskData);

        // Broadcast event for real-time notifications
        \Log::info('Broadcasting TaskCreated event for task: ' . $task->id . ' to user: ' . $user->id);
        broadcast(new TaskCreated($task, $user));
        \Log::info('TaskCreated event broadcast completed');

        return $task;
    }

    /**
     * Get a specific task for a user
     *
     * @param int $taskId
     * @param User $user
     * @return Task|null
     */
    public function getTaskForUser(int $taskId, User $user): ?Task
    {
        return $this->taskRepository->findTaskForUser($taskId, $user->id);
    }

    /**
     * Update a task
     *
     * @param Task $task
     * @param User $user
     * @param array $data
     * @return Task
     * @throws Exception
     */
    public function updateTask(Task $task, User $user, array $data): Task
    {
        // Check if user can access this task (created by them or assigned to them)
        $this->verifyTaskAccess($task, $user);

        // Check specific permissions for different update types
        if (isset($data['status'])) {
            $this->verifyStatusChangePermission($task, $user);
        }

        // Only task creator can modify assignment, title, description, priority, due_date
        if (array_intersect(['title', 'description', 'priority', 'due_date', 'assigned_to'], array_keys($data))) {
            $this->verifyTaskOwnership($task, $user);
        }

        // Apply business logic validation for updates
        $this->validateTaskData($data, true);

        // Filter only allowed fields for security
        $allowedFields = ['title', 'description', 'status', 'priority', 'due_date', 'assigned_to'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        return $this->taskRepository->update($task, $updateData);
    }

    /**
     * Delete a task
     *
     * @param Task $task
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function deleteTask(Task $task, User $user): bool
    {
        // Verify ownership
        $this->verifyTaskOwnership($task, $user);

        return $this->taskRepository->delete($task);
    }

    /**
     * Get user task statistics
     *
     * @param User $user
     * @return array
     */
    public function getUserTaskStatistics(User $user): array
    {
        $totalTasks = $this->taskRepository->getTasksCountByUser($user->id);
        $tasksByStatus = $this->taskRepository->getTasksCountByStatus($user->id);
        $overdueTasks = $this->taskRepository->getOverdueTasks($user->id);

        return [
            'total_tasks' => $totalTasks,
            'pending_tasks' => $tasksByStatus['pending'] ?? 0,
            'in_progress_tasks' => $tasksByStatus['in_progress'] ?? 0,
            'completed_tasks' => $tasksByStatus['completed'] ?? 0,
            'overdue_tasks' => $overdueTasks->count(),
            'completion_rate' => $totalTasks > 0
                ? round((($tasksByStatus['completed'] ?? 0) / $totalTasks) * 100, 2)
                : 0,
        ];
    }

    /**
     * Validate task data according to business rules
     *
     * @param array $data
     * @param bool $isUpdate
     * @throws Exception
     */
    private function validateTaskData(array $data, bool $isUpdate = false): void
    {
        // For new tasks, title is required
        if (!$isUpdate && empty($data['title'])) {
            throw new Exception('Task title is required');
        }

        // Due date must be today or in the future
        if (isset($data['due_date']) && !empty($data['due_date'])) {
            $dueDate = date('Y-m-d', strtotime($data['due_date']));
            $today = date('Y-m-d');

            if ($dueDate < $today) {
                throw new Exception('Due date must be today or in the future');
            }
        }

        // Validate status values
        if (isset($data['status']) && !in_array($data['status'], ['pending', 'in_progress', 'completed'])) {
            throw new Exception('Invalid status value');
        }

        // Validate priority values
        if (isset($data['priority']) && !in_array($data['priority'], ['low', 'medium', 'high'])) {
            throw new Exception('Invalid priority value');
        }
    }

    /**
     * Verify that the task belongs to the user
     *
     * @param Task $task
     * @param User $user
     * @throws Exception
     */
    private function verifyTaskOwnership(Task $task, User $user): void
    {
        if ($task->user_id !== $user->id) {
            throw new Exception('You do not have permission to access this task');
        }
    }

    /**
     * Verify that the user can access this task (created by them or assigned to them)
     *
     * @param Task $task
     * @param User $user
     * @throws Exception
     */
    private function verifyTaskAccess(Task $task, User $user): void
    {
        if ($task->user_id !== $user->id && $task->assigned_to !== $user->id) {
            throw new Exception('You do not have permission to access this task');
        }
    }

    /**
     * Verify that the user can change the status of this task
     * Only assigned user or task creator can change status
     *
     * @param Task $task
     * @param User $user
     * @throws Exception
     */
    private function verifyStatusChangePermission(Task $task, User $user): void
    {
        // Task creator can always change status
        if ($task->user_id === $user->id) {
            return;
        }

        // Assigned user can change status
        if ($task->assigned_to === $user->id) {
            return;
        }

        throw new Exception('Only the assigned user can change the task status');
    }
}
