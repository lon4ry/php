-- eshop;
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS eshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eshop;

CREATE TABLE catalog (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title   VARCHAR(255)      NOT NULL,
    author  VARCHAR(255)      NOT NULL,
    price   DECIMAL(10, 2)    NOT NULL DEFAULT 0,
    pubyear SMALLINT UNSIGNED NOT NULL
);

CREATE TABLE orders (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50)  NOT NULL UNIQUE,
    customer VARCHAR(255) NOT NULL,
    email    VARCHAR(255) NOT NULL,
    phone    VARCHAR(50)  NOT NULL,
    address  VARCHAR(255) NOT NULL,
    created  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ordered_items (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50)  NOT NULL,
    item_id  INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders (order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES catalog (id)
);

CREATE TABLE admins (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login    VARCHAR(50)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email    VARCHAR(255) NOT NULL,
    created  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DELIMITER //

CREATE PROCEDURE spAddItemToCatalog(
    IN p_title VARCHAR(255), IN p_author VARCHAR(255), IN p_price DECIMAL(10, 2), IN p_pubyear SMALLINT UNSIGNED)
BEGIN
    INSERT INTO catalog (title, author, price, pubyear) VALUES (p_title, p_author, p_price, p_pubyear);
END//

CREATE PROCEDURE spGetCatalog()
BEGIN
    SELECT id, title, author, price, pubyear FROM catalog ORDER BY id;
END//

-- p_ids: идентификаторы товаров из корзины через запятую, например '2,15'
CREATE PROCEDURE spGetItemsForBasket(IN p_ids VARCHAR(1000))
BEGIN
    SELECT id, title, author, price, pubyear FROM catalog WHERE FIND_IN_SET(id, p_ids) ORDER BY id;
END//

CREATE PROCEDURE spSaveOrder(
    IN p_order_id VARCHAR(50), IN p_customer VARCHAR(255), IN p_email VARCHAR(255),
    IN p_phone VARCHAR(50), IN p_address VARCHAR(255))
BEGIN
    INSERT INTO orders (order_id, customer, email, phone, address)
    VALUES (p_order_id, p_customer, p_email, p_phone, p_address);
END//

CREATE PROCEDURE spSaveOrderedItems(IN p_order_id VARCHAR(50), IN p_item_id INT UNSIGNED, IN p_quantity INT UNSIGNED)
BEGIN
    INSERT INTO ordered_items (order_id, item_id, quantity) VALUES (p_order_id, p_item_id, p_quantity);
END//

CREATE PROCEDURE spGetOrders()
BEGIN
    SELECT order_id, customer, email, phone, address, created FROM orders ORDER BY created DESC, id DESC;
END//

CREATE PROCEDURE spGetOrderedItems(IN p_order_id VARCHAR(50))
BEGIN
    SELECT c.id, c.title, c.author, c.price, c.pubyear, oi.quantity
    FROM ordered_items oi
    JOIN catalog c ON c.id = oi.item_id
    WHERE oi.order_id = p_order_id
    ORDER BY oi.id;
END//

CREATE PROCEDURE spSaveAdmin(IN p_login VARCHAR(50), IN p_password VARCHAR(255), IN p_email VARCHAR(255))
BEGIN
    INSERT INTO admins (login, password, email) VALUES (p_login, p_password, p_email);
END//

CREATE PROCEDURE spGetAdmin(IN p_login VARCHAR(50))
BEGIN
    SELECT id, login, password, email, created FROM admins WHERE login = p_login;
END//

DELIMITER ;

-- Демо-данные. Администратор по умолчанию: логин admin, пароль admin (сменить после развёртывания)
CALL spSaveAdmin('admin', '$2y$10$fmwmg.VRimiWhQHm5qP24Ogt0FZDflUKvJksEKx2m4y8MjTN.inru', 'admin@example.com');
CALL spAddItemToCatalog('Мастер и Маргарита', 'Михаил Булгаков', 450.00, 2019);
CALL spAddItemToCatalog('Преступление и наказание', 'Фёдор Достоевский', 390.00, 2020);
CALL spAddItemToCatalog('Чистый код', 'Роберт Мартин', 1200.00, 2022);
CALL spAddItemToCatalog('PHP 8', 'Дмитрий Котеров', 2100.00, 2023);
