# AI usage log

One section per milestone: AI tools used, the most important prompts, and at least two cases
where the AI was wrong - what was wrong, how it was noticed and how it was fixed.

## Milestone 1 - Setup, static frontend, ERD

**Tools used:** Claude Code (Claude Opus) in the terminal - folder skeleton, static Bootstrap views,
draft ERD in Mermaid. Every view was opened in the browser (desktop and 390 px mobile width) and
reviewed by hand. The SPA shell and spapp setup follow the course reference project (week 01).

**Most important prompts**
- "Read the project requirements and the course reference repo (week-01). Set up the frontend/backend
  folder structure, .gitignore and config.example.php for a recipe-sharing app called Sofra."
- "Create static spapp views for: login, register, dashboard, recipes (cards + add/edit modal),
  recipe details, favorites, world kitchen (TheMealDB placeholder), categories, ingredients,
  reviews, users (admin tables with DataTables) and profile. Bootstrap 5 only, responsive."
- "Draw a Mermaid ERD for users, categories, recipes, ingredients, recipe_ingredients, reviews, favorites."

**Cases where the AI was wrong**
1. *What:* the favorite (heart) button on recipe cards was huge and pushed outside the card.
   *How noticed:* screenshot of the recipes page at mobile width.
   *Fix:* the button sits inside `.recipe-banner`, which sets `font-size: 2.6rem` for the big
   category icon, so the heart inherited it. Added `font-size: 1rem` (and centering) to `.fav-btn`.
2. *What:* the "Edit recipe" button on the recipe details page opened `#recipe-modal`, which
   lives in `recipes.html`. If the user opens the details page first, that view was never loaded
   by spapp, so the button did nothing.
   *How noticed:* reviewing which view each `data-bs-target` points to.
   *Fix:* the button now links to `#recipes`. In M4 the edit modal will be shared or loaded per view.
3. *What:* DataTables pagination stayed Bootstrap blue while the theme is orange.
   *How noticed:* users table screenshot.
   *Fix:* overrode the `--bs-pagination-*` variables in `css/style.css`.

## Milestone 2 - Database and DAO layer

**Tools used:** Claude Code (Claude Opus) in the terminal - MySQL schema from the M1 ERD, sample data,
`BaseDao` and one DAO per entity, and `backend/rest/dao/test.php`. The DAO structure follows the course
reference project (week 03, `BaseDao` with a column whitelist). Everything was run against a local
MySQL 9 server with PHP 8.5: the SQL file was imported and `test.php` ran CRUD on all 7 DAOs.

**Most important prompts**
- "Turn docs/ERD.md into database/sofra.sql for MySQL: primary and foreign keys, ON DELETE rules,
  unique and check constraints, and Bosnian sample data that matches the static views."
- "Write BaseDao like the week-03 reference (shared PDO connection, generic add/update/delete with a
  column whitelist, transactions) and a DAO for every entity, with JOINs for author, category and rating."
- "Write test.php that does add/read/update/delete on every DAO, tests the constraints and a rolled-back
  transaction, and cleans up after itself."

**Cases where the AI was wrong**
1. *What:* `BaseDao::update()` returned `0` when a form was saved without changes. MySQL counts only
   *changed* rows by default, so in milestone 3 the service would treat an unchanged save as "not found".
   *How noticed:* tested `update(['name' => 'Pita'], 1)` on a category that is already called Pita.
   *Fix:* turned on the `ATTR_FOUND_ROWS` PDO option, so `rowCount()` returns matched rows (now `1`;
   a missing id still returns `0`).
2. *What:* the first version of that fix used `PDO::MYSQL_ATTR_FOUND_ROWS`, which prints a deprecation
   warning on PHP 8.5. The replacement `Pdo\Mysql::ATTR_FOUND_ROWS` does not exist before PHP 8.4,
   and `composer.json` allows PHP 8.2.
   *How noticed:* the warning showed up in the `test.php` output.
   *Fix:* use `Pdo\Mysql::ATTR_FOUND_ROWS` when that class exists, otherwise the old constant.
