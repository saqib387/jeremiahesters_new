<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('data_types')) {
            return;
        }

        DB::table('data_types')
            ->where('slug', 'users')
            ->where('name', 'users1')
            ->update(['name' => 'users']);

        if (!Schema::hasTable('permissions')) {
            return;
        }

        $now = now();
        $permissionKeys = [
            'browse_users',
            'read_users',
            'edit_users',
            'add_users',
            'delete_users',
        ];

        foreach ($permissionKeys as $key) {
            DB::table('permissions')->updateOrInsert(
                ['key' => $key],
                [
                    'table_name' => 'users',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        if (!Schema::hasTable('roles') || !Schema::hasTable('permission_role')) {
            return;
        }

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');

        if (!$adminRoleId) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('key', $permissionKeys)
            ->pluck('id');

        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->updateOrInsert([
                'permission_id' => $permissionId,
                'role_id' => $adminRoleId,
            ]);
        }
    }

    public function down()
    {
        if (!Schema::hasTable('data_types')) {
            return;
        }

        DB::table('data_types')
            ->where('slug', 'users')
            ->where('name', 'users')
            ->update(['name' => 'users1']);
    }
};
