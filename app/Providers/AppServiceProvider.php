<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // automatically prepare a sqlite database file when we are using the
        // sqlite connection.  Without this developers will often leave
        // DB_DATABASE=laravel or similar in .env (see .env.example) and the
        // framework tries to open a file literally called "laravel" in the
        // project root.  That fails with a 500 and the only clue in the log is
        // "Database file at path [laravel] does not exist".  The code below
        // resolves relative names to the database_path() and creates the file if
        // missing so the application can start even when the environment is
        // mis‑configured.
        if (config('database.default') === 'sqlite') {
            $database = config('database.connections.sqlite.database');

            if ($database !== ':memory:') {
                // convert to absolute path when a plain filename is given
                if (!preg_match('/^(\/|[A-Za-z]:\\\\)/', $database)) {
                    $database = database_path($database);
                }

                if (!file_exists($database)) {
                    if (!is_dir(dirname($database))) {
                        mkdir(dirname($database), 0755, true);
                    }
                    touch($database);
                }

                config(['database.connections.sqlite.database' => $database]);
            }
        }
    }
}
