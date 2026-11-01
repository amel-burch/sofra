<?php
/*
 * DAO demo / smoke test - php backend/rest/dao/test.php
 * Runs Create, Read, Update and Delete on every DAO. Everything it creates is deleted again.
 */
header('Content-Type: text/plain'); // keeps the line breaks when opened in a browser; ignored on the CLI
require_once __DIR__ . '/UserDao.php';
require_once __DIR__ . '/CategoryDao.php';
require_once __DIR__ . '/IngredientDao.php';
require_once __DIR__ . '/RecipeDao.php';
require_once __DIR__ . '/RecipeIngredientDao.php';
require_once __DIR__ . '/ReviewDao.php';
require_once __DIR__ . '/FavoriteDao.php';

function title($text) { echo "\n=== $text ===\n"; }

/** add -> get_by_id -> update -> get_by_id -> delete -> get_by_id, prints one line per step. */
function crud($name, $dao, $entity, $changes)
{
    $added = $dao->add($entity);
    $id = $added['id'];
    echo "$name: added #$id";
    echo $dao->get_by_id($id) ? ", read ok" : ", READ FAILED";
    echo ", updated " . $dao->update($changes, $id) . " row";
    $row = $dao->get_by_id($id);
    $key = array_key_first($changes);
    echo ($row[$key] == $changes[$key]) ? " ($key = {$row[$key]})" : " UPDATE NOT SAVED";
    return $id;
}

function remove($name, $dao, $id)
{
    echo "$name #$id: deleted " . $dao->delete($id) . " row, ";
    echo $dao->get_by_id($id) === null ? "gone\n" : "STILL THERE\n";
}

$users = new UserDao();
$categories = new CategoryDao();
$ingredients = new IngredientDao();
$recipes = new RecipeDao();
$recipe_ingredients = new RecipeIngredientDao();
$reviews = new ReviewDao();
$favorites = new FavoriteDao();

title('READ - one DAO per table');
echo "Users:              " . count($users->get_all()) . "\n";
echo "Categories:         " . count($categories->get_all()) . "\n";
echo "Ingredients:        " . count($ingredients->get_all()) . "\n";
echo "Recipes:            " . count($recipes->get_all()) . "\n";
echo "Recipe ingredients: " . count($recipe_ingredients->get_all()) . "\n";
echo "Reviews:            " . count($reviews->get_all()) . "\n";
echo "Favorites:          " . count($favorites->get_all()) . "\n";

title('Recipe #1 - author, category and rating come from JOINs in ONE query');
print_r($recipes->get_by_id(1));

title('Ingredients of recipe #1');
foreach ($recipe_ingredients->get_by_recipe(1) as $ri) {
    echo "- {$ri['quantity']} {$ri['unit']} {$ri['ingredient_name']}" . ($ri['note'] ? " ({$ri['note']})" : "") . "\n";
}

title('Top rated recipes');
foreach ($recipes->get_top_rated(3) as $r) {
    echo "- {$r['title']}: {$r['average_rating']} ({$r['review_count']} reviews)\n";
}

title('User lists never contain the password hash - only get_by_email does (login)');
echo "get_by_id has password: " . (array_key_exists('password', $users->get_by_id(1)) ? 'yes' : 'no') . "\n";
echo "get_by_email has password: " . (array_key_exists('password', $users->get_by_email('amel@sofra.ba')) ? 'yes' : 'no') . "\n";

title('CRUD on every entity');
$user_id = crud('User', $users, [
    'first_name' => 'Test', 'last_name' => 'User', 'email' => 'test@sofra.ba',
    'password' => password_hash('Test123!', PASSWORD_BCRYPT), 'city' => 'Bihać',
    'hacker' => 'DROP TABLE users'  // not a column -> silently dropped by only_columns()
], ['city' => 'Banja Luka']);
echo "\n";
$category_id = crud('Category', $categories, ['name' => 'Test category', 'description' => 'Created by test.php'], ['description' => 'Updated']);
echo "\n";
$ingredient_id = crud('Ingredient', $ingredients, ['name' => 'Test ingredient', 'unit' => 'g'], ['calories_per_100' => 100]);
echo "\n";
$recipe_id = crud('Recipe', $recipes, [
    'user_id' => $user_id, 'category_id' => $category_id, 'title' => 'Test recipe',
    'instructions' => 'Mix and bake.', 'prep_minutes' => 10, 'cook_minutes' => 20, 'servings' => 2, 'difficulty' => 'easy'
], ['servings' => 4]);
echo "\n";
$ri_id = crud('Recipe ingredient', $recipe_ingredients, ['recipe_id' => $recipe_id, 'ingredient_id' => $ingredient_id, 'quantity' => 250], ['quantity' => '300.00']);
echo "\n";
$review_id = crud('Review', $reviews, ['user_id' => $user_id, 'recipe_id' => $recipe_id, 'rating' => 4, 'comment' => 'Nice'], ['rating' => 5]);
echo "\n";
$favorite_id = crud('Favorite', $favorites, ['user_id' => $user_id, 'recipe_id' => 1], ['recipe_id' => 2]);
echo "\n";

title('Database constraints');
try {
    $reviews->add(['user_id' => $user_id, 'recipe_id' => $recipe_id, 'rating' => 3]);
    echo "Second review by the same user: SAVED (should not happen)\n";
} catch (PDOException $e) {
    echo "Second review by the same user: refused (unique user_id + recipe_id)\n";
}
try {
    $reviews->add(['user_id' => 1, 'recipe_id' => $recipe_id, 'rating' => 9]);
    echo "Rating 9: SAVED (should not happen)\n";
} catch (PDOException $e) {
    echo "Rating 9: refused (CHECK rating BETWEEN 1 AND 5)\n";
}
try {
    $categories->delete($category_id);
    echo "Delete a category that still has recipes: DELETED (should not happen)\n";
} catch (PDOException $e) {
    echo "Delete a category that still has recipes: refused (ON DELETE RESTRICT)\n";
}

title('Transaction - a recipe and its ingredients are saved together or not at all');
$before = $recipes->count_all();
try {
    $recipes->begin_transaction();
    $new = $recipes->add(['user_id' => 1, 'category_id' => 1, 'title' => 'Half saved', 'instructions' => '-']);
    $recipe_ingredients->add(['recipe_id' => $new['id'], 'ingredient_id' => 999999, 'quantity' => 1]); // no such ingredient
    $recipes->commit();
} catch (PDOException $e) {
    $recipes->rollback();
}
echo "Recipes before: $before, after the failed save: " . $recipes->count_all() . "\n";

title('DELETE (children first)');
remove('Favorite', $favorites, $favorite_id);
remove('Review', $reviews, $review_id);
remove('Recipe ingredient', $recipe_ingredients, $ri_id);
remove('Recipe', $recipes, $recipe_id);
remove('Ingredient', $ingredients, $ingredient_id);
remove('Category', $categories, $category_id);
remove('User', $users, $user_id);
