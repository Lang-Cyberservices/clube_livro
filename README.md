# Clube do Livro

Aplicação completa em `PHP + CodeIgniter 4 + Bootstrap 5` para gerenciar um clube do livro com:

- livro atual em destaque
- autenticação com perfis `admin` e `user`
- comentários e respostas encadeadas
- visibilidade restrita dos comentários antes do encontro
- painel administrativo para livros e usuários

## Requisitos

- PHP 8.1+
- MySQL/MariaDB

## Como rodar

1. Crie um banco chamado `folhas`.
2. Ajuste as credenciais no arquivo `.env`.
3. Execute as migrations:

```bash
php spark migrate
```

4. Popule com dados iniciais:

```bash
php spark db:seed ClubSeeder
```

5. Suba o servidor local:

```bash
php spark serve
```

6. Acesse `http://localhost:8080`.

## Credenciais iniciais

- Admin: `11999990001` / `admin123`

## Estrutura principal

- `app/Controllers`: home, autenticação, comentários e administração
- `app/Models`: usuários, livros, comentários e respostas
- `app/Database/Migrations`: estrutura completa do banco
- `app/Database/Seeds/ClubSeeder.php`: dados iniciais
- `app/Views`: layout Bootstrap 5, home, login e painel admin
