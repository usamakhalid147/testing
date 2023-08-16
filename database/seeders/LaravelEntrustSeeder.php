<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaravelEntrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {
        $this->command->info('Truncating Roles, Permissions and Users tables');
        $this->truncateEntrustTables();

        $config = config('entrust_seeder.role_structure');
        $userRoles = config('entrust_seeder.user_roles');
        $mapPermission = collect(config('entrust_seeder.permissions_map'));

        foreach ($config as $key => $modules) {

            $permissions = [];

            $this->command->info('Creating Role '. strtoupper($key));

            // Reading role permission modules
            foreach ($modules as $module => $value) {

                foreach (explode(',', $value) as $p => $perm) {

                    $permissionValue = $mapPermission->get($perm);

                    $permission = \App\Models\Permission::firstOrCreate([
                        'name' => $permissionValue . '-' . $module,
                        'user_type' => $key,
                        'display_name' => ucfirst($permissionValue) . ' ' . ucwords(str_replace('_', ' ', $module)),
                        'description' => ucfirst($permissionValue) . ' ' . ucwords(str_replace('_', ' ', $module)),
                    ])->id;

                    if ($key == 'admin') {
                        $permissions[] = $permission;
                    }
                }
            }

            if($key == 'admin') {
                // Create a new role
                $role = \App\Models\Role::create([
                    'name' => $key,
                    'user_type' => $key,
                    'display_name' => ucwords(str_replace('_', ' ', $key)),
                    'description' => ucwords(str_replace('_', ' ', $key))
                ]);

                // Attach all permissions to the role
                $role->permissions()->sync($permissions);

                if(isset($userRoles[$key])) {
                    $this->command->info("Creating '{$key}' users");

                    $role_users  = $userRoles[$key];

                    foreach ($role_users as $role_user) {
                        $user = \App\Models\Admin::create($role_user);
                        $user->attachRole($role);
                    }
                }
            }
        }
    }

    /**
     * Truncates all the entrust tables and the users table
     *
     * @return    void
     */
    public function truncateEntrustTables()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('permission_role')->truncate();
        DB::table('role_user')->truncate();
        DB::table('admins')->truncate();

        \App\Models\Role::truncate();
        \App\Models\Permission::truncate();

        Schema::enableForeignKeyConstraints();
    }
}