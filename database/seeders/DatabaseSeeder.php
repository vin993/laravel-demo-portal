<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Industry;
use App\Models\Dealer;
use App\Models\UserDetail;
use App\Models\Company;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::factory()->create(['name' => 'Admin']);
        $userRole = Role::factory()->create(['name' => 'User']);

        // Create random companies
        // Company::factory(5)->create();

        $companyNames = [
            'Midwest Equipment Solutions',
            'Power Systems Inc.',
            'Industrial Equipment Supply',
            'Agricultural Machinery Co.',
            'Marine Power Systems',
            'Heavy Equipment Specialists',
            'Farm Equipment Direct',
            'Energy Solutions Group',
            'Machinery Systems Ltd.',
            'Power Generation Tech'
        ];

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_approved' => true,
            'is_active' => true,
            'approved_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        // Create specific dealers
        $dealerNames = [
            'Ford Power Products',
            'Funk Drivetrain Components',
            'John DeereWilliam',
            'Kohler',
            'Yanmar'
        ];

        $dealers = collect();
        foreach ($dealerNames as $name) {
            $dealers->push(Dealer::create(['name' => $name]));
        }

        // Create specific industries
        $industryData = [
            'AG Power' => 'Keeping your operation growing even during your most crucial moments.',
            'DriveTrain Components' => 'John Deere and Funk™ drivetrain components are built in a modular approach for versatility
and quick delivery.',
            'Generator Drive Solutions' => 'Demo App that provides prime and standby power for a wide variety of critical applications.',
            'Industrial Diesel Systems' => 'Discover quality systems with varying levels of efficiency, capability, and affordability
to fit your application.',
            'Marine' => 'Dive into Marine propulsion, generators, and auxiliary systems with unmatched endurance and performance to
fit your operation.',
            'Repower' => 'Reliable Repower. Opting for a repower solution is a great way to extend the life of your equipment without
putting too much strain on your budget.'
        ];

        $industries = collect();
        foreach ($industryData as $name => $description) {
            $industries->push(Industry::create([
                'name' => $name,
                'description' => $description
            ]));
        }

        // Create test user
        // $testUser = User::create([
        //     'name' => 'Test User',
        //     'email' => 'test@email.com',
        //     'email_verified_at' => now(),
        //     'password' => bcrypt('password'),
        //     'role_id' => $userRole->id,
        //     'is_approved' => true,
        //     'is_active' => true,
        //     'last_login_at' => Carbon::createFromTimestamp(rand(strtotime('-1 year'), strtotime('now'))),
        //     'approved_at' => now(),
        //     'approved_by' => $admin->id,
        //     'remember_token' => Str::random(10),
        // ]);

        // Create regular users
        // $users = User::factory(20)->create([
        //     'role_id' => $userRole->id,
        //     'password' => bcrypt('password'),
        //     'approved_by' => $admin->id,
        // ]);

        // Create user details for admin and test user

        UserDetail::factory()->create([
            'user_id' => $admin->id,
            'company_name' => $companyNames[array_rand($companyNames)]
        ]);

        // UserDetail::factory()->create([
        //     'user_id' => $testUser->id,
        //     'company_name' => $companyNames[array_rand($companyNames)]
        // ]);

        // Create details and relationships for regular users
        // $users->each(function ($user) use ($industries, $dealers, $companyNames) {
        //     UserDetail::factory()->create([
        //         'user_id' => $user->id,
        //         'company_name' => $companyNames[array_rand($companyNames)]
        //     ]);


        //     // Attach random industries (1-3) to each user
        //     $user->industries()->attach(
        //         $industries->random(rand(1, 3))->pluck('id')->toArray()
        //     );

        //     // Attach random dealers (0-2) to each user
        //     $user->dealers()->attach(
        //         $dealers->random(rand(0, 2))->pluck('id')->toArray()
        //     );
        // });
    }
}
