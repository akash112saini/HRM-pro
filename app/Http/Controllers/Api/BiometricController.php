<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BiometricDevice;
use App\Services\AttendanceCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BiometricController extends Controller
{
    protected AttendanceCalculatorService $attendanceService;

    public function __construct(AttendanceCalculatorService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Receive punch data from biometric devices.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function push(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string',
                'employee_code' => 'required|string',
                'timestamp' => 'required|date_format:Y-m-d H:i:s',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Authenticate device using Bearer token
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication token required',
                ], 401);
            }

            // Verify device token
            $device = BiometricDevice::where('device_id', $request->device_id)
                ->where('is_active', true)
                ->first();

            if (!$device) {
                Log::warning("Biometric push from unknown device: {$request->device_id}");
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found or inactive',
                ], 404);
            }

            // Verify token (in production, use proper encryption/hashing)
            if (!hash_equals($device->api_token, $token)) {
                Log::warning("Invalid token for device: {$request->device_id}");
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid authentication token',
                ], 401);
            }

            // Process attendance data
            $attendance = $this->attendanceService->syncBiometricData([
                'device_id' => $request->device_id,
                'employee_code' => $request->employee_code,
                'timestamp' => $request->timestamp,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            Log::info("Biometric punch recorded", [
                'device_id' => $request->device_id,
                'employee_code' => $request->employee_code,
                'attendance_id' => $attendance->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'employee_name' => $attendance->employee->full_name,
                    'punch_in' => $attendance->punch_in?->format('Y-m-d H:i:s'),
                    'punch_out' => $attendance->punch_out?->format('Y-m-d H:i:s'),
                    'status' => $attendance->status,
                    'is_late' => $attendance->is_late,
                    'late_minutes' => $attendance->late_minutes,
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Employee not found for biometric punch", [
                'device_id' => $request->device_id,
                'employee_code' => $request->employee_code,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error("Biometric push error: " . $e->getMessage(), [
                'device_id' => $request->device_id,
                'employee_code' => $request->employee_code,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing attendance',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get device status and last sync time.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication token required',
                ], 401);
            }

            $device = BiometricDevice::where('device_id', $request->device_id)
                ->where('is_active', true)
                ->first();

            if (!$device || !hash_equals($device->api_token, $token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found or invalid token',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'device_id' => $device->device_id,
                    'device_name' => $device->device_name,
                    'location' => $device->location,
                    'is_active' => $device->is_active,
                    'last_sync_at' => $device->last_sync_at?->format('Y-m-d H:i:s'),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
