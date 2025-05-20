<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceTask;
use App\Models\MaintenanceTaskType;
use App\Models\Vehicle;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class MaintenanceTaskController extends Controller
{
    public function addOrUpdateMaintainanceTask(Request $request)
    {
        try {
            // Validate that task_id exists in maintenance_task_types table if provided
            if ($request->has('task_id')) {
                $taskType = MaintenanceTaskType::find($request->task_id);
                if (!$taskType) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid maintenance task type ID provided',
                        'data' => (object)[]
                    ]);
                }
            }

            if ($request->has('car_id')) {
                $carType = Vehicle::find($request->car_id);
                if (!$carType) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid Car provided',
                        'data' => (object)[]
                    ]);
                }
            }

            $validator = Validator::make($request->all(), [
                'task_id' => 'nullable|integer',
                'car_id' => 'required|integer',
                'current_miles' => 'required|integer|min:0',
                'remind_me' => 'required|in:AT,AFTER',
                'remind_me_miles' => 'required|integer|min:0',
                'notification_type' => 'required|in:daily,weekly,monthly',
                'notification_time' => 'required|string',
                'note' => 'nullable|string',
                'last_service_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'data' => (object)[]
                ]);
            }

            // Check if remind_me_miles is greater than current_miles
            if ($request->remind_me_miles <= $request->current_miles) {
                return response()->json([
                    'status' => false,
                    'message' => 'Reminder miles must be greater than current miles',
                    'data' => (object)[]
                ]);
            }

            $user = JWTAuth::toUser(JWTAuth::getToken());

            // Optional: Check if updating by task_id
            $task = MaintenanceTask::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'task_id' => $request->task_id,
                    'car_id' => $request->car_id
                ],
                [
                    'current_miles' => $request->current_miles,
                    'remind_me' => $request->remind_me,
                    'remind_me_miles' => $request->remind_me_miles,
                    'notification_type' => $request->notification_type,
                    'notification_time' => $request->notification_time,
                    'last_service_id' => $request->last_service_id,
                    'note' => $request->note,
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Maintenance task saved successfully.',
                'data' => $task,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showMaintenanceTaskList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'car_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'data' => (object)[]
                ]);
            }

            $user = JWTAuth::toUser(JWTAuth::getToken());

            // Check if car exists
            $car = Vehicle::find($request->car_id);
            if (!$car) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Car provided',
                    'data' => (object)[]
                ]);
            }

            // Fetch maintenance tasks with related data where remind_me_miles > current_miles
            $maintenanceTasks = MaintenanceTask::where('user_id', $user->id)
                ->where('car_id', $request->car_id)
                ->whereRaw('remind_me_miles > current_miles')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Maintenance tasks fetched successfully',
                'data' => $maintenanceTasks
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => (object)[]
            ]);
        }
    }

    public function showMaintenanceHistory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'car_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'data' => (object)[]
                ]);
            }

            $user = JWTAuth::toUser(JWTAuth::getToken());

            // Check if car exists
            $car = Vehicle::find($request->car_id);
            if (!$car) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Car provided',
                    'data' => (object)[]
                ]);
            }

            // Fetch maintenance tasks with related data where current_miles >= remind_me_miles
            $maintenanceTasks = MaintenanceTask::where('user_id', $user->id)
                ->where('car_id', $request->car_id)
                ->whereRaw('current_miles >= remind_me_miles')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Maintenance history fetched successfully',
                'data' => $maintenanceTasks
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => (object)[]
            ]);
        }
    }

    public function deleteMaintenanceTask(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'data' => (object)[]
                ]);
            }

            $user = JWTAuth::toUser(JWTAuth::getToken());

            // Find the maintenance task
            $maintenanceTask = MaintenanceTask::where('id', $request->task_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$maintenanceTask) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maintenance task not found or you do not have permission to delete it',
                    'data' => (object)[]
                ]);
            }

            // Delete the maintenance task
            $maintenanceTask->delete();

            return response()->json([
                'status' => true,
                'message' => 'Maintenance task deleted successfully',
                'data' => (object)[]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => (object)[]
            ]);
        }
    }

    public function updateMaintenanceTaskStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer',
                'status' => 'required|in:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'data' => (object)[]
                ]);
            }

            $user = JWTAuth::toUser(JWTAuth::getToken());

            // Find the maintenance task
            $maintenanceTask = MaintenanceTask::where('id', $request->task_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$maintenanceTask) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maintenance task not found or you do not have permission to update it',
                    'data' => (object)[]
                ]);
            }

            // If status is 1 (complete), update current_miles to remind_me_miles
            if ($request->status == 1) {
                $maintenanceTask->current_miles = $maintenanceTask->remind_me_miles;
                $maintenanceTask->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Maintenance task status updated successfully',
                'data' => $maintenanceTask
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => (object)[]
            ]);
        }
    }
}