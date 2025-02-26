<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ScraperMercadoLivreProduct implements ShouldQueue
{
    use Queueable;
    protected $url;
    protected $maxAttempts = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info($this->url);
        } catch (\Exception $e) {

            Log::error("Erro ao processar produto {$this->url}: ". $e->getMessage());
            if ($this->attempts() < $this->maxAttempts) {
                throw $e;
            }
        }

    }
}
