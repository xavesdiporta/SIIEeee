<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'add_article',
            'remove_article',
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Permission::create(['name' => $role]);
        }
    }
}
