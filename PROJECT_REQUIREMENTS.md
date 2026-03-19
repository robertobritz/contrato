# ContractFlow — Requisitos do Sistema

> Documento de referência para o agente de desenvolvimento (Claude Sonnet no VSCode).  
> Leia este arquivo **antes de qualquer tarefa** para garantir consistência arquitetural.

---

## 1. Visão Geral

**ContractFlow** é um sistema web de gestão e edição de contratos jurídicos no navegador.  
O usuário carrega um contrato-base com variáveis dinâmicas (`$contratante.nome`, `$contratante.cpf`, etc.) e o sistema gera versões personalizadas para cada contratante cadastrado, mantendo o original intacto para reedição.

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

### 5.2 `contratantes`

Partes contratantes. Atributos utilizados em contratos:

| Coluna               | Tipo       | Variável no contrato                |
| -------------------- | ---------- | ----------------------------------- |
| `id`                 | UUID       | —                                   |
| `user_id`            | FK → users | —                                   |
| `name`               | string     | `$contratante.nome`                 |
| `email`              | string     | `$contratante.email`                |
| `phone`              | string     | `$contratante.telefone`             |
| `cpf`                | string     | `$contratante.cpf`                  |
| `rg`                 | string     | `$contratante.rg`                   |
| `birth_date`         | date       | `$contratante.nascimento`           |
| `nationality`        | string     | `$contratante.nacionalidade`        |
| `marital_status`     | enum       | `$contratante.estado_civil`         |
| `profession`         | string     | `$contratante.profissao`            |
| `address`            | string     | `$contratante.endereco`             |
| `address_number`     | string     | `$contratante.endereco_numero`      |
| `address_complement` | string     | `$contratante.endereco_complemento` |
| `neighborhood`       | string     | `$contratante.bairro`               |
| `city`               | string     | `$contratante.cidade`               |
| `state`              | string     | `$contratante.estado`               |
| `zip_code`           | string     | `$contratante.cep`                  |
| `created_at`         | timestamp  | —                                   |
| `updated_at`         | timestamp  | —                                   |

### 5.2b `contratados`

Partes contratadas. Mesma estrutura de campos que `contratantes`, com variáveis prefixadas por `$contratado.*`:

| Variável                         | Campo   |
| -------------------------------- | ------- |
| `$contratado.nome`               | `name`  |
| `$contratado.cpf`                | `cpf`   |
| `$contratado.email`              | `email` |
| … (mesmos campos de contratante) |         |

### 5.3 `contracts` (Contratos-base)

| Coluna               | Tipo                     | Descrição                                                                      |
| -------------------- | ------------------------ | ------------------------------------------------------------------------------ |
| `id`                 | UUID                     | —                                                                              |
| `user_id`            | FK → users               | Dono do contrato                                                               |
| `title`              | string                   | Nome descritivo                                                                |
| `body`               | longText                 | Conteúdo HTML com variáveis `$contratante.*`                                   |
| `source_type`        | enum(`upload`, `manual`) | Forma de criação do contrato                                                   |
| `original_file_path` | string nullable          | Caminho do arquivo `.doc`/`.docx` carregado (só quando `source_type = upload`) |
| `created_at`         | timestamp                | —                                                                              |
| `updated_at`         | timestamp                | —                                                                              |

### 5.4 `contratante_contracts` (Contratos por contratante)

| Coluna               | Tipo                         | Descrição                                         |
| -------------------- | ---------------------------- | ------------------------------------------------- |
| `id`                 | UUID                         | —                                                 |
| `contract_id`        | FK → contracts               | Contrato-base                                     |
| `contratante_id`     | FK → contratantes            | Contratante vinculado                             |
| `contratado_id`      | FK → contratados (null)      | Contratado vinculado                              |
| `objeto_contrato_id` | FK → objeto_contratos (null) | Objeto de contrato vinculado                      |
| `body`               | longText                     | Cópia do body com variáveis substituídas          |
| `is_manually_edited` | boolean                      | True se o usuário editou manualmente após geração |
| `generated_at`       | timestamp nullable           | Data da última geração automática                 |
| `created_at`         | timestamp                    | —                                                 |
| `updated_at`         | timestamp                    | —                                                 |

### 5.5 `objeto_contratos` (Objetos de Contrato)

| Coluna           | Tipo                      | Descrição             |
| ---------------- | ------------------------- | --------------------- |
| `id`             | UUID                      | —                     |
| `contratante_id` | FK → contratantes         | Contratante vinculado |
| `contratado_id`  | FK → contratados          | Contratado vinculado  |
| `tipo`           | enum(`servico`,`produto`) | Tipo do objeto        |
| `descricao`      | text                      | Descrição do objeto   |
| `quantidade`     | decimal                   | Quantidade            |
| `unidade`        | string nullable           | Unidade de medida     |
| `valor`          | decimal                   | Valor unitário        |
| `created_at`     | timestamp                 | —                     |
| `updated_at`     | timestamp                 | —                     |

---

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
- O upload não cria `contratante_contracts` — só o contrato-base.

### F2 — Visualização e Edição do Contrato-base

- O painel exibe o `body` em um editor WYSIWYG (TipTap via Filament) sob o rótulo **"Corpo do Contrato"**.
- Se o contrato foi criado via upload, o conteúdo inicial do editor é o texto extraído do arquivo Word.
- Se foi criado manualmente, o editor inicia com o conteúdo salvo anteriormente.
- O usuário edita livremente, incluindo escrevendo variáveis `$contratante.*` e `$contratado.*`.
- Um **botão flutuante "Variáveis"** (fixo no canto inferior direito, segue a rolagem da tela) exibe a lista de variáveis de contratante disponíveis. O painel mostra apenas o rótulo de cada variável; ao clicar no ícone de copiar, a variável é copiada para a área de transferência e o painel é fechado automaticamente para facilitar a inserção no editor. Um **contador numérico** exibido ao lado de cada variável indica quantas vezes ela já foi utilizada no contrato (atualizado em tempo real a cada segundo). O botão flutuante está disponível tanto na **criação** quanto na **edição** de contratos.
- Ao salvar, apenas `contracts.body` é atualizado.
- Os `contratante_contracts` **não** são recalculados automaticamente — o usuário escolhe quando regenerar.

### F3 — Cadastro de Contratantes e Contratados

- Resource **Contratantes** no Filament com todos os campos da tabela `contratantes`.
- Resource **Contratados** no Filament com todos os campos da tabela `contratados`.
- Listagem com busca por nome, CPF e e-mail.
- Validações: CPF único por usuário, e-mail único, campos obrigatórios sinalizados.

### F3b — Objeto do Contrato

- Resource **Objetos de Contrato** no Filament com os campos: contratante, contratado, tipo, descrição, quantidade, unidade e valor.
- Na criação, são listados os contratantes e contratados cadastrados pelo usuário.

### F4 — Vinculação de Contratante ao Contrato

- Na tela do contrato-base, uma seção **"Contratantes vinculados"** lista os `contratante_contracts`.
- Botão **"Vincular contratante"** abre um modal de seleção (Select do Filament com busca).
- Ao vincular, o sistema cria o `contratante_contract` e executa a substituição de variáveis imediatamente (geração inicial).

### F5 — Sistema de Variáveis

**Mapeamento completo** (`$contratante.{chave}` → `contratantes.{coluna}`):

```
$contratante.nome              → name
$contratante.email             → email
$contratante.telefone          → phone
$contratante.cpf               → cpf
$contratante.rg                → rg
$contratante.nascimento        → birth_date (formatado: d/m/Y)
$contratante.nacionalidade     → nationality
$contratante.estado_civil      → marital_status
$contratante.profissao         → profession
$contratante.endereco          → address
$contratante.endereco_numero   → address_number
$contratante.endereco_complemento → address_complement
$contratante.bairro            → neighborhood
$contratante.cidade            → city
$contratante.estado            → state
$contratante.cep               → zip_code
```

Idem para contratado com prefixo `$contratado.*`.

**Implementação via Service:**

```php
// App\Services\ContractVariableResolver

public function resolve(string $body, Contratante $contratante): string
{
    $map = $contratante->variableMap(); // retorna array ['$contratante.nome' => 'João', ...]
    return str_replace(array_keys($map), array_values($map), $body);
}
```

- Variáveis sem correspondência (campo vazio no contratante) são **mantidas** no texto (não apagadas), permitindo revisão.
- Um helper opcional pode listar as variáveis detectadas no `body` que ainda não foram resolvidas.

### F6 — Contratos por Contratante (Edição Individual)

- Cada `contratante_contract` pode ser aberto e editado separadamente no editor WYSIWYG.
- Ao editar manualmente, o campo `is_manually_edited` passa a `true`.
- Um aviso visual indica se o contrato-base foi alterado após a última geração.
- O usuário pode **"Regenerar"** o contrato do contratante (sobrescreve o `body` do `contratante_contract` com a substituição atual), perdendo edições manuais (confirmação necessária).

### F7 — Contratos Originais vs. Contratos de Contratante

|                      | Contrato-base (`contracts`)                                      | Contrato do contratante (`contratante_contracts`) |
| -------------------- | ---------------------------------------------------------------- | ------------------------------------------------- |
| Editável?            | Sim, sempre                                                      | Sim, independentemente                            |
| Contém variáveis?    | Sim                                                              | Não (substituídas)                                |
| Pode ser regenerado? | N/A                                                              | Sim (a partir do contrato-base)                   |
| Exclusão             | Remove também os contratante_contracts (soft delete recomendado) | Independente                                      |

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
│   ├── Contratante.php
│   ├── Contratado.php
│   ├── Contract.php
│   ├── ObjetoContrato.php
│   └── ContratanteContract.php
├── Services/
│   ├── ContractVariableResolver.php
│   ├── ContractImportService.php   # extrai texto de .txt/.docx/.html
│   └── ClientContractGenerator.php
├── Policies/
│   ├── ContractPolicy.php
│   └── ContratantePolicy.php
└── Http/
    └── Requests/
        ├── StoreClientRequest.php
        └── StoreContractRequest.php

tests/
├── Feature/
│   ├── ContractUploadTest.php
│   ├── ContractEditorTest.php
│   ├── ContratanteManagementTest.php
│   ├── ContratanteContractBindingTest.php
│   └── VariableResolutionTest.php
└── Unit/
    ├── ContractVariableResolverTest.php
    └── ContractImportServiceTest.php
```

---

## 8. Segurança e Autorização

- **Policies** para `Contract` e `Contratante`: usuário só acessa seus próprios registros (`user_id`).
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
