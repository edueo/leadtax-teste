<?php

namespace App\Console\Commands;

use App\Services\MercadoLivreScraperService;
use Illuminate\Console\Command;
use App\Jobs\ScraperMercadoLivreProduct;

class ScrapperProdutos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrapper-produtos {keyword?} {--pages=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fazer o scrapping de produtos do Mercado Livre com base em palavras chaves';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keyword = $this->argument('keyword') ?? 'smartphone';
        $pages = $this->option('pages');

        $scrapperService = new MercadoLivreScraperService();
        $productUrls = $scrapperService->getProductUrls($keyword, $pages);

        $this->info(count($productUrls)." produtos encontrados para a palavra-chave: $keyword");

        foreach($productUrls as $url){
            ScraperMercadoLivreProduct::dispatch($url);
        }

        $this->info("Produtos enviados para a fila de processamento!");

        return Command::SUCCESS;
    }
}
