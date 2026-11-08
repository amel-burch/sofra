<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';

/**
 * Talks to TheMealDB (https://www.themealdb.com/api.php) - the "World kitchen" page.
 *
 * Rules for every external call:
 *  - always set a timeout (the API can be slow or down)
 *  - check the HTTP status and the JSON we get back
 *  - on failure write the details to the error log and throw ExternalServiceException (503)
 *    with a FRIENDLY message -> the rest of Sofra keeps working
 *  - return only the fields our frontend needs (not the raw response)
 */
class MealDbService
{
    const UNAVAILABLE = 'The World kitchen service is not available at the moment. Please try again later.';

    /** Search international meals by name, e.g. "burek". No results is an empty list, not an error. */
    public function search($term)
    {
        $term = trim((string) $term);
        if (mb_strlen($term) < 2) {
            throw new ValidationException('Please enter at least 2 characters to search.');
        }
        $data = $this->request('search.php', ['s' => $term]);
        return array_map([$this, 'map_meal'], $data['meals'] ?? []);
    }

    /** Meal categories for the filter drop-down (Beef, Dessert, Vegetarian...). */
    public function categories()
    {
        $data = $this->request('list.php', ['c' => 'list']);
        return array_map(fn($c) => $c['strCategory'], $data['meals'] ?? []);
    }

    /** Meals of one category - TheMealDB returns only id, name and picture here. */
    public function by_category($category)
    {
        $category = trim((string) $category);
        Validator::required(['category' => $category], ['category']);
        $data = $this->request('filter.php', ['c' => $category]);
        if (empty($data['meals'])) {
            throw new NotFoundException("No meals found in the category '$category'.");
        }
        return array_map(fn($m) => [
            'id' => (int) $m['idMeal'],
            'name' => $m['strMeal'],
            'thumbnail' => $m['strMealThumb']
        ], $data['meals']);
    }

    /** Full details of one meal (ingredients, instructions, video). */
    public function get_by_id($id)
    {
        Validator::integer($id, 'id', 1);
        $data = $this->request('lookup.php', ['i' => $id]);
        if (empty($data['meals'][0])) {
            throw new NotFoundException('Meal not found in TheMealDB.');
        }
        return $this->map_meal($data['meals'][0]);
    }

    /** "Surprise me" button on the World kitchen page. */
    public function random()
    {
        $data = $this->request('random.php');
        if (empty($data['meals'][0])) {
            error_log('TheMealDB random.php returned no meal');
            throw new ExternalServiceException(self::UNAVAILABLE);
        }
        return $this->map_meal($data['meals'][0]);
    }

    /** TheMealDB keeps ingredients in 20 numbered columns (strIngredient1..20) - we turn them into a list. */
    private function map_meal($m)
    {
        $ingredients = [];
        for ($i = 1; $i <= 20; $i++) {
            $name = trim((string) ($m["strIngredient$i"] ?? ''));
            if ($name !== '') {
                $ingredients[] = ['name' => $name, 'measure' => trim((string) ($m["strMeasure$i"] ?? ''))];
            }
        }
        return [
            'id' => (int) $m['idMeal'],
            'name' => $m['strMeal'],
            'category' => $m['strCategory'] ?? null,
            'area' => $m['strArea'] ?? null,
            'instructions' => $m['strInstructions'] ?? null,
            'thumbnail' => $m['strMealThumb'] ?? null,
            'tags' => !empty($m['strTags']) ? array_map('trim', explode(',', $m['strTags'])) : [],
            'youtube' => $m['strYoutube'] ?: null,
            'source' => $m['strSource'] ?? null,
            'ingredients' => $ingredients
        ];
    }

    /** One GET request with cURL - all error handling in one place. */
    private function request($endpoint, $params = [])
    {
        $url = rtrim(Config::MEALDB_BASE_URL(), '/') . '/' . Config::MEALDB_API_KEY() . '/' . $endpoint;
        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => (int) Config::MEALDB_TIMEOUT(),
            CURLOPT_HTTPHEADER => ['Accept: application/json']
        ]);
        $body = curl_exec($curl);
        $error = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($body === false) {                       // timeout, DNS problem, no internet...
            error_log("TheMealDB request failed: $error ($endpoint)");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }
        if ($status !== 200) {
            error_log("TheMealDB returned HTTP $status ($endpoint)");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }
        $data = json_decode($body, true);
        if (!is_array($data)) {
            error_log("TheMealDB returned invalid JSON ($endpoint)");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }
        return $data;
    }
}
