<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('hospitals', function (Blueprint $table) {
			if (!Schema::hasColumn('hospitals', 'city')) $table->string('city')->nullable()->after('address');
			if (!Schema::hasColumn('hospitals', 'status')) $table->enum('status', ['active','inactive'])->default('active')->after('contact_person');
		});

		Schema::table('users', function (Blueprint $table) {
			if (!Schema::hasColumn('users', 'hospital_id')) $table->foreignId('hospital_id')->nullable()->constrained()->nullOnDelete()->after('id');
			if (!Schema::hasColumn('users', 'status')) $table->enum('status', ['active','inactive'])->default('active')->after('address');
			if (!Schema::hasColumn('users', 'last_login')) $table->timestamp('last_login')->nullable()->after('status');
		});

		// Adjust unique index on users.email to be tenant-scoped if possible
		$database = DB::getDatabaseName();
		$indexExists = DB::table('information_schema.STATISTICS')
			->where('TABLE_SCHEMA', $database)
			->where('TABLE_NAME', 'users')
			->where('INDEX_NAME', 'users_email_unique')
			->exists();
		if ($indexExists) {
			try { DB::statement('ALTER TABLE `users` DROP INDEX `users_email_unique`'); } catch (\Throwable $e) {}
		}
		// Create composite unique if not exists
		$compositeExists = DB::table('information_schema.STATISTICS')
			->where('TABLE_SCHEMA', $database)
			->where('TABLE_NAME', 'users')
			->where('INDEX_NAME', 'users_hospital_email_unique')
			->exists();
		if (!$compositeExists) {
			DB::statement('ALTER TABLE `users` ADD UNIQUE `users_hospital_email_unique` (`hospital_id`, `email`)');
		}

		$tenantTables = [
			'donors', 'patients', 'blood_bags', 'blood_requests', 'departments', 'transfusions', 'lab_tests', 'blood_inventory', 'sms_campaigns'
		];
		foreach ($tenantTables as $table) {
			if (Schema::hasTable($table)) {
				Schema::table($table, function (Blueprint $t) use ($table) {
					if (!Schema::hasColumn($table, 'hospital_id')) {
						$t->foreignId('hospital_id')->nullable()->constrained()->nullOnDelete();
					}
				});
			}
		}
	}

	public function down(): void
	{
		// Revert users composite unique safely
		$database = DB::getDatabaseName();
		$compositeExists = DB::table('information_schema.STATISTICS')
			->where('TABLE_SCHEMA', $database)
			->where('TABLE_NAME', 'users')
			->where('INDEX_NAME', 'users_hospital_email_unique')
			->exists();
		if ($compositeExists) {
			try { DB::statement('ALTER TABLE `users` DROP INDEX `users_hospital_email_unique`'); } catch (\Throwable $e) {}
		}

		Schema::table('users', function (Blueprint $table) {
			if (Schema::hasColumn('users', 'hospital_id')) $table->dropConstrainedForeignId('hospital_id');
			if (Schema::hasColumn('users', 'status')) $table->dropColumn('status');
			if (Schema::hasColumn('users', 'last_login')) $table->dropColumn('last_login');
		});

		$tenantTables = [
			'donors', 'patients', 'blood_bags', 'blood_requests', 'departments', 'transfusions', 'lab_tests', 'blood_inventory', 'sms_campaigns'
		];
		foreach ($tenantTables as $table) {
			if (Schema::hasTable($table)) {
				Schema::table($table, function (Blueprint $t) use ($table) {
					if (Schema::hasColumn($table, 'hospital_id')) {
						$t->dropConstrainedForeignId('hospital_id');
					}
				});
			}
		}

		Schema::table('hospitals', function (Blueprint $table) {
			if (Schema::hasColumn('hospitals', 'status')) $table->dropColumn('status');
			if (Schema::hasColumn('hospitals', 'city')) $table->dropColumn('city');
		});
	}
}; 