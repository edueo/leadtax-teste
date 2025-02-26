# Leadtax Text

Tarefa: Desenvolver uma aplicação que suporte a extração de múltiplos produtos, com agendamento automático para atualização periódica dos dados. Verifique se os produtos foram atualizados, realizando apenas o scraping de novos ou modificados, e implemente tratamento avançado de erros com logs detalhados e notificações em caso de falhas recorrentes. Adicionalmente:

- [] Configure o sistema para realizar atualizações periódicas usando agendamento (por exemplo, Laravel Scheduler).
- [] Implemente uma camada de caching para otimizar o carregamento dos dados já obtidos.
- [] Utilize filas (queues) para processar a coleta de dados de forma assíncrona e distribuir a carga.
- [] Desenvolva um painel de administração para visualizar logs, gerenciar tarefas e monitorar a saúde do sistema.

Checklist de Avaliação

Nível Júnior

- [] Captura correta de nome e preço dos produtos.
- [] Estruturação básica e clara do código.
- [] Armazenamento e exibição de dados de forma adequada.

Nível Pleno

- [] Extração correta de descrição e URL da imagem.
- [] Estrutura modular do código com padrão MVC.
- [] Tratamento básico de erros e logs simples.
- [] Exibição organizada dos dados com layout responsivo e paginação.

Nível Sênior

- [] Configuração correta do agendamento periódico.
- [] Verificação e atualização eficientes dos produtos.
- [] Tratamento avançado de erros com logs e notificações.
- [] Código otimizado e escalável, com uso de filas e cache.
- [] Painel de administração para gerenciamento e monitoramento.
- [] E completar todo checklist das categorias anteriores (junior, pleno, senior)


## Scrapper Mercado Livre

### Bibliotecas utilizadas

- [DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html)
- [CssSelector](https://symfony.com/doc/current/components/css_selector.html)

**Comando para iniciar o scrapper**

```bash
./vendor/bin/sail php artisan app:scrapper-produtos --pages=10
```
