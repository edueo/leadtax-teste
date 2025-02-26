<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:scraper-produtos smartphone --pages=10')
                ->dailyAt('02:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/mercadolivre-smartphone-scraper.log'));

        $schedule->command('app:scraper-produtos computadores --pages=10')
                ->dailyAt('03:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/mercadolivre-computadores-scraper.log'));

        $schedule->command('app:scraper-produtos "console video game" --pages=5')
                ->dailyAt('04:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/mercadolivre-games-scraper.log'));

        // Limpa jobs com falha automaticamente após 1 dia
        $schedule->command('queue:prune-failed --hours=24')
                ->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
