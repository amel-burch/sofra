-- =====================================================================
-- Sofra - recipe sharing database (MySQL 8)
-- Import:  mysql -u root -p < database/sofra.sql
--          (or phpMyAdmin / DBeaver -> Import -> choose this file)
--
-- Test accounts (password in brackets):
--   amel@sofra.ba   (Admin123!)  - role admin
--   amra@sofra.ba   (User123!)   - role user
--   emina@sofra.ba  (User123!)   - role user
--   lejla@sofra.ba  (User123!)   - role user
-- =====================================================================

-- Read this file as UTF-8 (names like Karšić, Hadžić, čorba)
SET NAMES utf8mb4;

DROP DATABASE IF EXISTS sofra;
CREATE DATABASE sofra CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sofra;

-- ---------------------------------------------------------------------
-- Tables
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(50)  NOT NULL,
    last_name   VARCHAR(50)  NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,                 -- bcrypt hash, never plain text
    role        ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    city        VARCHAR(60)  NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(60)  NOT NULL UNIQUE,
    description VARCHAR(255) NULL
);

CREATE TABLE recipes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT          NOT NULL,
    category_id  INT          NOT NULL,
    title        VARCHAR(150) NOT NULL,
    description  TEXT         NULL,
    instructions TEXT         NOT NULL,
    prep_minutes SMALLINT     NOT NULL DEFAULT 0,
    cook_minutes SMALLINT     NOT NULL DEFAULT 0,
    servings     TINYINT      NOT NULL DEFAULT 4,
    difficulty   ENUM('easy', 'medium', 'hard') NOT NULL DEFAULT 'medium',
    image_url    VARCHAR(255) NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    -- deleting a user deletes their recipes; a category with recipes cannot be deleted
    CONSTRAINT fk_recipes_user     FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    CONSTRAINT fk_recipes_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    CONSTRAINT chk_recipes_minutes  CHECK (prep_minutes >= 0 AND cook_minutes >= 0),
    CONSTRAINT chk_recipes_servings CHECK (servings > 0)
);

CREATE TABLE ingredients (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(80) NOT NULL UNIQUE,
    unit             VARCHAR(10) NOT NULL,             -- g, ml, pcs, tbsp, tsp
    calories_per_100 SMALLINT    NULL
);

-- M:N recipes <-> ingredients, with the quantity for this recipe
CREATE TABLE recipe_ingredients (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id     INT           NOT NULL,
    ingredient_id INT           NOT NULL,
    quantity      DECIMAL(8, 2) NOT NULL,
    note          VARCHAR(100)  NULL,
    CONSTRAINT fk_ri_recipe     FOREIGN KEY (recipe_id)     REFERENCES recipes(id)     ON DELETE CASCADE,
    CONSTRAINT fk_ri_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE RESTRICT,
    CONSTRAINT uq_ri UNIQUE (recipe_id, ingredient_id),
    CONSTRAINT chk_ri_quantity CHECK (quantity > 0)
);

CREATE TABLE reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT     NOT NULL,
    recipe_id  INT     NOT NULL,
    rating     TINYINT NOT NULL,
    comment    TEXT    NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_user   FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    CONSTRAINT fk_reviews_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    CONSTRAINT uq_reviews UNIQUE (user_id, recipe_id),  -- one review per user per recipe
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
);

CREATE TABLE favorites (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    recipe_id  INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_favorites_user   FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    CONSTRAINT fk_favorites_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    CONSTRAINT uq_favorites UNIQUE (user_id, recipe_id)  -- a recipe is saved at most once
);

-- ---------------------------------------------------------------------
-- Sample data
-- ---------------------------------------------------------------------
INSERT INTO users (first_name, last_name, email, password, role, city, created_at) VALUES
('Amel',  'Karšić', 'amel@sofra.ba',  '$2y$12$oMwf3tS6FwDttltHHb8y0uCVY3sRc3Enxdrq.kzC2/NVagi1OPw7i', 'admin', 'Sarajevo', '2026-10-01 10:00:00'),
('Amra',  'Hadžić', 'amra@sofra.ba',  '$2y$12$t2.EfLAg7i9T1bW4mmcn0eEedxF/vErlXQX6SN7MPiNakeklUG7AS', 'user',  'Mostar',   '2026-10-03 12:30:00'),
('Emina', 'Softić', 'emina@sofra.ba', '$2y$12$t2.EfLAg7i9T1bW4mmcn0eEedxF/vErlXQX6SN7MPiNakeklUG7AS', 'user',  'Tuzla',    '2026-10-05 09:15:00'),
('Lejla', 'Mujić',  'lejla@sofra.ba', '$2y$12$t2.EfLAg7i9T1bW4mmcn0eEedxF/vErlXQX6SN7MPiNakeklUG7AS', 'user',  'Zenica',   '2026-10-09 18:45:00');

INSERT INTO categories (name, description) VALUES
('Pita',       'Rolled pastries: burek, zeljanica, krompiruša'),
('Meat',       'Grilled and baked meat dishes'),
('Stew',       'Slow-cooked one-pot dishes'),
('Dessert',    'Cakes and sweets'),
('Vegetarian', 'Dishes without meat');

INSERT INTO ingredients (name, unit, calories_per_100) VALUES
('Flour',          'g',    364),  -- 1
('Minced beef',    'g',    250),  -- 2
('Onion',          'pcs',   40),  -- 3
('Sunflower oil',  'ml',   884),  -- 4
('Spinach',        'g',     23),  -- 5
('Rice',           'g',    130),  -- 6
('Apples',         'pcs',   52),  -- 7
('Walnuts',        'g',    654),  -- 8
('Sugar',          'g',    387),  -- 9
('Potatoes',       'g',     77),  -- 10
('Cottage cheese', 'g',     98),  -- 11
('Eggs',           'pcs',  155),  -- 12
('Sour cream',     'g',    193),  -- 13
('Beef',           'g',    250),  -- 14
('Carrots',        'g',     41),  -- 15
('Cabbage leaves', 'pcs',   25),  -- 16
('Salt',           'tsp',    0),  -- 17
('Black pepper',   'tsp',  251);  -- 18

INSERT INTO recipes (user_id, category_id, title, description, instructions, prep_minutes, cook_minutes, servings, difficulty, image_url, created_at) VALUES
(1, 1, 'Burek',
 'Classic Bosnian meat pie rolled from thin dough.',
 '1. Mix flour, water and salt into a soft dough and let it rest 30 minutes.\n2. Fry the chopped onion, add the minced beef, salt and pepper.\n3. Stretch the dough thin, spread the filling and roll it up.\n4. Coil the rolls in an oiled pan and bake at 220 °C for 40 minutes.',
 60, 40, 6, 'hard', NULL, '2026-10-10 11:00:00'),
(2, 1, 'Zeljanica',
 'Pita filled with spinach, cottage cheese and eggs.',
 '1. Make the dough as for burek.\n2. Mix chopped spinach, cottage cheese, eggs and sour cream.\n3. Fill, roll and bake at 220 °C for 35 minutes.',
 50, 35, 6, 'medium', NULL, '2026-10-11 09:30:00'),
(1, 2, 'Ćevapi',
 'Grilled minced meat sausages served in somun with onion.',
 '1. Mix the minced beef with salt and pepper and leave it in the fridge overnight.\n2. Shape small sausages.\n3. Grill 8-10 minutes, turning once. Serve with chopped onion.',
 20, 10, 4, 'easy', NULL, '2026-10-12 19:00:00'),
(3, 3, 'Bosanski lonac',
 'Layered meat and vegetable stew cooked slowly in a clay pot.',
 '1. Cut the beef, potatoes, carrots and onion into large pieces.\n2. Layer everything in a pot, season and cover with water.\n3. Cook on low heat for 3 hours without stirring.',
 30, 180, 6, 'easy', NULL, '2026-10-13 14:20:00'),
(4, 2, 'Sarma',
 'Cabbage leaves stuffed with minced meat and rice.',
 '1. Mix minced beef, rice, chopped onion, salt and pepper.\n2. Wrap the filling in cabbage leaves.\n3. Stack the rolls in a pot, cover with water and cook 2 hours.',
 45, 120, 6, 'medium', NULL, '2026-10-15 10:10:00'),
(2, 4, 'Tufahije',
 'Poached apples stuffed with walnuts, served with whipped cream.',
 '1. Peel and core the apples.\n2. Poach them in sugar syrup for 10 minutes.\n3. Fill with ground walnuts mixed with sugar and let cool in the syrup.',
 20, 15, 4, 'easy', NULL, '2026-10-16 16:00:00'),
(3, 5, 'Krompiruša',
 'Pita filled with potatoes and onion.',
 '1. Make the dough as for burek.\n2. Mix grated potatoes, chopped onion, salt and pepper.\n3. Fill, roll and bake at 220 °C for 40 minutes.',
 50, 40, 6, 'medium', NULL, '2026-10-18 12:00:00'),
(4, 4, 'Hurmašice',
 'Syrup-soaked walnut cookies.',
 '1. Make a dough from flour, eggs, oil and sugar.\n2. Shape oval cookies and press them on a grater.\n3. Bake at 180 °C for 25 minutes and pour cold syrup over the hot cookies.',
 30, 25, 8, 'medium', NULL, '2026-10-20 17:30:00');

INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity, note) VALUES
-- Burek
(1, 1, 500, NULL), (1, 2, 600, NULL), (1, 3, 2, 'finely chopped'), (1, 4, 100, NULL), (1, 17, 1, NULL), (1, 18, 0.5, NULL),
-- Zeljanica
(2, 1, 500, NULL), (2, 5, 400, 'washed and chopped'), (2, 11, 300, NULL), (2, 12, 2, NULL), (2, 13, 200, NULL), (2, 4, 100, NULL),
-- Ćevapi
(3, 2, 1000, 'mix of beef and lamb is best'), (3, 3, 2, 'for serving'), (3, 17, 2, NULL), (3, 18, 1, NULL),
-- Bosanski lonac
(4, 14, 800, 'cut into cubes'), (4, 10, 600, NULL), (4, 15, 300, NULL), (4, 3, 3, NULL), (4, 17, 2, NULL),
-- Sarma
(5, 16, 20, 'sour cabbage'), (5, 2, 700, NULL), (5, 6, 150, NULL), (5, 3, 2, 'finely chopped'), (5, 18, 1, NULL),
-- Tufahije
(6, 7, 6, 'firm, sour apples'), (6, 8, 200, 'ground'), (6, 9, 400, NULL),
-- Krompiruša
(7, 1, 500, NULL), (7, 10, 1000, 'grated'), (7, 3, 2, NULL), (7, 4, 100, NULL), (7, 17, 1, NULL),
-- Hurmašice
(8, 1, 500, NULL), (8, 12, 3, NULL), (8, 4, 150, NULL), (8, 9, 600, '200 g for the dough, 400 g for the syrup'), (8, 8, 100, 'ground');

INSERT INTO reviews (user_id, recipe_id, rating, comment, created_at) VALUES
(2, 1, 5, 'Just like my grandmother makes it.',              '2026-10-14 13:00:00'),
(3, 1, 4, 'Great, but the dough takes practice.',             '2026-10-15 20:10:00'),
(4, 3, 5, 'Leaving the meat overnight really makes a difference.', '2026-10-16 18:00:00'),
(1, 4, 5, 'Perfect for a cold day.',                          '2026-10-17 12:40:00'),
(2, 5, 4, NULL,                                               '2026-10-18 19:25:00'),
(3, 6, 5, 'Easy and beautiful dessert.',                       '2026-10-19 21:00:00'),
(1, 7, 3, 'Needed more salt for my taste.',                    '2026-10-21 10:05:00'),
(4, 2, 4, 'Light and tasty.',                                  '2026-10-22 08:50:00');

INSERT INTO favorites (user_id, recipe_id, created_at) VALUES
(2, 1, '2026-10-14 13:05:00'),
(2, 3, '2026-10-15 09:00:00'),
(3, 6, '2026-10-19 21:05:00'),
(4, 1, '2026-10-20 11:00:00'),
(4, 4, '2026-10-20 11:02:00'),
(1, 6, '2026-10-21 15:30:00');
