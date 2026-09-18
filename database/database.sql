CREATE DATABASE IF NOT EXISTS cardhub;

USE cardhub;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(150) NOT NULL,
    name_pt VARCHAR(150),
    game VARCHAR(100) NOT NULL,
    edition VARCHAR(100) NOT NULL,
    image VARCHAR(500),
    rarity VARCHAR(50)
);

INSERT INTO users (username, password)
VALUES (
    'CardUser',
    '$2y$12$FRn50zxfwqMG9eMpzK0gvO.OWfego3lpQmGU1tyFey/5SiTaJRym6'
) ON DUPLICATE KEY UPDATE
    password = VALUES(password);

INSERT INTO cards (
    name_en,
    name_pt,
    game,
    edition,
    image,
    rarity
)
VALUES (
    'Forest',
    'Floresta',
    'mtg',
    'hob',
    '/public/images/cards/forest.jpg',
    'C'
);