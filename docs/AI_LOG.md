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
