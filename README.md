# CardHub

Sistema web para gerenciamento de cartas colecionáveis, desenvolvido como desafio técnico para a vaga DEV.

O sistema permite autenticação de usuário e gerenciamento de cartas dos jogos Magic: The Gathering, Pokémon e Yu-Gi-Oh!, com cadastro, edição, exclusão, filtros por jogo e edição, imagens e raridades.

## Tecnologias utilizadas

### Backend

* PHP 8.5
* MySQL 8
* PDO
* API REST

### Frontend

* HTML5
* CSS3
* JavaScript Vanilla

### Outros

* JSON para armazenamento das edições e raridades de cada jogo.

Não foram utilizados frameworks ou bibliotecas JavaScript/CSS.

---

## Funcionalidades

* Login de usuário
* Cadastro de usuário pela API
* Logout
* Listagem de cartas
* Cadastro de cartas
* Edição de cartas
* Exclusão de cartas
* Upload de imagens
* Filtro por jogo
* Filtro por edição
* Carregamento dinâmico de edições de acordo com o jogo selecionado
* Carregamento dinâmico de raridades de acordo com o jogo selecionado
* Validação de jogo, edição e raridade no backend
* Prevenção de cartas duplicadas dentro da mesma edição
* Organização das cartas por raridade

### Regra de duplicidade

Uma carta não pode possuir o mesmo nome em inglês dentro da mesma edição.

O mesmo nome pode existir em edições diferentes.

Exemplo:

* `Forest` + `The Hobbit` → não permite duplicação
* `Forest` + `Dominaria` → permitido

---

## Estrutura do projeto

```text
CardHub/
├── index.php
├── README.md
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── CardController.php
│   │   └── GameController.php
│   └── models/
│       ├── Card.php
│       └── User.php
├── config/
│   └── database.php
├── data/
│   └── games/
│       ├── mtg.json
│       ├── ptcg.json
│       └── ygo.json
├── database/
│   └── database.sql
└── public/
    ├── css/
    │   └── style.css
    ├── images/
    │   └── cards/
    ├── js/
    │   └── app.js
    └── index.html
```

---

## Requisitos

Para executar o projeto localmente, é necessário ter instalado:

* PHP 8.5 ou compatível
* MySQL 8 ou compatível
* MySQL Workbench (opcional, utilizado para gerenciamento do banco)
* Navegador web

---

## Configuração do banco de dados

O arquivo responsável pela estrutura inicial do banco está localizado em:

```text
database/database.sql
```

Ele cria:

* Banco de dados `cardhub`
* Tabela `users`
* Tabela `cards`
* Usuário inicial do sistema para testes
* Carta inicial para demonstração

### 1. Executar o SQL

Abra o arquivo:

```text
database/database.sql
```

no MySQL Workbench e execute o script.

### 2. Configurar a conexão

A conexão com o banco está localizada em:

```text
config/database.php
```
Por segurança, a senha utilizada no ambiente de desenvolvimento não é disponibilizada no repositório.

Antes de executar o projeto, configure as credenciais do seu ambiente MySQL no arquivo:

```php
$host = 'localhost'; 
$db = 'cardhub'; 
$user = 'cardhub_user'; 
$password = 'DATABASE_PASSWORD';
```
Substitua cardhub_user e DATABASE_PASSWORD pelas credenciais de um usuário do MySQL que possua permissão para acessar o banco cardhub.
---

## Executando o projeto

Abra o terminal na pasta raiz do projeto:

```powershell
cd "C:\caminho\para\CardHub"
```

Execute o servidor PHP:

```powershell
php -S localhost:8000
```

Depois acesse no navegador:

```text
http://localhost:8000
```

---

## Credenciais para teste

O banco possui um usuário inicial para testes:

```text
Usuário: CardUser
Senha: CHUser
```

Após o login, o usuário pode acessar o dashboard e gerenciar as cartas.

---

## Jogos disponíveis

### Magic: The Gathering

Edições disponíveis:

* Dominaria
* War of the Spark
* Throne of Eldraine
* The Hobbit
* Marvel Super Heroes

Raridades:

* Comum
* Incomum
* Rara
* Mítica Rara

### Pokémon

Edições disponíveis:

* Base Set
* Sword & Shield
* Scarlet & Violet
* 30th Celebration
* Chaos Rising

### Yu-Gi-Oh!

Edições disponíveis:

* Legend of Blue Eyes White Dragon
* Metal Raiders
* Starter Deck: Yugi
* Rise of the Duelist
* Blazing Dominion

As edições e raridades são carregadas a partir dos arquivos JSON localizados em:

```text
data/games/
```

---

## Decisões de UX e Produto

### 1. Carregamento das edições baseado no jogo selecionado

O campo de edição permanece desabilitado até que um jogo seja selecionado.

Após a seleção do jogo, o sistema busca as edições correspondentes e atualiza o campo automaticamente.

**Motivo:** evita que o usuário selecione uma edição pertencente a outro jogo e reduz a quantidade de informações apresentadas simultaneamente.

### 2. Organização das cartas por raridade

As cartas são agrupadas visualmente de acordo com sua raridade.

**Motivo:** facilita a identificação e navegação pelo catálogo, especialmente quando existe uma quantidade maior de cartas cadastradas.

### 3. Interface responsiva

O dashboard e o formulário de gerenciamento foram adaptados para diferentes tamanhos de tela.

Em telas menores, os controles são reorganizados para facilitar a utilização em dispositivos móveis.

**Motivo:** permitir que o gerenciamento do catálogo continue utilizável em diferentes dispositivos.

### 4. Prevenção de duplicidade

O sistema verifica no backend se já existe uma carta com o mesmo nome em inglês dentro da mesma edição.

**Motivo:** evitar registros duplicados no catálogo sem impedir que uma mesma carta seja cadastrada em edições diferentes.

---

## API

Principais endpoints:

### Autenticação

```text
POST /api/login
POST /api/register
POST /api/logout
```

### Cartas

```text
GET    /api/cards
POST   /api/cards
PUT    /api/cards/{id}
DELETE /api/cards/{id}
```

### Jogos

```text
GET /api/{game}/editions
GET /api/{game}/rarities
```

As operações de gerenciamento de cartas exigem autenticação.

---

## Observações

As imagens das cartas utilizadas pelo sistema ficam armazenadas em:

```text
public/images/cards/
```
O projeto inclui algumas imagens de cartas para demonstração e testes iniciais. 
Essas imagens podem ser utilizadas no cadastro e na edição de cartas. 
O sistema também permite o cadastro de novas cartas utilizando outras imagens fornecidas pelo usuário.

As informações de edições e raridades ficam nos arquivos JSON dentro de:

```text
data/games/
```

O backend realiza validações para garantir que jogo, edição e raridade correspondam aos dados disponíveis para cada jogo.

O banco de dados inicial é criado através do arquivo database/database.sql. 
Após a execução do script, é necessário configurar as credenciais de acesso ao MySQL no arquivo config/database.php.
