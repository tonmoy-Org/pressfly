<?php

namespace App\Providers;

use App\Mailer\Transports\PHPMailerMailTransport;
use App\Mailer\Transports\PHPMailerSendmailTransport;
use App\Mailer\Transports\PHPMailerSmtpTransport;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*
        // Not working anymore with laravel 10
        $this->app->bind('path.public', function () {
            return \realpath(\base_path('../'));
        });
        */
        $this->app->usePublicPath(\realpath(\base_path('../')));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(!$this->app->isProduction());

        Env::disablePutenv();

        Schema::defaultStringLength(191);

        Paginator::useBootstrap();

        $this->app->setLocale(\get_option('language', 'en'));

        Mail::extend('phpmailer-mail', function (array $config = []) {
            return new PHPMailerMailTransport();
        });

        Mail::extend('phpmailer-smtp', function (array $config = []) {
            return new PHPMailerSmtpTransport();
        });

        Mail::extend('phpmailer-sendmail', function (array $config = []) {
            return new PHPMailerSendmailTransport();
        });

        // Route
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        if (!$this->app->runningInConsole()) {
            if (!\in_array(\request()->segment(1), ['admin', 'member'], true)) {
                //\Debugbar::startMeasure('report', 'Elements');
                \App\Helpers\Elements::add();
                //\Debugbar::stopMeasure('report');
            }
        }
    }
}
