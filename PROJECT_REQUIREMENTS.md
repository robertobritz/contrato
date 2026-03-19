# ContractFlow — Requisitos do Sistema

> Documento de referência para o agente de desenvolvimento (Claude Sonnet no VSCode).  
> Leia este arquivo **antes de qualquer tarefa** para garantir consistência arquitetural.

---

## 1. Visão Geral

**ContractFlow** é um sistema web de gestão e edição de contratos jurídicos no navegador.  
O usuário carrega um contrato-base com variáveis dinâmicas (`$cliente.nome`, `$cliente.cpf`, etc.) e o sistema gera versões personalizadas para cada cliente cadastrado, mantendo o original intacto para reedição.

---

## 2. Stack Tecnológica

| Camada                    | Tecnologia                                |
| ------------------------- | ----------------------------------------- |
| Framework PHP             | Laravel 13                                |
| Painel Admin              | Filament v5                               |
| Testes                    | PEST PHP (TDD)                            |
| Banco de dados local      | MySQL via Takeout (Docker)                |
| Editor de texto rico      | TipTap ou similar (integrado ao Filament) |
| Armazenamento de arquivos | Laravel Storage (disco `local`)           |
| Autenticação              | Laravel Breeze / Filament Shield          |
| Linguagem                 | PHP 8.3+                                  |

---

## 3. Ambiente de Desenvolvimento

- **Takeout** gerencia os serviços Docker locais (MySQL, Redis se necessário).
- Não utilizar Laravel Sail; o PHP roda diretamente na máquina host.
- Variáveis de ambiente em `.env` (nunca commitadas).
- `APP_ENV=local`, `APP_DEBUG=true`.

```bash
# Exemplo de inicialização do banco
takeout enable mysql
```

---

## 4. Metodologia de Desenvolvimento

### 4.1 TDD com PEST PHP

- **Todo código de produção** deve ser precedido de um teste que falha (Red → Green → Refactor).
- Testes ficam em `tests/Feature` (fluxos completos via Filament/HTTP) e `tests/Unit` (lógica isolada).
- Rodar a suite completa antes de qualquer commit: `php artisan test`.
- Nomenclatura dos testes em inglês, descritiva:
    ```php
    it('replaces client variables in a contract body');
    it('prevents unauthenticated users from accessing contracts');
    ```

### 4.2 Boas Práticas

- **SOLID** — cada classe tem uma única responsabilidade.
- **Service Layer** — lógica de negócio em `App\Services`, nunca em controllers ou Models.
- **Form Requests** — validações em classes dedicadas.
- **Resources do Filament** — um Resource por entidade (Client, Contract, ClientContract).
- **Migrations** atômicas e reversíveis (`down()` sempre implementado).
- **Factories e Seeders** para todo Model — usados nos testes e para popular dados de desenvolvimento.
- **Conventional Commits** no Git: `feat:`, `fix:`, `test:`, `refactor:`, `docs:`.

---

## 5. Entidades e Modelo de Dados

### 5.1 `users`

Gerenciado pelo Laravel/Filament. Campos padrão + `is_admin` (boolean).

### 5.2 `clients`

Atributos utilizados em contratos:

| Coluna               | Tipo       | Variável no contrato            |
| -------------------- | ---------- | ------------------------------- |
| `id`                 | UUID       | —                               |
| `user_id`            | FK → users | —                               |
| `name`               | string     | `$cliente.nome`                 |
| `email`              | string     | `$cliente.email`                |
| `phone`              | string     | `$cliente.telefone`             |
| `cpf`                | string     | `$cliente.cpf`                  |
| `rg`                 | string     | `$cliente.rg`                   |
| `birth_date`         | date       | `$cliente.nascimento`           |
| `nationality`        | string     | `$cliente.nacionalidade`        |
| `marital_status`     | enum       | `$cliente.estado_civil`         |
| `profession`         | string     | `$cliente.profissao`            |
| `address`            | string     | `$cliente.endereco`             |
| `address_number`     | string     | `$cliente.endereco_numero`      |
| `address_complement` | string     | `$cliente.endereco_complemento` |
| `neighborhood`       | string     | `$cliente.bairro`               |
| `city`               | string     | `$cliente.cidade`               |
| `state`              | string     | `$cliente.estado`               |
| `zip_code`           | string     | `$cliente.cep`                  |
| `created_at`         | timestamp  | —                               |
| `updated_at`         | timestamp  | —                               |

### 5.3 `contracts` (Contratos-base)

| Coluna               | Tipo                     | Descrição                                                                      |
| -------------------- | ------------------------ | ------------------------------------------------------------------------------ |
| `id`                 | UUID                     | —                                                                              |
| `user_id`            | FK → users               | Dono do contrato                                                               |
| `title`              | string                   | Nome descritivo                                                                |
| `body`               | longText                 | Conteúdo HTML com variáveis `$cliente.*`                                       |
| `source_type`        | enum(`upload`, `manual`) | Forma de criação do contrato                                                   |
| `original_file_path` | string nullable          | Caminho do arquivo `.doc`/`.docx` carregado (só quando `source_type = upload`) |
| `created_at`         | timestamp                | —                                                                              |
| `updated_at`         | timestamp                | —                                                                              |

### 5.4 `client_contracts` (Contratos por cliente)

| Coluna               | Tipo               | Descrição                                         |
| -------------------- | ------------------ | ------------------------------------------------- |
| `id`                 | UUID               | —                                                 |
| `contract_id`        | FK → contracts     | Contrato-base                                     |
| `client_id`          | FK → clients       | Cliente vinculado                                 |
| `body`               | longText           | Cópia do body com variáveis substituídas          |
| `is_manually_edited` | boolean            | True se o usuário editou manualmente após geração |
| `generated_at`       | timestamp nullable | Data da última geração automática                 |
| `created_at`         | timestamp          | —                                                 |
| `updated_at`         | timestamp          | —                                                 |

---

## 6. Funcionalidades Detalhadas

### F1 — Criação de Contrato-base

- O usuário autenticado acessa **Contratos > Novo**.
- O formulário apresenta uma **seleção exclusiva de modo de criação** (Radio / Toggle):
    - **"Fazer upload de arquivo Word"** — aceita `.doc` ou `.docx`.
    - **"Escrever manualmente"** — exibe diretamente o editor WYSIWYG em branco.
- Apenas **uma opção pode estar ativa** por vez; ao alternar, os campos da opção anterior ficam ocultos/desabilitados.

**Modo: Upload de arquivo Word**

- O usuário seleciona um arquivo `.doc` ou `.docx`.
- O sistema extrai o conteúdo textual e preenche automaticamente o editor **"Corpo do Contrato"** com o conteúdo convertido para HTML.
- O arquivo original é salvo em `storage/app/contracts/originals/{id}`.
- O título é preenchido automaticamente pelo nome do arquivo (editável).
- `contracts.source_type` é definido como `upload`.

**Modo: Escrita manual**

- O editor **"Corpo do Contrato"** é exibido em branco para entrada livre do usuário.
- `contracts.source_type` é definido como `manual`.
- Nenhum arquivo é armazenado; `original_file_path` permanece `null`.

**Regras gerais:**

- No modo upload, apenas `.doc` e `.docx` são aceitos (validação MIME real).
- Tamanho máximo do arquivo: 5 MB.
- O "Corpo do Contrato" (editor WYSIWYG) é sempre o único campo de edição de conteúdo, independentemente do modo escolhido.
- O upload não cria `client_contracts` — só o contrato-base.

### F2 — Visualização e Edição do Contrato-base

- O painel exibe o `body` em um editor WYSIWYG (TipTap via Filament) sob o rótulo **"Corpo do Contrato"**.
- Se o contrato foi criado via upload, o conteúdo inicial do editor é o texto extraído do arquivo Word.
- Se foi criado manualmente, o editor inicia com o conteúdo salvo anteriormente.
- O usuário edita livremente, incluindo escrevendo variáveis `$cliente.*`.
- Um **botão flutuante "Variáveis"** (fixo no canto inferior direito, segue a rolagem da tela) exibe a lista de variáveis de cliente disponíveis. O painel mostra apenas o rótulo de cada variável; ao clicar no ícone de copiar, a variável é copiada para a área de transferência e o painel é fechado automaticamente para facilitar a inserção no editor. Um **contador numérico** exibido ao lado de cada variável indica quantas vezes ela já foi utilizada no contrato (atualizado em tempo real a cada segundo). O botão flutuante está disponível tanto na **criação** quanto na **edição** de contratos.
- Ao salvar, apenas `contracts.body` é atualizado.
- Os `client_contracts` **não** são recalculados automaticamente — o usuário escolhe quando regenerar.

### F3 — Cadastro de Clientes

- Resource **Clientes** no Filament com todos os campos da tabela `clients`.
- Listagem com busca por nome, CPF e e-mail.
- Validações: CPF único por usuário, e-mail único, campos obrigatórios sinalizados.

### F4 — Vinculação de Cliente ao Contrato

- Na tela do contrato-base, uma seção **"Clientes vinculados"** lista os `client_contracts`.
- Botão **"Vincular cliente"** abre um modal de seleção (Select do Filament com busca).
- Ao vincular, o sistema cria o `client_contract` e executa a substituição de variáveis imediatamente (geração inicial).

### F5 — Sistema de Variáveis

**Mapeamento completo** (`$cliente.{chave}` → `clients.{coluna}`):

```
$cliente.nome              → name
$cliente.email             → email
$cliente.telefone          → phone
$cliente.cpf               → cpf
$cliente.rg                → rg
$cliente.nascimento        → birth_date (formatado: d/m/Y)
$cliente.nacionalidade     → nationality
$cliente.estado_civil      → marital_status
$cliente.profissao         → profession
$cliente.endereco          → address
$cliente.endereco_numero   → address_number
$cliente.endereco_complemento → address_complement
$cliente.bairro            → neighborhood
$cliente.cidade            → city
$cliente.estado            → state
$cliente.cep               → zip_code
```

**Implementação via Service:**

```php
// App\Services\ContractVariableResolver

public function resolve(string $body, Client $client): string
{
    $map = $client->variableMap(); // retorna array ['$cliente.nome' => 'João', ...]
    return str_replace(array_keys($map), array_values($map), $body);
}
```

- Variáveis sem correspondência (campo vazio no cliente) são **mantidas** no texto (não apagadas), permitindo revisão.
- Um helper opcional pode listar as variáveis detectadas no `body` que ainda não foram resolvidas.

### F6 — Contratos por Cliente (Edição Individual)

- Cada `client_contract` pode ser aberto e editado separadamente no editor WYSIWYG.
- Ao editar manualmente, o campo `is_manually_edited` passa a `true`.
- Um aviso visual indica se o contrato-base foi alterado após a última geração.
- O usuário pode **"Regenerar"** o contrato do cliente (sobrescreve o `body` do `client_contract` com a substituição atual), perdendo edições manuais (confirmação necessária).

### F7 — Contratos Originais vs. Contratos de Cliente

|                      | Contrato-base (`contracts`)                                 | Contrato do cliente (`client_contracts`) |
| -------------------- | ----------------------------------------------------------- | ---------------------------------------- |
| Editável?            | Sim, sempre                                                 | Sim, independentemente                   |
| Contém variáveis?    | Sim                                                         | Não (substituídas)                       |
| Pode ser regenerado? | N/A                                                         | Sim (a partir do contrato-base)          |
| Exclusão             | Remove também os client_contracts (soft delete recomendado) | Independente                             |

---

## 7. Estrutura de Diretórios Relevante

```
app/
├── Filament/
│   └── Resources/
│       ├── ClientResource.php
│       ├── ContractResource.php
│       └── ClientContractResource.php
├── Models/
│   ├── Client.php
│   ├── Contract.php
│   └── ClientContract.php
├── Services/
│   ├── ContractVariableResolver.php
│   ├── ContractImportService.php   # extrai texto de .txt/.docx/.html
│   └── ClientContractGenerator.php
├── Policies/
│   ├── ContractPolicy.php
│   └── ClientPolicy.php
└── Http/
    └── Requests/
        ├── StoreClientRequest.php
        └── StoreContractRequest.php

tests/
├── Feature/
│   ├── ContractUploadTest.php
│   ├── ContractEditorTest.php
│   ├── ClientManagementTest.php
│   ├── ClientContractBindingTest.php
│   └── VariableResolutionTest.php
└── Unit/
    ├── ContractVariableResolverTest.php
    └── ContractImportServiceTest.php
```

---

## 8. Segurança e Autorização

- **Policies** para `Contract` e `Client`: usuário só acessa seus próprios registros (`user_id`).
- Filament Shield (ou Gate manual) para controle de permissões por papel.
- Autenticação obrigatória em todas as rotas do painel.
- Upload com validação de MIME real (não só extensão): usar `mimetypes` no FormRequest.
- Conteúdo HTML do editor deve ser sanitizado antes de salvar (`HTMLPurifier` ou `League\HtmlToMarkdown`).

---

## 9. Instruções para o Agente de IA (Claude no VSCode)

> **Leia esta seção antes de iniciar qualquer tarefa.**

### 9.1 Fluxo de Trabalho Obrigatório

1. **Antes de criar qualquer arquivo de código**, escreva o teste correspondente em PEST.
2. Rode `php artisan test` — o teste deve falhar (Red).
3. Implemente o código mínimo para passar o teste (Green).
4. Refatore mantendo os testes verdes (Refactor).
5. Nunca pule etapas do ciclo TDD.

### 9.2 Padrões de Código

- **Tipagem estrita** em todos os arquivos PHP: `declare(strict_types=1);`
- **Return types** explícitos em todos os métodos.
- **Injeção de dependência** via construtor — nunca instanciar services com `new` dentro de outras classes.
- **Nomes em inglês** para classes, métodos, variáveis e colunas de banco.
- **Nomes em português** apenas nas labels da UI (Filament) e mensagens de validação.
- Usar **Enums PHP 8.1+** para campos de valor fixo (`marital_status`, etc.).
- Preferir **Collections** e métodos funcionais do Laravel em vez de loops imperativos.

### 9.3 Filament v5 — Convenções

- Cada Resource gerado via `php artisan make:filament-resource {Model} --generate`.
- Usar `RelationManager` para listar `client_contracts` dentro do `ContractResource`.
- Ações customizadas (ex: "Regenerar contrato") como `Action` do Filament com confirmação modal.
- Editor WYSIWYG: usar `RichEditor` do Filament ou componente TipTap customizado.

### 9.4 O que Nunca Fazer

- ❌ Lógica de negócio em Models, Controllers ou Resources do Filament.
- ❌ Queries raw sem `DB::` ou Eloquent justificado.
- ❌ Commitar `.env`, `storage/` ou `vendor/`.
- ❌ Criar migrations sem `down()`.
- ❌ Pular testes argumentando que "é simples".
- ❌ Usar `dd()` ou `dump()` em código que não seja temporário de debug.

### 9.5 Comandos Úteis de Referência

```bash
# Criar entidades
php artisan make:model Client -mf          # Model + Migration + Factory
php artisan make:filament-resource Client --generate
php artisan make:service ContractVariableResolver  # (pacote ou manual)
php artisan make:policy ContractPolicy --model=Contract

# Testes
php artisan test
php artisan test --filter=VariableResolution
./vendor/bin/pest --coverage

# Banco
php artisan migrate:fresh --seed
takeout enable mysql   # iniciar MySQL local
```

---

## 10. Ordem de Implementação Sugerida

1. **Setup inicial**: instalação do Laravel 13, Filament v5, PEST, configuração do Takeout.
2. **Autenticação**: painel Filament funcional com login.
3. **Clients**: Model, migration, factory, Policy, Resource, testes.
4. **Contracts (base)**: Model, migration, factory, upload service, editor, testes.
5. **Variable Resolver**: Service + Unit tests completos.
6. **ClientContracts**: Model, migration, RelationManager, geração, regeneração, testes.
7. **Polish**: avisos de variáveis não resolvidas, confirmações de regeneração, sanitização HTML.

---

_Última atualização: gerado automaticamente como documento-base do projeto ContractFlow._
