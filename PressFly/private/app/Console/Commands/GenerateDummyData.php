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
            $viewCount = \App\Models\Statistic::where('user_id', $user->id)->count();
            
            if ($viewCount > 0) {
                $newEarn = 5000 / $viewCount;
                
                \Illuminate\Support\Facades\DB::statement("
                    UPDATE statistics 
                    SET author_earn = ?
                    WHERE user_id = ?
                ", [$newEarn, $user->id]);
                
                $totalEarn = \App\Models\Statistic::where('user_id', $user->id)->sum('author_earn');
                $user->author_earnings = $totalEarn;
                $user->save();
                
                $this->info("Updated earnings to \$5000. New CPM is approximately $" . round($newEarn * 1000, 2));
            }
        } else {
            $this->error("User not found.");
        }

        return Command::SUCCESS;
    }
}
