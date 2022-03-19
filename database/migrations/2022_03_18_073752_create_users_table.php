<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
        -- kumu.users definition

        CREATE TABLE `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '4bUwBYgbQhD9IegSd3O7',
        `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `users_uuid_unique` (`uuid`),
        UNIQUE KEY `users_username_unique` (`username`)
        ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
         *
         *
         * INSERT INTO kumu.users
          (uuid, username, password, created_at)
          VALUES('4bUwBYgbQhD9IegSd3O7s', 'admin@gmails.com', '$2y$10$31Bja9RRZUNwJ3KV670m4OnHuSVaBq9Z9stjlvVZTFSqGIPcmVys.', CURRENT_TIMESTAMP);
         */
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('uuid', 30)->unique()->default(Str::random(20));
            $table->string('username', 50)->unique();
            $table->string('password', 150);
            $table->dateTimeTz('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTimeTz('updated_at')->nullable();
        });

        User::create(['username' => 'admin@gmail.com', 'password' => Hash::make('admin123')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
