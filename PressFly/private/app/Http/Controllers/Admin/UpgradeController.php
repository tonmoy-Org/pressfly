<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class UpgradeController extends AdminController
{
    public function callAction($method, $parameters)
    {
        $this->authorize('super_admin');

        return parent::callAction($method, $parameters);
    }

    public function index(): View
    {
        return \view('admin.upgrade.index');
    }

    public function process(): RedirectResponse
    {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(10 * 60);
        @ini_set('max_execution_time', 10 * 60);

        try {
            $result = \Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $ex) {
            $result = $ex->getMessage();
        }

        if ($result !== 0) {
            \session()->flash('danger', $result);

            return \to_route('admin.upgrade');
        }

        $db_version = \get_option_db('version', '1.0.0');

        if (\version_compare($db_version, '3.5.0', '<')) {
            $files = [
                \base_path() . '/app/Console/Kernel.php',
                \base_path() . '/app/Exceptions/Handler.php',
                \base_path() . '/app/Http/Kernel.php',
                \base_path() . '/app/Http/Middleware/Authenticate.php',
                \base_path() . '/app/Http/Middleware/EncryptCookies.php',
                \base_path() . '/app/Http/Middleware/PreventRequestsDuringMaintenance.php',
                \base_path() . '/app/Http/Middleware/RedirectIfAuthenticated.php',
                \base_path() . '/app/Http/Middleware/TrimStrings.php',
                \base_path() . '/app/Http/Middleware/TrustHosts.php',
                \base_path() . '/app/Http/Middleware/TrustProxies.php',
                \base_path() . '/app/Http/Middleware/ValidateSignature.php',
                \base_path() . '/app/Http/Middleware/VerifyCsrfToken.php',
                \base_path() . '/app/Models/AppModel.php',
                \base_path() . '/app/Providers/BroadcastServiceProvider.php',
                \base_path() . '/app/Providers/EventServiceProvider.php',
                \base_path() . '/app/Providers/RouteServiceProvider.php',
                \base_path() . '/config/broadcasting.php',
                \base_path() . '/config/cors.php',
                \base_path() . '/config/hashing.php',
                \base_path() . '/config/sanctum.php',
                \base_path() . '/config/view.php',
                \base_path() . '/routes/channels.php',
                \base_path() . '/tests/CreatesApplication.php',
            ];
            foreach ($files as $file) {
                if (\file_exists($file)) {
                    \unlink($file);
                }
            }
        }

        DB::table('options')
            ->where('name', 'version')
            ->update(['value' => APP_VERSION]);

        \Artisan::call('optimize:clear');

        \session()->flash('success', __('Database upgraded successfully.'));

        return \to_route('admin.dashboard');
    }
}
