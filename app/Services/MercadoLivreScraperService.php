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

    public function scrapeProductDetails(string $url): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->getRandomUserAgent(),
                'Accept-Language' => 'pt-BR,pt;q=0.9',
            ])->get($url);

            if (!$response->successful()) {
                Log::warning("Falha ao acessar detalhes do produto: {$url} - Status: " . $response->status());
                return null;
            }

            $crawler = new Crawler($response->body());

            // Extraindo ID do Mercado Livre da URL
            preg_match('/MLB-(\d+)|MLB(\d+)/', $url, $matches);
            Log::debug($matches);
            $mercadolivreId = $matches[0] ?? $matches[2] ?? null;

            if (!$mercadolivreId) {
                throw new \Exception("Não foi possível extrair o ID do produto da URL: {$url}");
            }

            // Preço atual
            $price = null;
            try {
                $priceText = $crawler->filter('.ui-pdp-price__second-line .andes-money-amount__fraction')->text();
                $priceText = preg_replace('/[^\d]/', '', $priceText);
                $price = floatval($priceText);
            } catch (\Exception $e) {
                // Tentar método alternativo
                try {
                    $priceText = $crawler->filter('.andes-money-amount__fraction')->first()->text();
                    $priceText = preg_replace('/[^\d]/', '', $priceText);
                    $price = floatval($priceText);
                } catch (\Exception $ex) {
                    Log::warning("Não foi possível extrair o preço para o produto: {$url}");
                }
            }

            // Título do produto
            $title = null;
            try {
                $title = $crawler->filter('h1.ui-pdp-title')->text();
            } catch (\Exception $e) {
                Log::warning("Não foi possível extrair o título para o produto: {$url}");
            }

            // Extrair descrição
            $description = null;
            try {
                $description = $crawler->filter('.ui-pdp-description__content')->text();
            } catch (\Exception $e) {
                // Descrição opcional
            }

            // Quantidade disponível
            $availableQuantity = 0;
            try {
                $availText = $crawler->filter('.ui-pdp-buybox__quantity__available')->text();
                preg_match('/(\d+)/', $availText, $matches);
                $availableQuantity = isset($matches[1]) ? intval($matches[1]) : 0;
            } catch (\Exception $e) {
                // Quantidade disponível opcional
            }

            // Condição do produto
            $condition = null;
            try {
                $condition = $crawler->filter('.ui-pdp-subtitle')->text();
            } catch (\Exception $e) {
                // Condição opcional
            }

            // Thumbnail
            $thumbnail = null;
            try {
                $thumbnail = $crawler->filter('.ui-pdp-gallery__figure img')->attr('src');
            } catch (\Exception $e) {
                // Thumbnail opcional
            }

            // Vendedor
            $sellerName = null;
            $sellerRating = null;
            try {
                $sellerName = $crawler->filter('.ui-pdp-seller__header__title')->text();
                $ratingText = $crawler->filter('.ui-seller-info__status-info .ui-pdp-seller__status-title')->text();
                preg_match('/(\d+(?:\.\d+)?)/', $ratingText, $matches);
                $sellerRating = isset($matches[1]) ? floatval($matches[1]) : null;
            } catch (\Exception $e) {
                // Informações do vendedor opcionais
            }

            return [
                'mercadolivre_id' => 'MLB' . $mercadolivreId,
                'title' => $title,
                'price' => $price,
                'description' => $description,
                'available_quantity' => $availableQuantity,
                'condition' => $condition,
                'permalink' => $url,
                'thumbnail' => $thumbnail,
                'seller_name' => $sellerName,
                'seller_rating' => $sellerRating,
                'last_updated' => now(),
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao extrair detalhes do produto {$url}: " . $e->getMessage());
            return null;
        }
    }
}
