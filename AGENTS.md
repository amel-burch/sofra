# AGENTS.md - context for AI coding assistants

> Read this file completely before suggesting or writing code. Updated in every milestone.

## Project
**Sofra** - a single-page application for sharing recipes, focused on Bosnian cuisine
(IT 2001 Web Programming project). Users publish recipes with ingredients, review and save
recipes. Admins manage categories, ingredients, users and moderate reviews. International
recipe ideas come from TheMealDB API.

Roles: `admin`, `user`.
Entities: users, categories, recipes, ingredients, recipe_ingredients, reviews, favorites.

## Technology stack
- **Frontend:** HTML5, CSS3, JavaScript, jQuery 3, Bootstrap 5, spapp (hash-based SPA router),
  DataTables, Highcharts. No JS frameworks (React/Vue/Angular are not allowed).
- **Backend:** PHP 8.2+, FlightPHP 3, PDO (MySQL). No other PHP framework.
- **Database:** MySQL 8.
- **Local server:** `php -S localhost:8000 server.php` or XAMPP.

## Folder structure
```
frontend/            static SPA - only HTML, CSS and JS
  index.html         app shell; every <section> in <main id="spapp"> is one view
  views/             HTML templates loaded by spapp (one file per view)
  css/ js/ assets/
backend/             REST API - only PHP
  index.php          entry point
  config/            config.example.php (committed), config.php (NOT committed)
  rest/routes/       presentation layer - FlightPHP routes (one file per entity)
  rest/services/     business logic layer (BaseService + one service per entity, MealDbService)
  rest/dao/          data access layer (PDO)
  utils/             Validator (400 rules) and exceptions with HTTP status codes
  public/v1/docs/    Swagger UI (index.html), swagger.php, doc_setup.php (schemas), paths/ (one file per route file)
  middleware/        reusable FlightPHP middleware
  logs/              info.log / error.log (NOT committed)
database/            sofra.sql - schema + sample data (re-import to reset the DB)
docs/                ERD, AI log
```

## Architecture rules
1. Never mix backend and frontend code. No PHP in `frontend/`, no HTML output from `backend/`.
2. Request flow: route → service → DAO → database. Routes never call DAOs; only services do.
3. SQL lives only in DAO classes and always uses prepared statements.
4. Secrets (DB password, JWT secret, API keys) only in `backend/config/config.php`, read via `Config::...()`.
5. The last `<section>` in `index.html` is the default view (spapp); keep `dashboard` last.
6. Every DAO extends `BaseDao` and passes its table name and the list of writable columns.
   Add custom queries only as DAO methods; use `query` / `query_unique` / `execute` with named parameters.
7. Never return the `password` column except from `UserDao::get_by_email` (login).
8. Schema changes go into `database/sofra.sql` AND `docs/ERD.md` in the same commit.
9. Routes stay thin: read the request, call ONE service method, `Flight::json(...)`. No try/catch in routes.
10. Services signal problems by throwing `ValidationException` (400), `NotFoundException` (404),
    `ExternalServiceException` (503) from `utils/exceptions.php`. `Flight::map('error')` in `index.php`
    is the only place that builds error responses. Never echo SQL, stack traces or file paths.
11. Every service extends `BaseService` and puts its rules in `validate($entity, $id)`; `$id` is null on create.
    Use `$nullable` for optional text columns and `$immutable` for ids that must not change on update.
12. Every new or changed route gets its OpenAPI attributes in `public/v1/docs/paths/<Entity>Paths.php` in the same commit.
13. External APIs are called only from a backend service with cURL, a timeout from Config, status/JSON checks,
    `error_log()` on failure and a friendly 503 message. Return only the fields the frontend needs.

## Naming conventions
- PHP classes `PascalCase` (`RecipeService`), PHP methods/variables `snake_case` (`get_by_id`).
- JS objects `PascalCase` (`RecipeService`), JS functions/variables `camelCase`.
- DB tables plural `snake_case` (`recipes`, `recipe_ingredients`), columns `snake_case`.
- View files `kebab-case.html` (`recipe-details.html`); view id = file name without `.html`.

## Rules for the AI
- Do not add libraries or change the folder structure without asking.
- Change only the files the task is about; do not "improve" unrelated code.
- Explain every change so the student can defend it without AI.
