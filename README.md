# Scrapper Mercado Livre

## Setup ambiente local

1. Clonar esse repositório
2. Criar o .env: `cp .env.example .env`
3. Instalar as dependências do projeto: `composer install`
4. Subir os containers: `./vendor/bin/sail up`
5. Executar as migrations: `./vendor/bin/sail php artisan migrate`

## Componentes da solução

1. **app/Commands/ScrapperProdutos.php**: Comando responsável por receber um termo de busca e número de páginas e fazer o dispatch das urls para a fila.

_Obs: Para esse projeto, utilizei a fila em banco de dados._

2. **app/Jobs/ScraperMercadoLivreProduct.php**: Worker que irá processar a fila e persistir os produtos no bano de dados.

3. **app/Services/MercadoLivreScraperService.php**: Serviço que efetua o scrape no site, para isso foi utilizado 2 bibliotecas:

- [DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html)
- [CssSelector](https://symfony.com/doc/current/components/css_selector.html)

## Executando o script manualmente 

1. Executar o comando para enfileirar os produtos dado uma palavra chave e o número de páginas:

```bash
./vendor/bin/sail php artisan app:scrapper-produtos --pages=10
```

A saída desse comando deve ser similar a essa:
```
199 produtos encontrados para a palavra-chave: smartphone
Produtos enviados para a fila de processamento!
```

2. Com os produtos inseridos na fila, podemos executar o job que irá efetuar o processamento.

```bash 
./vendor/bin/sail php artisan queue:work --tries=3 --backoff=5
```

3. Após a execução do job, os produtos devem ser inseridos na tabela `products`:

```
*************************** 70. row ***************************
                 id: 70
    mercadolivre_id: MLBMLB43943405
              title: Redmi Note 13 256gb 8gb Ram Dual Sim Cor Verde-claro
              price: 1370.00
     original_price: NULL
discount_percentage: NULL
 available_quantity: 5
      sold_quantity: 0
          condition: Novo | +100 vendidos
          permalink: https://www.mercadolivre.com.br/redmi-note-13-256gb-8gb-ram-dual-sim-cor-verde-claro/p/MLB43943405#polycard_client=search-nordic&searchVariation=MLB43943405&wid=MLB3948468133&position=49&search_layout=stack&type=product&tracking_id=82d347f6-d510-447a-a272-49a44f17411d&sid=search
          thumbnail: https://http2.mlstatic.com/D_NQ_NP_893914-MLA80727566036_112024-O.jpg
          seller_id: NULL
        seller_name: Vendido porCENTERSOM
      seller_rating: NULL
        category_id: NULL
      category_name: NULL
        description: Capacidade e eficinciaCom seu poderoso processador e memria RAM de 8 GB seu computador alcanar alto desempenho com alta velocidade de transmisso de contedos e executar vrios aplicativos ao mesmo tempo, sem atrasos.Capacidade de armazenamento ilimitadaEsquea de apagar. Com sua memria interna de 256 GB voc poder baixar todos os arquivos e aplicativos que precisa, salvar fotos e armazenar seus filmes, sries e vdeos favoritos para reproduzi-los quando quiser.
         attributes: NULL
      shipping_info: NULL
       last_updated: 2025-02-26 11:07:40
         created_at: 2025-02-26 11:07:40
         updated_at: 2025-02-26 11:07:40
```

## Próximos passos:

- Implementar o painel administrativo para gerenciamento e monitoramento.


## Execução agendada 

Criar uma cron no Sistema Operacional

```
* * * * * cd /path-to-your-project && ./vendor/bin/sail php artisan schedule:run >> /dev/null 2>&1
```

As tarefas agendadas estão configuradas em `app/Console/Kernel.php`

