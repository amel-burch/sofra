# Sofra

Recipe-sharing single-page application focused on Bosnian cuisine (IT 2001 Web Programming, IBU 2026/27).
Users publish recipes with ingredients, rate and review them and keep a list of favorites.
Admins manage categories, ingredients and users and moderate reviews.

> **Status:** Milestone 2 - MySQL database with sample data and a DAO layer (CRUD for all 7 entities).
> The frontend is still static; routes and services come in milestone 3.

## Entities
users, categories, recipes, ingredients, recipe_ingredients, reviews, favorites.
See [docs/ERD.md](docs/ERD.md) for the diagram and relationships.

## Roles
| Role  | Can do |
|-------|--------|
| admin | Everything: CRUD on all entities, manage users, moderate reviews |
| user  | Browse recipes, CRUD on own recipes, reviews and favorites |

Test accounts (from `database/sofra.sql`; login works from milestone 4):

| Email | Password | Role |
|-------|----------|------|
| amel@sofra.ba | Admin123! | admin |
| amra@sofra.ba | User123! | user |
| emina@sofra.ba | User123! | user |
| lejla@sofra.ba | User123! | user |

## External API
[TheMealDB](https://www.themealdb.com/api.php) powers the **World kitchen** page: search international
recipes by name or category for inspiration. Planned for milestone 3.

## Run locally
```bash
mysql -u root -p < database/sofra.sql                            # creates the "sofra" database with sample data
cp backend/config/config.example.php backend/config/config.php   # then put your DB user/password in it
php backend/rest/dao/test.php                                    # optional: CRUD smoke test of every DAO
php -S localhost:8000 server.php
```
- Frontend: http://localhost:8000/frontend/
- Backend: http://localhost:8000/backend/

XAMPP: clone into `htdocs`, import `database/sofra.sql` in phpMyAdmin and open `http://localhost/sofra/frontend/`.

## Data access layer (milestone 2)
`backend/rest/dao/BaseDao.php` has the reusable CRUD (`get_all`, `get_by_id`, `add`, `update`, `delete`,
`count_all`) plus transactions. Every entity has its own DAO that extends it:
`UserDao`, `CategoryDao`, `IngredientDao`, `RecipeDao`, `RecipeIngredientDao`, `ReviewDao`, `FavoriteDao`.
- All SQL uses PDO prepared statements; `add`/`update` only write whitelisted columns.
- Read methods JOIN the related names (author, category, ingredient, rating) so the frontend needs one request.
- DB credentials come from `backend/config/config.php` (not committed).

## Links
- Swagger docs: milestone 3
- Deployed app: milestone 5

## Project structure
```
frontend/   index.html, views/, css/, js/, assets/
backend/    index.php, config/, rest/{routes,services,dao}, middleware/, logs/
database/   sofra.sql (schema + sample data)
docs/       ERD.md, AI_LOG.md
AGENTS.md   context for AI assistants
```
