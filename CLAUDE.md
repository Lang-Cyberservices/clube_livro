# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

**Clube do Livro** — a PHP 8.1 + CodeIgniter 4 web app for managing a book club. Users authenticate with phone + password, read/comment on the current highlighted book, and participate in a voting cycle to choose the next book.

## Commands

```bash
# Database (MariaDB via Docker)
docker compose up -d

# Run migrations and seed initial data
php spark migrate
php spark db:seed ClubSeeder   # creates admin user: 11999990001 / admin123

# Dev server → http://localhost:8080
php spark serve

# Rollback migrations
php spark migrate:rollback

# Code style check / fix
composer cs
composer cs-fix

# Static analysis
composer analyze
```

There are no application-level tests — `composer test` runs the CI4 framework's own test suite.

## Architecture

### Auth & filter chain

Authentication is session-based (custom, not CI Shield). The session stores `id`, `name`, `phone`, `role`, and `must_change_password`. Helper functions in `app/Helpers/auth_helper.php` expose `is_logged_in()`, `is_admin()`, `current_user()`, `current_user_id()`, and `must_change_password()` — available in all controllers and views via `BaseController::$helpers`.

Filter aliases in `app/Config/Filters.php`:
- `auth` — redirect to login if not authenticated
- `authpass` — `auth` + `passwordchange` (forces password update on first login)
- `authpassadmin` — `authpass` + `admin` (restricts to role=admin)

`passwordchange` is applied globally (before every request) except `auth/primeiro-acesso` and `logout`.

### Routing

Auto-routing is disabled. All routes are explicit in `app/Config/Routes.php`. Public routes (`/`, `/sobre`, `/livros-anteriores`, `/livros/:num`) require no auth. The `/admin/*` group uses the `authpassadmin` filter.

### Comment visibility

Before `meeting_happened = 1` on a book, each user sees only their own comments and replies. After the meeting, all comments become visible. This logic lives in `CommentModel::getVisibleComments()` and `CommentReplyModel::getRepliesForComments()`.

### Voting lifecycle

`BookVotingService` (`app/Libraries/BookVotingService.php`) orchestrates the full voting cycle:
1. **collecting** — open session auto-created when no ongoing book; members and admins submit suggestions (`book_suggestions`)
2. **active** — admin activates voting; members cast one vote each (`book_votes`, unique per session+user)
3. **finished** — admin finalizes; the top-voted suggestion is inserted as a new `books` row with `is_current = 1`, and `setCurrentBook()` marks all others non-current in a transaction

`VotingSessionModel` status constants: `STATUS_COLLECTING`, `STATUS_ACTIVE`, `STATUS_FINISHED`.

### Views

Views use CI4's template inheritance. All pages extend `app/Views/layouts/main.php` via `$this->extend()` / `$this->section('content')`. Shared partials: `partials/navbar.php` and `partials/flash.php`. CSS variables and Bootstrap 5.3 are inlined in the layout; no build step is required.

### Database

Database name: `folhas`. Credentials come from `.env`. Migrations in `app/Database/Migrations/` are numbered chronologically. The `run/` directory holds MariaDB data files (Docker volume mount).
