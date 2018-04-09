-- MySQL Script generated by MySQL Workbench
-- Thu Mar 15 00:50:31 2018
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema comp1640
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema comp1640
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `comp1640` DEFAULT CHARACTER SET utf8 ;
USE `comp1640` ;

-- -----------------------------------------------------
-- Table `comp1640`.`accounts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comp1640`.`accounts` (
  `acc_id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `acc_type` TINYINT(1) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`acc_id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC))
ENGINE = InnoDB;
-- -----------------------------------------------------
-- Insert Sample Data Table `comp1640`.`accounts`
-- -----------------------------------------------------
INSERT INTO `comp1640`.`accounts` VALUES (NULL, "qa_manager", "123456", 2, "huybq.1992@gmail.com");
INSERT INTO `comp1640`.`accounts` VALUES (NULL, "qa_coordinator", "123456", 1, "huybq.1992@gmail.com");
INSERT INTO `comp1640`.`accounts` VALUES (NULL, "user1", "123456", 0, "huybq.1992@gmail.com");
INSERT INTO `comp1640`.`accounts` VALUES (NULL, "user2", "123456", 0, "huybq.1992@gmail.com");
INSERT INTO `comp1640`.`accounts` VALUES (NULL, "staff1", "123456", 3, "huybq.1992@gmail.com");
INSERT INTO `comp1640`.`accounts` VALUES (NULL, "staff2", "123456", 3, "huybq.1992@gmail.com");


-- -----------------------------------------------------
-- Table `comp1640`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comp1640`.`categories` (
  `cate_id` INT NOT NULL AUTO_INCREMENT,
  `cate_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`cate_id`),
  UNIQUE INDEX `cate_name_UNIQUE` (`cate_name` ASC))
ENGINE = InnoDB;
-- -----------------------------------------------------
-- Insert Sample Data Table `comp1640`.`categories`
-- -----------------------------------------------------
INSERT INTO `comp1640`.`categories` VALUES (NULL, "General");
INSERT INTO `comp1640`.`categories` VALUES (NULL, "Information Technology");
INSERT INTO `comp1640`.`categories` VALUES (NULL, "Accounting & Finance");
INSERT INTO `comp1640`.`categories` VALUES (NULL, "Hospitality & Tourism");
INSERT INTO `comp1640`.`categories` VALUES (NULL, "Psychology");
INSERT INTO `comp1640`.`categories` VALUES (NULL, "Business");

-- -----------------------------------------------------
-- Table `comp1640`.`settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comp1640`.`settings` (
  `set_id` INT NOT NULL AUTO_INCREMENT,
  `set_name` VARCHAR(100) NOT NULL,
  `set_value` TIMESTAMP NOT NULL,
  PRIMARY KEY (`set_id`),
  UNIQUE INDEX `set_name_UNIQUE` (`set_name` ASC))
ENGINE = InnoDB;
-- -----------------------------------------------------
-- Insert Sample Data Table `comp1640`.`settings`
-- -----------------------------------------------------
INSERT INTO `comp1640`.`settings` VALUES (1, "Closure Date", '2018-05-01 00:00:00');
INSERT INTO `comp1640`.`settings` VALUES (2, "Final Closure Date", '2018-05-02 00:00:00');


-- -----------------------------------------------------
-- Table `comp1640`.`ideas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comp1640`.`ideas` (
  `idea_id` INT NOT NULL AUTO_INCREMENT,
  `idea_title` VARCHAR(200) NOT NULL,
  `idea_content` TEXT NULL,
  `idea_time` TIMESTAMP NOT NULL,
  `idea_anony` TINYINT(1) ZEROFILL NOT NULL,
  `accounts_acc_id` INT NOT NULL,
  `categories_cate_id` INT NOT NULL,
  `idea_filename` TEXT NULL,
  PRIMARY KEY (`idea_id`, `accounts_acc_id`, `categories_cate_id`),
  INDEX `fk_ideas_accounts_idx` (`accounts_acc_id` ASC),
  INDEX `fk_ideas_categories1_idx` (`categories_cate_id` ASC),
  CONSTRAINT `fk_ideas_accounts`
    FOREIGN KEY (`accounts_acc_id`)
    REFERENCES `comp1640`.`accounts` (`acc_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ideas_categories1`
    FOREIGN KEY (`categories_cate_id`)
    REFERENCES `comp1640`.`categories` (`cate_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comp1640`.`thumbs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comp1640`.`thumbs` (
  `thumb_id` INT NOT NULL AUTO_INCREMENT,
  `thumb_type` TINYINT(1) ZEROFILL NOT NULL,
  `accounts_acc_id` INT NOT NULL,
  `ideas_idea_id` INT NOT NULL,
  `ideas_accounts_acc_id` INT NOT NULL,
  `ideas_categories_cate_id` INT NOT NULL,
  PRIMARY KEY (`thumb_id`, `accounts_acc_id`, `ideas_idea_id`, `ideas_accounts_acc_id`, `ideas_categories_cate_id`),
  INDEX `fk_thumbs_accounts1_idx` (`accounts_acc_id` ASC),
  INDEX `fk_thumbs_ideas1_idx` (`ideas_idea_id` ASC, `ideas_accounts_acc_id` ASC, `ideas_categories_cate_id` ASC),
  CONSTRAINT `fk_thumbs_accounts1`
    FOREIGN KEY (`accounts_acc_id`)
    REFERENCES `comp1640`.`accounts` (`acc_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_thumbs_ideas1`
    FOREIGN KEY (`ideas_idea_id` , `ideas_accounts_acc_id` , `ideas_categories_cate_id`)
    REFERENCES `comp1640`.`ideas` (`idea_id` , `accounts_acc_id` , `categories_cate_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comp1640`.`comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comp1640`.`comments` (
  `cm_id` INT NOT NULL AUTO_INCREMENT,
  `cm_content` TEXT NOT NULL,
  `ideas_idea_id` INT NOT NULL,
  `ideas_accounts_acc_id` INT NOT NULL,
  `ideas_categories_cate_id` INT NOT NULL,
  `cm_time` TIMESTAMP NOT NULL,
  `accounts_acc_id` INT NOT NULL,
  PRIMARY KEY (`cm_id`, `ideas_idea_id`, `ideas_accounts_acc_id`, `ideas_categories_cate_id`),
  INDEX `fk_comments_ideas1_idx` (`ideas_idea_id` ASC, `ideas_accounts_acc_id` ASC, `ideas_categories_cate_id` ASC),
  CONSTRAINT `fk_comments_ideas1`
    FOREIGN KEY (`ideas_idea_id` , `ideas_accounts_acc_id` , `ideas_categories_cate_id`)
    REFERENCES `comp1640`.`ideas` (`idea_id` , `accounts_acc_id` , `categories_cate_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;