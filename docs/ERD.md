# Sofra - Entity Relationship Diagram (draft)

Sofra is a recipe-sharing app focused on Bosnian cuisine. Users publish recipes, list their
ingredients, review and save other people's recipes. Admins manage categories, ingredients,
users and moderate content.

```mermaid
erDiagram
    USERS ||--o{ RECIPES : "publishes"
    USERS ||--o{ REVIEWS : "writes"
    USERS ||--o{ FAVORITES : "saves"
    CATEGORIES ||--o{ RECIPES : "groups"
    RECIPES ||--o{ RECIPE_INGREDIENTS : "uses"
    INGREDIENTS ||--o{ RECIPE_INGREDIENTS : "used in"
    RECIPES ||--o{ REVIEWS : "receives"
    RECIPES ||--o{ FAVORITES : "saved as"

    USERS {
        int id PK
        varchar first_name
        varchar last_name
        varchar email UK
        varchar password "bcrypt hash"
        enum role "admin | user"
        varchar city "optional"
        timestamp created_at
    }
    CATEGORIES {
        int id PK
        varchar name UK
        varchar description
    }
    RECIPES {
        int id PK
        int user_id FK
        int category_id FK
        varchar title
        text description
        text instructions
        smallint prep_minutes
        smallint cook_minutes
        tinyint servings
        enum difficulty "easy | medium | hard"
        varchar image_url
        timestamp created_at
    }
    INGREDIENTS {
        int id PK
        varchar name UK
        varchar unit "g | ml | pcs | tbsp..."
        smallint calories_per_100 "optional"
    }
    RECIPE_INGREDIENTS {
        int id PK
        int recipe_id FK
        int ingredient_id FK
        decimal quantity
        varchar note "e.g. finely chopped"
    }
    REVIEWS {
        int id PK
        int user_id FK
        int recipe_id FK
        tinyint rating "1 - 5"
        text comment
        timestamp created_at
    }
    FAVORITES {
        int id PK
        int user_id FK
        int recipe_id FK
        timestamp created_at
    }
```

## Relationships

| Relationship                 | Type | Rule                                                              |
|------------------------------|------|-------------------------------------------------------------------|
| User → Recipes               | 1:N  | A user publishes many recipes; a recipe has one author.           |
| Category → Recipes           | 1:N  | A category groups many recipes; a recipe belongs to one category. |
| Recipe ↔ Ingredients         | M:N  | Resolved by `recipe_ingredients` (quantity per recipe).           |
| User → Reviews / Recipe → Reviews | 1:N | One review per user per recipe (unique `user_id, recipe_id`). |
| User → Favorites / Recipe → Favorites | 1:N | A user saves a recipe at most once (unique `user_id, recipe_id`). |

`reviews` and `favorites` are two many-to-many relationships between users and recipes.

## External API
[TheMealDB](https://www.themealdb.com/api.php) - "World kitchen" page: search international recipes
by name or category; the result is shown as recipe cards and can be used as inspiration
for a new Sofra recipe. Not stored in the database.
