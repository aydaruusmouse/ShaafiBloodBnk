<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
	public function run(): void
	{
		// Roles
		$roles = [
			['name' => 'super_admin', 'description' => 'System Super Admin'],
			['name' => 'hospital_admin', 'description' => 'Hospital Admin'],
			['name' => 'doctor', 'description' => 'Doctor'],
			['name' => 'nurse', 'description' => 'Nurse'],
			['name' => 'reception', 'description' => 'Reception'],
			['name' => 'lab', 'description' => 'Laboratory'],
			['name' => 'finance', 'description' => 'Finance'],
			['name' => 'read_only', 'description' => 'Read Only'],
		];
		foreach ($roles as $r) {
			Role::firstOrCreate(['name' => $r['name']], ['description' => $r['description']]);
		}

		// Super Admin user
		$superRole = Role::where('name', 'super_admin')->first();
		User::firstOrCreate(
			['email' => 'superadmin@example.com'],
			[
				'name' => 'Super Admin',
				'role_id' => $superRole?->id,
				'password' => Hash::make('ChangeMe123!'),
				'phone' => null,
				'address' => null,
			]
		);
	}
}
