<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role_id' => $superAdminRole->id,
            'is_approved' => true,
            'is_active' => true,
            'approved_at' => now(),
            'remember_token' => Str::random(10),
        ]);

       
        UserDetail::create([
            'user_id' => $superAdmin->id,
            'company_name' => 'Demo Company',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'phone' => '000-000-0000',
            'address' => 'System Administrator',
            'city' => 'System',
            'state' => 'Admin',
            'zip' => '00000'
        ]);

        // Update existing admin user to show it was approved by super admin
        $existingAdmin = User::where('email', 'admin@example.com')->first();
        if ($existingAdmin) {
            $existingAdmin->update(['approved_by' => $superAdmin->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        $superAdmin = User::whereHas('role', function($query) {
            $query->where('name', 'Super Admin');
        })->first();
        
        if ($superAdmin) {
          
            $superAdmin->userDetail()->delete();
            $superAdmin->delete();
        }


        Role::where('name', 'Super Admin')->delete();


        $existingAdmin = User::where('email', 'admin@example.com')->first();
        if ($existingAdmin) {
            $existingAdmin->update(['approved_by' => null]);
        }
    }
};