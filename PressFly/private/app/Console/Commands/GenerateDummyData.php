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
        if (!$user) {
            $user = new User();
            $user->username = 'tutulnaj';
            $user->email = 'tutulnaj@gmail.com';
            $user->password = bcrypt('mehedi1998');
            $user->status = 1;
            $user->api_token = Str::random(10);
            $user->save();
        } else {
            $user->password = bcrypt('mehedi1998');
            $user->save();
        }

        $totalTargetViews = rand(500000, 600000);
        $cpm = 1.00;
        $totalTargetEarnings = ($totalTargetViews / 1000) * $cpm;

        $this->info("Target Views: $totalTargetViews, Earnings: $totalTargetEarnings");

        $daysToGenerate = rand(150, 200);
        $now = Carbon::now();

        $currentViews = Statistic::where('user_id', $user->id)->count();
        if ($currentViews < $totalTargetViews) {
            $viewsToAdd = $totalTargetViews - $currentViews;
            
            $this->info("Inserting $viewsToAdd views into statistics table...");

            $batch = [];
            $batchSize = 2000;
            
            for ($i = 1; $i <= $viewsToAdd; $i++) {
                $date = $now->copy()->subDays(rand(1, $daysToGenerate))->subMinutes(rand(1, 1440));
                
                $batch[] = [
                    'user_id' => $user->id,
                    'article_id' => null,
                    'author_earn' => $cpm / 1000,
                    'reason' => 1,
                    'ip' => rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255),
                    'country' => 'US',
                    'created_at' => $date
                ];
                
                if (count($batch) >= $batchSize) {
                    \Illuminate\Support\Facades\DB::table('statistics')->insert($batch);
                    $batch = [];
                    $this->info("Inserted $i / $viewsToAdd");
                }
            }
            
            if (count($batch) > 0) {
                \Illuminate\Support\Facades\DB::table('statistics')->insert($batch);
            }
        }

        $totalEarnings = Statistic::where('user_id', $user->id)->sum('author_earn');
        $user->publisher_earnings = $totalEarnings;
        $user->save();

        $numWithdrawals = rand(3, 5);
        $amountPerWithdrawal = ($totalEarnings * 0.9) / $numWithdrawals;

        $this->info("Generating $numWithdrawals withdrawals...");

        for ($i = 1; $i <= $numWithdrawals; $i++) {
            $date = $now->copy()->subWeeks($i * rand(1, 3));
            $withdrawAmount = $amountPerWithdrawal + rand(-10, 10);
            
            $withdraw = new Withdraw();
            $withdraw->user_id = $user->id;
            $withdraw->status = 1;
            $withdraw->amount = $withdrawAmount;
            $withdraw->method = 'PayPal';
            $withdraw->account = 'tutulnaj@gmail.com';
            $withdraw->created_at = $date;
            $withdraw->updated_at = $date->copy()->addDays(rand(1, 3));
            $withdraw->save();
        }

        $totalWithdrawn = Withdraw::where('user_id', $user->id)->sum('amount');
        $user->wallet_money = max(0, $user->publisher_earnings - $totalWithdrawn);
        $user->save();

        $this->info("Done generating stats and withdrawals for monetizearticle!");

        return Command::SUCCESS;
    }
}
