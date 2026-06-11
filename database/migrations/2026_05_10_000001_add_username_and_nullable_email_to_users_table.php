<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $addedUsername = false;

        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->after('name');
            });

            $addedUsername = true;
        }

        if ($addedUsername) {
            $usedUsernames = [];

            DB::table('users')->orderBy('id')->get(['id', 'name', 'email', 'username'])->each(function ($user) use (&$usedUsernames) {
                if ($user->username) {
                    $usedUsernames[$user->username] = true;

                    return;
                }

                $base = $user->email
                    ? Str::before($user->email, '@')
                    : $user->name;

                $base = Str::of($base)
                    ->lower()
                    ->replaceMatches('/[^a-z0-9_]+/', '_')
                    ->trim('_')
                    ->toString();

                $base = $base !== '' ? $base : 'user';
                $username = $base;
                $counter = 1;

                while (isset($usedUsernames[$username])) {
                    $username = $base.'_'.$counter++;
                }

                $usedUsernames[$username] = true;

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => $username]);
            });
        }

        Schema::table('users', function (Blueprint $table) use ($addedUsername) {
            if ($addedUsername) {
                $table->string('username')->nullable(false)->change();
                $table->unique('username');
            }

            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
