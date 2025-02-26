<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class MercadoLivreScraperService
{
    protected $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
    ];

    public function getRandomUserAgent()
    {
        return $this->userAgents[array_rand($this->userAgents)];
    }

    public function getProductUrls(string $keyword, int $pages = 5): array
    {
        $urls = [];
        $keyword = urlencode($keyword);

        for ($page = 1; $page <= $pages; $page++) {
            try {
                $searchUrl = "https://lista.mercadolivre.com.br/{$keyword}_Desde_{$page}";
                if ($page === 1) {
                    $searchUrl = "https://lista.mercadolivre.com.br/{$keyword}";
                }

                $response = Http::withHeaders([
                    'User-Agent' => $this->getRandomUserAgent(),
                    'Accept-Language' => 'pt-BR,pt;q=0.9',
                ])->get($searchUrl);

                //print_r($response->body());

                if ($response->successful()) {
                    $crawler = new Crawler($response->body());

                    // Seletor para links de produtos, ajuste conforme necessário
                    $crawler->filter('h3.poly-component__title-wrapper > a')->each(function (Crawler $node) use (&$urls) {
                        $url = $node->attr('href');
                        if ($url) {
                            $urls[] = $url;
                        }
                    });

                    // Espera aleatoriamente entre 2-5 segundos para evitar bloqueio
                    sleep(rand(2, 5));
                } else {
                    Log::warning("Falha ao recuperar a página {$page} para a palavra-chave '{$keyword}': " . $response->status());
                }
            } catch (\Exception $e) {
                Log::error("Erro ao buscar produtos para '{$keyword}' na página {$page}: " . $e->getMessage());
            }
        }
        return array_unique($urls);
    }
}
