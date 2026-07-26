<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
    //health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn() => route('login'));
        $middleware->redirectUsersTo(fn() => url('/'));

        $middleware->throttleApi('api');

        $middleware->append(
            [
                //\App\Http\Middleware\Install::class,
            ]
        );

        $middleware->web(
            append: [
                \App\Http\Middleware\Install::class,
                \App\Http\Middleware\MemberMissingFields::class,
                \App\Http\Middleware\Upgrade::class,
                \App\Http\Middleware\VisitorCheck::class,
            ]
        );

        $middleware
            ->alias(
                [
                    'role' => \App\Http\Middleware\UserRole::class,
                ]
            );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // $schedule->command('inspire')->hourly();

        /**
         * https://laravel.com/docs/8.x/queues#processing-all-queued-jobs-then-exiting
         * https://laravel.com/docs/8.x/queues#queue-workers-and-deployment
         *
         * Check: https://laravel.com/docs/8.x/queues#job-expiration
         * Check: https://laravel.com/docs/8.x/queues#worker-timeouts
         */
        // \Artisan::call('queue:restart');
        // queue:listen
//        $schedule->command('queue:work --stop-when-empty --tries=1 --backoff=5')
//            ->everyMinute()->withoutOverlapping()->runInBackground();
        /**
         * Check last time the cron run
         * https://laravel.com/docs/8.x/scheduling#task-hooks
         */
    })
    ->create();
