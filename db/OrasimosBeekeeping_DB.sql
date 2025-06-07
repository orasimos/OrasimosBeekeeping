-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema OrasimosBeekeeping_DB
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema OrasimosBeekeeping_DB
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `OrasimosBeekeeping_DB` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `OrasimosBeekeeping_DB`;

-- -----------------------------------------------------
-- Table `OrasimosBeekeeping_DB`.`Users`
-- -----------------------------------------------------
CREATE TABLE `Users` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(45) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(45) NOT NULL,
  `LoginStatus` bit(1) NOT NULL DEFAULT b'0',
  `AuthToken` varchar(200) DEFAULT NULL,
  `FailedAuthAttempts` int NOT NULL DEFAULT 0,
  `OAuthProvider` enum('google','facebook','twitter','linkedin') NULL,
  `OAuthUid` varchar(50) NULL,
  `Firstname` varchar(100) NOT NULL,
  `Lastname` varchar(100) NOT NULL,
  `Locale` varchar(5) NOT NULL DEFAULT 'el-GR',
  `Inserted` bit(1) NOT NULL DEFAULT b'0',
  `Modified` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`Id`,`Username`,`Email`),
  UNIQUE KEY `Id_UNIQUE` (`Id`),
  UNIQUE KEY `Username_UNIQUE` (`Username`),
  UNIQUE KEY `Email_UNIQUE` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- -----------------------------------------------------
-- Table `OrasimosBeekeeping_DB`.`ProductTypes`
-- -----------------------------------------------------
CREATE TABLE `ProductTypes` (
  `Id` int NOT NULL,
  `Description` varchar(45) NOT NULL,
  `DescriptionEng` varchar(45) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Id_UNIQUE` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- -----------------------------------------------------
-- Table `OrasimosBeekeeping_DB`.`Products`
-- -----------------------------------------------------
CREATE TABLE `Products` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `NameEng` varchar(100) NOT NULL,
  `Type` int(11) DEFAULT NULL,
  `Description` varchar(1000) DEFAULT NULL,
  `DescriptionEng` varchar(1000) DEFAULT NULL,
  `NutritionalValue` varchar(1000) DEFAULT NULL,
  `NutritionalValueEng` varchar(1000) DEFAULT NULL,
  `ImageName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Products_ProductTypes_idx` (`Type`),
  CONSTRAINT `FK_Products_ProductTypes` FOREIGN KEY (`Type`) REFERENCES `ProductTypes` (`Id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- -----------------------------------------------------
-- Table `OrasimosBeekeeping_DB`.`ProductPrices`
-- -----------------------------------------------------
CREATE TABLE `ProductPrices` (
  `Id` int NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Inserted` datetime NOT NULL DEFAULT current_timestamp(),
  `ProductId` int DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Id_UNIQUE` (`Id`),
  KEY `FK_ProductPrices_Products_idx` (`ProductId`),
  CONSTRAINT `FK_ProductPrices_Products` FOREIGN KEY (`ProductId`) REFERENCES `Products` (`Id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- -----------------------------------------------------
-- Table `OrasimosBeekeeping_DB`.`Carts`
-- -----------------------------------------------------
CREATE TABLE `Carts` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `UserId` INT(11) NOT NULL,
  `ProductId` INT(11) NOT NULL,
  `Quantity` DECIMAL(10,2) NOT NULL,
  `Inserted` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `Completed` BIT(1) NOT NULL DEFAULT b'0',
  `CompletedOn` DATETIME NULL,
  `Price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Carts_Users_idx` (`UserId`),
  KEY `FK_Carts_Products_idx` (`ProductId`),
  CONSTRAINT `FK_Carts_Users` FOREIGN KEY (`UserId`) REFERENCES `Users`(`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_Carts_Products` FOREIGN KEY (`ProductId`) REFERENCES `Products`(`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*CREATE USER 'orasimos' IDENTIFIED BY 'Orasimos123!';*/
GRANT SELECT ON TABLE `OrasimosBeekeeping_DB`.* TO 'orasimos';
GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON TABLE `OrasimosBeekeeping_DB`.* TO 'orasimos';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
