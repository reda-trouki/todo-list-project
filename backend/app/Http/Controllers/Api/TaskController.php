<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\Contracts\TaskServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class TaskController extends Controller
{
    protected TaskServiceInterface $taskService;

    /**
     * Constructor - Inject the task service
     *
     * @param TaskServiceInterface $taskService
     */
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * Display a listing of the user's tasks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        // Build filters array from request
        $filters = [];
        if ($request->has('status')) {
            $filters['status'] = $request->status;
        }
        if ($request->has('priority')) {
            $filters['priority'] = $request->priority;
        }

        $tasks = $this->taskService->getUserTasks($user, $filters);

        return response()->json([
            'success' => true,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a newly created task in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            \Log::error('Task validation failed:', [
                'errors' => $validator->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('api')->user();

        \Log::info('Creating task with data:', $request->all());

        try {
            $task = $this->taskService->createTask($user, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'task' => $task,
            ], 201);
        } catch (Exception $e) {
            \Log::error('Task creation failed:', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified task.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $task = $this->taskService->getTaskForUser((int)$id, $user);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or you do not have permission to view this task',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'task' => $task,
        ]);
    }

    /**
     * Update the specified task in storage.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $task = $this->taskService->getTaskForUser((int)$id, $user);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or you do not have permission to update this task',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updatedTask = $this->taskService->updateTask($task, $user, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'task' => $updatedTask,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified task from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $task = $this->taskService->getTaskForUser((int)$id, $user);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or you do not have permission to delete this task',
            ], 404);
        }

        try {
            $this->taskService->deleteTask($task, $user);

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get all users for task assignment.
     *
     * @return JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        try {
            $users = \App\Models\User::select('id', 'full_name as name', 'email')->get();

            return response()->json([
                'success' => true,
                'users' => $users,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
            ], 500);
        }
    }
}
