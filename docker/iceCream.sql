-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema iceCream
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema iceCream
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `iceCream` DEFAULT CHARACTER SET utf8mb4 ;
USE `iceCream` ;

-- -----------------------------------------------------
-- Table `iceCream`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(45) NOT NULL,
    `last_name` VARCHAR(45) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone_number` VARCHAR(45) NULL,
    `address` VARCHAR(255) NULL,
    `password` VARCHAR(255) NULL,
    `user_type` ENUM('owner', 'worker') NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`products` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
    `flavour` VARCHAR(45) NOT NULL,
    `description` TEXT NULL,
    `price` DECIMAL(7,2) NOT NULL DEFAULT 0.00,
    `stock` INT NOT NULL DEFAULT 0,
    `category_id` INT UNSIGNED NULL,
    `is_cake` TINYINT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `fk_products_categories_idx` (`category_id` ASC) VISIBLE,
    CONSTRAINT `fk_products_categories`
    FOREIGN KEY (`category_id`)
    REFERENCES `iceCream`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`tags` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`orders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`orders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_date` DATE NOT NULL,
    `pickup_date` DATE NOT NULL,
    `note` TEXT NULL,
    `name_client` VARCHAR(45) NOT NULL,
    `email_client` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`ice_truck_reservations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`ice_truck_reservations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `reservation_date` DATE NOT NULL,
    `pickup_date` DATE NOT NULL,
    `name_client` VARCHAR(255) NOT NULL,
    `email_client` VARCHAR(255) NOT NULL,
    `note` TEXT NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`opening_hours`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`opening_hours` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `day` VARCHAR(45) NOT NULL,
    `open_time` TIME NOT NULL,
    `close_time` TIME NOT NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`special_hours`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`special_hours` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `day` VARCHAR(45) NOT NULL,
    `open_time` TIME NOT NULL,
    `close_time` TIME NOT NULL,
    `description` VARCHAR(255) NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`popups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`popups` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `frequency_in_days` INT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_popups_users1_idx` (`user_id` ASC) VISIBLE,
    CONSTRAINT `fk_popups_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `iceCream`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`events`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`events` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
    `date` DATE NOT NULL,
    `url` VARCHAR(255) NULL,
    `is_closed` TINYINT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`product_tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`product_tag` (
    `product_id` INT UNSIGNED NOT NULL,
    `tag_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`product_id`, `tag_id`),
    INDEX `fk_products_has_tags_tags1_idx` (`tag_id` ASC) VISIBLE,
    INDEX `fk_products_has_tags_products1_idx` (`product_id` ASC) VISIBLE,
    CONSTRAINT `fk_products_has_tags_products1`
    FOREIGN KEY (`product_id`)
    REFERENCES `iceCream`.`products` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
    CONSTRAINT `fk_products_has_tags_tags1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `iceCream`.`tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iceCream`.`order_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `iceCream`.`order_product` (
    `order_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    PRIMARY KEY (`order_id`, `product_id`),
    INDEX `fk_orders_has_products_products1_idx` (`product_id` ASC) VISIBLE,
    INDEX `fk_orders_has_products_orders1_idx` (`order_id` ASC) VISIBLE,
    CONSTRAINT `fk_orders_has_products_orders1`
    FOREIGN KEY (`order_id`)
    REFERENCES `iceCream`.`orders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
    CONSTRAINT `fk_orders_has_products_products1`
    FOREIGN KEY (`product_id`)
    REFERENCES `iceCream`.`products` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
    ENGINE = InnoDB;


INSERT INTO `categories`(`name`) VALUES ('sorbet'), ('artisanal'), ('alcohol');

INSERT INTO `tags`(`name`) VALUES ('vegan'), ('gluten-free'), ('nuts');

INSERT INTO `products`(`name`, `flavour`, `price`, `stock`, `category_id`, `is_cake`) VALUES ('chocolade-ijs','chocolade','5.00','100','2','0');
INSERT INTO `products`(`name`, `flavour`, `price`, `stock`, `category_id`, `is_cake`) VALUES ('vanille-ijs','vanille','10.00','75','2','0');

INSERT INTO `products`(`name`, `flavour`, `description`, `price`, `stock`, `is_cake`) VALUES ('Choco-bismou','chocolade','chocolade taart met een chocomouse vulling','5.00','10','1');

INSERT INTO `events`(`name`, `date`, `is_closed`) VALUES ('Coco-party','2024-02-12','1');
INSERT INTO `events`(`name`, `date`, `is_closed`) VALUES ('Flanel-party','2024-07-15','1');

INSERT INTO `opening_hours`(`day`, `open_time`, `close_time`) VALUES
                                                                  ('Maandag','08:00','18:00'),
                                                                  ('dinsdag','08:00','18:00'),
                                                                  ('woensdag','08:00','18:00'),
                                                                  ('donderdag','08:00','18:00'),
                                                                  ('vrijdag','09:00','17:00'),
                                                                  ('zaterdag','09:00','17:00'),
                                                                  ('zondag','00:00','00:00');


INSERT INTO `users`(`first_name`, `last_name`, `email`, `phone_number`, `address`, `password`, `user_type`) VALUES ('Sven','Ijs','svenIjs@outlook.com','0475364789','Kazenstraat, 15 9000 Gent','$2y$10$FBpnESlUbmwP595twZLBouF2PIN7IkD4nsijl5wXYgtG9ih0J0Rza','owner');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
