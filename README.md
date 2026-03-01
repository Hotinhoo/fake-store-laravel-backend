# Fake Store API - Laravel Backend

Este projeto é uma API REST desenvolvida em Laravel para consumir dados da Fake Store API. Ele armazena os produtos localmente, disponibiliza endpoints estruturados com regras de negócio rígidas e conta com uma Interface Visual de Administração para facilitar os testes e validações.

![captura de tela](https://github.com/Hotinhoo/fake-store-laravel-backend/blob/37bb944b2ee330d64884b3a6074435dec68b22a8/screenshot.png)

## Pré-requisitos

* PHP >= 8.2
* Composer
* MySQL, PostgreSQL ou SQLite (configurável)

## Como Executar o Projeto (Quick Start)

Siga os passos abaixo para rodar o projeto perfeitamente no seu ambiente local:

* Clone o repositório e instale as dependências do Laravel:
```bash
git clone https://github.com/Hotinhoo/fake-store-laravel-backend.git
cd fake-store-laravel-backend
composer install
```

* Copie o arquivo de exemplo para criar o seu .env:
```bash
cp .env.example .env
```
Ou windows:
```
copy .env.example .env
```

* Gere a chave de segurança da aplicação:
```bash
php artisan key:generate
```

* Abra o arquivo .env e confirme se as configurações de banco de dados e API estão conforme abaixo:
```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=enc
DB_USERNAME=root
DB_PASSWORD=

FAKESTORE_API_URL=https://fakestoreapi.com/products
FAKESTORE_VERIFY_SSL=false
```
*Nota: a verificação de SSL por padrão no código é true, mas coloquei essa variável para desativar caso esteja rodando em ambiente de produção sem o certificado*

* Rode as migrations para criar as tabelas e índices necessários:
```bash
php artisan migrate
```

* Inicie o servidor de desenvolvimento:
```bash
php artisan serve
```

A aplicação agora está rodando em http://127.0.0.1:8000/.
## Interface Visual de Administração

Para provar que a API é robusta e fácil de integrar, o projeto conta com uma página front-end (Single Page Application) integrada diretamente no caminho / da aplicação. Através dessa tela, você pode testar todo o escopo do projeto visualmente:

* **Importação Real**: Clique no botão "Import from FakeStore" para engatilhar o endpoint de Upsert.
* **Filtros Combinados**: Use a barra lateral para testar a busca por texto (mín. 3 chars), categorias dinâmicas, intervalo de preços e rating em tempo real.
* **Gestão de Produtos**: Clique em qualquer card de produto para abrir o painel de edição.
* **Edição**: Altere título, preço ou categoria e veja as validações de erro da API.
* **Remoção**: Teste a regra de Soft Delete. O sistema bloqueará a exclusão caso o rating seja superior a 4.5. É obrigatório informar o motivo.

## Sincronização e Logs (Fonte da Verdade)

O sistema foi desenhado para tratar a API externa (FakeStore) como a fonte absoluta da verdade. Isso significa que a rotina de importação garante a integridade dos dados originais:

* **Sobrescrita Automática:** Se você alterar o título ou o preço de um produto localmente e depois rodar a importação, o sistema detectará a divergência e restaurará os dados originais da API externa.
* **Restauração de Excluídos:** Se você aplicar um Soft Delete em um produto e ele for enviado novamente pela API externa em uma nova importação, o sistema irá retirá-lo da lixeira automaticamente.
* **Rastreabilidade Total (Logs):** Nenhuma alteração manual ou automática é perdida. Toda vez que a API externa sobrescreve ou restaura um dado, o sistema registra o histórico exato na coluna update_log (JSON) com a tag "action": "api_sync".

  

## Resumo dos Endpoints (API REST)

Todas as rotas da API possuem o prefixo /api e retornam JSON padronizado.

* `POST /api/products/import` - Sincronização, Upsert e Restore automático.
* `GET /api/products/stats` - Estatísticas consolidadas com uso de Cache.
* `GET /api/products` - Listagem com filtros combinados e paginação (Padrão Pipeline).
* `GET /api/products/{id}` - Detalhes com cálculo de taxa (10%) em tempo real.
* `PATCH /api/products/{id}` - Edição parcial com validação via FormRequest.
* `DELETE /api/products/{id}` - Soft Delete com trava de rating e registro de motivo.

## Detalhes Arquiteturais

**Camadas Isoladas:** Lógica de negócio em Service Classes.
**Pipelines:** Filtros SQL modulares e performáticos.
**Resiliência:** Uso de Http::retry() e tratamento global de erros (incluindo JSON 404).
