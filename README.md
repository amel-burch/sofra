# Sofra

Recipe-sharing single-page application focused on Bosnian cuisine (IT 2001 Web Programming, IBU 2026/27).
Users publish recipes with ingredients, rate and review them and keep a list of favorites.
Admins manage categories, ingredients and users and moderate reviews.

> **Status:** Milestone 1 - project structure, static SPA, draft ERD. The backend is not connected yet.

## Entities
users, categories, recipes, ingredients, recipe_ingredients, reviews, favorites.
See [docs/ERD.md](docs/ERD.md) for the diagram and relationships.

## Roles
| Role  | Can do |
|-------|--------|
| admin | Everything: CRUD on all entities, manage users, moderate reviews |
| user  | Browse recipes, CRUD on own recipes, reviews and favorites |

Test accounts: added in milestone 4.

## External API
[TheMealDB](https://www.themealdb.com/api.php) powers the **World kitchen** page: search international
recipes by name or category for inspiration. Planned for milestone 3.

## Run locally
```bash
cp backend/config/config.example.php backend/config/config.php   # then edit values
php -S localhost:8000 server.php
```
- Frontend: http://localhost:8000/frontend/
- Backend: http://localhost:8000/backend/

XAMPP: clone into `htdocs` and open `http://localhost/sofra/frontend/`.
Database import (`.sql`) is added in milestone 2.

## Links
- Swagger docs: milestone 3
- Deployed app: milestone 5

## Project structure
```
frontend/   index.html, views/, css/, js/, assets/
backend/    index.php, config/, rest/{routes,services,dao}, middleware/, logs/
docs/       ERD.md, AI_LOG.md
AGENTS.md   context for AI assistants
```
