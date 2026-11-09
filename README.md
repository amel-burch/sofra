# Sofra

Recipe-sharing single-page application focused on Bosnian cuisine (IT 2001 Web Programming, IBU 2026/27).
Users publish recipes with ingredients, rate and review them and keep a list of favorites.
Admins manage categories, ingredients and users and moderate reviews.

> **Status:** Milestone 3 - REST API (FlightPHP) with a service layer, full CRUD for all 7 entities,
> OpenAPI/Swagger docs and the TheMealDB integration. The frontend is still static; it is connected in milestone 4.

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
recipes by name or category, open a meal's details or get a random one for inspiration.
The frontend never calls TheMealDB directly: `MealDbService` (backend) calls it with a 5 s timeout, writes failures
to the error log and answers `503` with a friendly message, so the rest of Sofra keeps working.
Base URL, API key (`1` = public test key) and timeout are in `backend/config/config.php`.

| Endpoint | What it does |
|----------|--------------|
| `GET /world/search?q=burek` | search meals by name |
| `GET /world/categories` | category names for the filter |
| `GET /world/categories/{category}` | meals of one category |
| `GET /world/meals/{id}` | full meal: ingredients, instructions, video |
| `GET /world/random` | random meal ("Surprise me") |

## Run locally
```bash
mysql -u root -p < database/sofra.sql                            # creates the "sofra" database with sample data
cp backend/config/config.example.php backend/config/config.php   # then put your DB user/password in it
(cd backend && composer install)                                 # FlightPHP + swagger-php (vendor/ is not committed)
php backend/rest/dao/test.php                                    # optional: CRUD smoke test of every DAO
php -S localhost:8000 server.php
```
- Frontend: http://localhost:8000/frontend/
- Backend: http://localhost:8000/backend/
- Swagger: http://localhost:8000/backend/public/v1/docs/

XAMPP: clone into `htdocs`, import `database/sofra.sql` in phpMyAdmin and open `http://localhost/sofra/frontend/`.

## Data access layer (milestone 2)
`backend/rest/dao/BaseDao.php` has the reusable CRUD (`get_all`, `get_by_id`, `add`, `update`, `delete`,
`count_all`) plus transactions. Every entity has its own DAO that extends it:
`UserDao`, `CategoryDao`, `IngredientDao`, `RecipeDao`, `RecipeIngredientDao`, `ReviewDao`, `FavoriteDao`.
- All SQL uses PDO prepared statements; `add`/`update` only write whitelisted columns.
- Read methods JOIN the related names (author, category, ingredient, rating) so the frontend needs one request.
- DB credentials come from `backend/config/config.php` (not committed).

## Business logic and REST API (milestone 3)
Request flow: **route** (`backend/rest/routes`) → **service** (`backend/rest/services`) → **DAO** → MySQL.
- `BaseService` has the shared CRUD (404 when an id does not exist, validation before every write).
  `PUT` changes only the sent fields, but the rules are checked on the whole entity.
- One service per entity with its rules (`utils/Validator.php`), for example:
  unique emails, category and ingredient names; bcrypt passwords that are never returned; the last admin cannot
  be deleted or demoted; categories/ingredients still in use cannot be deleted; a recipe and its ingredient list
  are saved in one transaction; the same ingredient only once per recipe; ratings 1-5; one review per user per
  recipe and no reviews of your own recipe; a recipe is saved to favorites only once; author/owner ids cannot be
  changed on update.
- Errors are JSON `{"error": "..."}` from one handler in `backend/index.php`: `400` invalid data or broken rule
  (also broken JSON), `404` unknown id or endpoint, `503` TheMealDB down, `500` everything else (details only in
  the server log, never SQL or stack traces). `401`/`403` come with JWT and roles in milestone 4.

| Resource | Endpoints |
|----------|-----------|
| users | `GET/POST /users`, `GET/PUT/DELETE /users/{id}`, `GET /users/{id}/favorites` |
| categories | `GET/POST /categories`, `GET/PUT/DELETE /categories/{id}` |
| ingredients | `GET/POST /ingredients` (`?search=`), `GET/PUT/DELETE /ingredients/{id}` |
| recipes | `GET/POST /recipes` (`?search=`, `?category_id=`, `?user_id=`), `GET/PUT/DELETE /recipes/{id}`, `GET /recipes/top-rated`, `GET /recipes/{id}/ingredients`, `GET /recipes/{id}/reviews` |
| recipe-ingredients | `GET/POST /recipe-ingredients`, `GET/PUT/DELETE /recipe-ingredients/{id}` |
| reviews | `GET/POST /reviews`, `GET/PUT/DELETE /reviews/{id}` |
| favorites | `GET/POST /favorites`, `GET/PUT/DELETE /favorites/{id}` |
| world kitchen | see External API above |

Every endpoint is documented with swagger-php attributes in `backend/public/v1/docs/` (`doc_setup.php` for
schemas, `paths/*.php` one file per route file) and can be tried out in Swagger UI.

## Links
- Swagger docs: http://localhost:8000/backend/public/v1/docs/ (local)
- Deployed app: milestone 5

## Project structure
```
frontend/   index.html, views/, css/, js/, assets/
backend/    index.php, config/, rest/{routes,services,dao}, utils/, public/v1/docs/ (Swagger), middleware/, logs/
database/   sofra.sql (schema + sample data)
docs/       ERD.md, AI_LOG.md
AGENTS.md   context for AI assistants
```
