<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
class SuperAdminController extends Controller
{
    public function toggleAdminStatus($adminId)
    {
        try {

            $admin = User::whereHas('role', function ($q) {
                $q->where('name', 'Admin');
            })->findOrFail($adminId);


            $admin->is_active = !$admin->is_active;
            $admin->save();

            $status = $admin->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'is_active' => $admin->is_active,
                'message' => "Admin has been {$status} successfully."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createAdmin(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'is_active' => 'boolean'
            ]);


            $adminRole = Role::where('name', 'Admin')->first();

            if (!$adminRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin role not found in the system.'
                ], 500);
            }

            // Create the new admin user
            $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $adminRole->id,
                'is_active' => $request->has('is_active') ? true : false,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully!',
                'admin' => $admin
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin: ' . $e->getMessage()
            ], 500);
        }
    }
}
