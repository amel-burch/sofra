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
  rest/routes/       presentation layer - FlightPHP routes
  rest/services/     business logic layer
  rest/dao/          data access layer (PDO)
  middleware/        reusable FlightPHP middleware
  logs/              info.log / error.log (NOT committed)
docs/                ERD, AI log
```

## Architecture rules
1. Never mix backend and frontend code. No PHP in `frontend/`, no HTML output from `backend/`.
2. Request flow: route → service → DAO → database. Routes never call DAOs; only services do.
3. SQL lives only in DAO classes and always uses prepared statements.
4. Secrets (DB password, JWT secret, API keys) only in `backend/config/config.php`, read via `Config::...()`.
5. The last `<section>` in `index.html` is the default view (spapp); keep `dashboard` last.

## Naming conventions
- PHP classes `PascalCase` (`RecipeService`), PHP methods/variables `snake_case` (`get_by_id`).
- JS objects `PascalCase` (`RecipeService`), JS functions/variables `camelCase`.
- DB tables plural `snake_case` (`recipes`, `recipe_ingredients`), columns `snake_case`.
- View files `kebab-case.html` (`recipe-details.html`); view id = file name without `.html`.

## Rules for the AI
- Do not add libraries or change the folder structure without asking.
- Change only the files the task is about; do not "improve" unrelated code.
- Explain every change so the student can defend it without AI.
