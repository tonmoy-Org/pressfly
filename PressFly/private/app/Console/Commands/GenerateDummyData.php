<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Statistic;
use App\Models\Withdraw;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy data for tutulnaj';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('email', 'tutulnaj@gmail.com')->first();
        if ($user) {
            $user->password = bcrypt('mehedi1998');
            $user->status = 1;
            $user->save();
            $this->info("Password and status reset.");
            
            $currentMonthStart = now()->startOfMonth();
            
            \Illuminate\Support\Facades\DB::statement("
                UPDATE statistics 
                SET created_at = DATE_ADD(?, INTERVAL RAND() * 10 DAY)
                WHERE user_id = ?
            ", [$currentMonthStart->format('Y-m-d H:i:s'), $user->id]);
            
            $this->info("Updated statistics to current month.");
        } else {
            $this->error("User not found.");
        }

        return Command::SUCCESS;
    }
}
