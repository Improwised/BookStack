<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // get Admin Role Id
        $adminRoleId = DB::table('roles')->where('display_name', '=', 'admin')->first()->id;

        // Create & attach new entity permissions
        $ops = ['Encrypt All','Encrypt Own'];
        foreach ($ops as $op) {
            $permissionId = DB::table('role_permissions')->insertGetId([
                'name'         => strtolower('Page') . '-' . strtolower(str_replace(' ', '-', $op)),
                'display_name' => $op . ' ' . 'Page' . 's',
                'created_at'   => Carbon::now()->toDateTimeString(),
                'updated_at'   => Carbon::now()->toDateTimeString(),
            ]);
            DB::table('permission_role')->insert([
                'role_id'       => $adminRoleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
