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

CREATE USER 'orasimos' IDENTIFIED BY 'Orasimos123!';
GRANT SELECT ON TABLE `OrasimosBeekeeping_DB`.* TO 'orasimos';
GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON TABLE `OrasimosBeekeeping_DB`.* TO 'orasimos';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

/*Insert Product Types*/
INSERT INTO ProductTypes (Id, Description, DescriptionEng) VALUES
  (1, 'Μέλι', 'Honey'),
  (2, 'Υπερτροφές', 'Superfoods'),
  (3, 'Προσωπική Φροντίδα', 'Personal Care'),
  (4, 'Μελισσοκέρι', 'Beeswax');

/*Insert Products*/
INSERT INTO `Products`
  (`Id`, `Name`, `NameEng`, `ImageName`, `Type`,
   `Description`, `DescriptionEng`,
   `NutritionalValue`, `NutritionalValueEng`)
VALUES
  -- 1. Thyme Honey
  (
    1, 'Θυμαρίσιο Μέλι', 'Thyme Honey', 'thyme-honey.png', 1,
    'Παράγεται μόνο τους καλοκαιρινούς μήνες όταν ανθίζει το θυμάρι. Το χαρακτηριστικό του είναι η αρωματική γεύση και το χρυσαφένιο χρώμα. Στην Ελλάδα θεωρείται είδος πολυτελείας.',
    'Product only in the summer months when thyme blossoms. Its aromatic taste and golden color are its most prominent characteristics. In Greece, it is considered a luxury item.',
    'Υψηλή σε αντιοξειδωτικά και μέταλλα',
    'High in antioxidants and minerals'
  ),

  -- 2. Blossom Honey
  (
    2, 'Μέλι Ανθέων', 'Blossom Honey', 'blossom-honey.png', 1,
    'Το μέλι ανθέων παράγεται καθόλη τη διάρκεια του έτους. Κάθε ημέρα η γεύση του είναι μοναδική και απρόβλεπτη επειδή οι μέλισσες συλλέγουν γύρη από τα διαθέσιμα λουλούδια γύρω τους.',
    'Blossom honey is produced year-round. Every day its taste is unique and unpredictable because the bees collect pollen from the available flowers in their vicinity.',
    'Πλούσιο σε φυσικά σάκχαρα και ένζυμα',
    'Rich in natural sugars and enzymes'
  ),

  -- 3. Carob Honey
  (
    4, 'Χαρουπόμελο', 'Carob Honey', 'carob-honey.png', 1,
    'Κατανάλωση αυτού του μελιού φρέσκου δίνει μια πολύ έντονη γήινη γεύση που δεν αρέσει σε όλους. Αφήνοντάς το να ωριμάσει μετατρέπεται σε «καφέ χρυσό»!',
    'Eating this honey fresh gives off a very strong earthy flavor not for everyone. Leaving it to mature transforms it into brown gold!',
    'Υψηλό σε σίδηρο και μαγνήσιο',
    'High in iron and magnesium'
  ),

  -- 4. Sage Honey
  (
    3, 'Μέλι Φασκόμηλου', 'Sage Honey', 'sage-honey.png', 1,
    'Το μέλι φασκόμηλου έχει απαλή γεύση και ανοιχτό χρώμα. Είναι γνωστό για τις καταπραϋντικές ιδιότητές του και χρησιμοποιείται συχνά σε φυτικά σκευάσματα.',
    'Sage honey has a delicate flavor and light color. It is known for its soothing properties and is often used in herbal remedies.',
    'Περιέχει αντιφλεγμονώδεις ενώσεις',
    'Contains anti-inflammatory compounds'
  ),

  -- 5. Erica Honey
  (
    5, 'Μέλι Ερείκης', 'Erica Honey', 'erica-honey.png', 1,
    'Το μέλι ερείκης εκτιμάται για το σκούρο χρώμα του και την πλούσια, έντονη γεύση του. Συχνά αναζητείται για την υψηλή περιεκτικότητά του σε αντιοξειδωτικά.',
    'Erica honey is prized for its dark color and rich, robust flavor. It is often sought after for its high antioxidant content.',
    'Υψηλό σε αντιοξειδωτικά',
    'High in antioxidants'
  ),

  -- 6. Chewing Propolis
  (
    6, 'Μασώμενη Πρόπολη', 'Chewing Propolis', 'chewing-propolis.png', 2,
    'Η μασώμενη πρόπολη είναι γνωστή για τις φυσικές αντιβιοτικές και αντιφλεγμονώδεις ιδιότητές της. Υποστηρίζει την στοματική υγεία και ενισχύει το ανοσοποιητικό σύστημα.',
    'Chewing propolis is known for its natural antibiotic and anti-inflammatory properties. It supports oral health and boosts the immune system.',
    'Πλούσια σε βιοφλαβονοειδή',
    'Rich in bioflavonoids'
  ),

  -- 7. Royal Jelly
  (
    7, 'Βασιλικός Πολτός', 'Royal Jelly', 'royal-jelly.png', 2,
    'Ο βασιλικός πολτός είναι ένα θρεπτικό υποπροϊόν που παράγουν οι εργάτριες μέλισσες. Χρησιμοποιείται ως συμπλήρωμα διατροφής για την ενίσχυση της ζωτικότητας και της συνολικής υγείας.',
    'Royal jelly is a nutrient-rich substance produced by worker bees. It is used as a dietary supplement to promote vitality and overall health.',
    'Υψηλό σε βιταμίνες B1, B2, B6, B12',
    'High in vitamins B1, B2, B6, B12'
  ),

  -- 8. Edible Honeycomb
  (
    8, 'Βρώσιμη Κηρήθρα', 'Edible Honeycomb', 'edible-honeycomb.png', 2,
    'Η βρώσιμη κηρήθρα προσφέρει έναν μοναδικό και φυσικό τρόπο κατανάλωσης μελιού. Παρέχει μια γλυκιά, μασώμενη υφή και είναι ιδανική για τυρομεζέδες ή γλυκά.',
    'Edible honeycomb offers a unique and natural way to enjoy honey. It provides a sweet, chewy texture and is perfect for adding to cheese boards or desserts.',
    'Πλούσια σε βιταμίνες και ένζυμα',
    'Rich in vitamins and enzymes'
  ),

  -- 9. Fresh Pollen
  (
    9, 'Φρέσκια Γύρη', 'Fresh Pollen', 'fresh-pollen.png', 2,
    'Η φρέσκια γύρη είναι ένα πολύ θρεπτικό προϊόν μελισσών γεμάτο βιταμίνες, μέταλλα και πρωτεΐνες. Χρησιμοποιείται συχνά ως συμπλήρωμα διατροφής για ενέργεια και υποστήριξη του ανοσοποιητικού.',
    'Fresh pollen is a highly nutritious bee product packed with vitamins, minerals, and proteins. It is often used as a dietary supplement for energy and immune support.',
    'Υψηλή σε πρωτεΐνες, βιταμίνες και μέταλλα',
    'High in proteins, vitamins, and minerals'
  ),

  -- 10. Propolis Tincture
  (
    10, 'Βάμμα Πρόπολης', 'Propolis Tincture', 'propolis-tincture.png', 2,
    'Το εκχύλισμα πρόπολης σε υγρή μορφή είναι γνωστό για τις φαρμακευτικές του ιδιότητες. Παρέχει υποστήριξη στο ανοσοποιητικό, έχει αντιφλεγμονώδη και αντιβακτηριακή δράση.',
    'A liquid extract of propolis known for its medicinal properties. Provides immune support, and has anti-inflammatory and antibacterial properties.',
    'Πλούσια σε βιοφλαβονοειδή',
    'Rich in bioflavonoids'
  ),

  -- 11. Beeswax Candles
  (
    11, 'Κεριά από Μελισσοκέρι', 'Beeswax Candles', 'beeswax-candles.png', 4,
    'Φυσικά, χειροποίητα κεριά από μελισσοκέρι σε διάφορα σχήματα και μεγέθη. Διαρκούν πολύ, καίγονται καθαρά και έχουν φυσική μυρωδιά μελιού.',
    'Natural, handcrafted beeswax candles in various shapes and sizes. Long-lasting, clean-burning, and have a natural honey scent.',
    'Χωρίς τοξίνες, προάγει καθαρό αέρα',
    'Free from toxins, promotes clean air'
  ),

  -- 12. Beeswax Wraps
  (
    12, 'Κηρόπανα', 'Beeswax Wraps', 'beeswax-wraps.png', 4,
    'Φιλικές προς το περιβάλλον, επαναχρησιμοποιούμενες μεμβράνες φαγητού από μελισσοκέρι. Βιώσιμη εναλλακτική πλαστικής μεμβράνης που διατηρεί τα τρόφιμα φρέσκα.',
    'Eco-friendly, reusable food wraps made from beeswax. A sustainable alternative to plastic wrap that keeps food fresh.',
    'Αντιβακτηριακές, διαπνέουσες',
    'Antibacterial, breathable'
  ),

  -- 13. Honey Soap
  (
    13, 'Σαπούνι με Μέλι', 'Honey Soap', 'honey-soap.png', 3,
    'Χειροποίητο σαπούνι με μέλι και μελισσοκέρι. Ενυδατικό, αντιβακτηριακό και κατάλληλο για ευαίσθητο δέρμα.',
    'Handcrafted soap made with honey and beeswax. Moisturizing, antibacterial, and suitable for sensitive skin.',
    'Ενυδατικό, καταπραϋντικό',
    'Moisturizing, soothing'
  ),

  -- 14. Beeswax Lip Balm
  (
    14, 'Βάλσαμο Χειλιών με Μελισσοκέρι', 'Beeswax Lip Balm', 'beeswax-lip-balm.png', 3,
    'Φυσικό βάλσαμο χειλιών από μελισσοκέρι, μέλι και αιθέρια έλαια. Ενυδατικό, προστατευτικό και καταπραϋντικό.',
    'Natural lip balm made with beeswax, honey, and essential oils. Moisturizing, protective, and soothing for lips.',
    'Ενυδατικό, προστατευτικό',
    'Protective, hydrating'
  );


/*Insert Product Prices*/
INSERT INTO ProductPrices (Id, Price, Date, Inserted, ProductId)
VALUES
  -- Product 1: Thyme Honey (base 10.00)
  (  1, 10.00, '2025-01-01', '2025-01-01', 1),
  (  2, 10.20, '2025-02-01', '2025-02-01', 1),
  (  3, 10.10, '2025-03-01', '2025-03-01', 1),
  (  4, 10.50, '2025-04-01', '2025-04-01', 1),
  (  5, 10.30, '2025-05-01', '2025-05-01', 1),

  -- Product 2: Blossom Honey (base  8.00)
  (  6,  8.00, '2025-01-01', '2025-01-01', 2),
  (  7,  8.10, '2025-02-01', '2025-02-01', 2),
  (  8,  7.90, '2025-03-01', '2025-03-01', 2),
  (  9,  8.25, '2025-04-01', '2025-04-01', 2),
  ( 10,  8.05, '2025-05-01', '2025-05-01', 2),

  -- Product 3: Carob Honey (base 10.00)
  ( 11, 10.00, '2025-01-01', '2025-01-01', 3),
  ( 12,  9.80, '2025-02-01', '2025-02-01', 3),
  ( 13, 10.50, '2025-03-01', '2025-03-01', 3),
  ( 14, 10.20, '2025-04-01', '2025-04-01', 3),
  ( 15, 10.40, '2025-05-01', '2025-05-01', 3),

  -- Product 4: Sage Honey (base 12.00)
  ( 16, 12.00, '2025-01-01', '2025-01-01', 4),
  ( 17, 12.30, '2025-02-01', '2025-02-01', 4),
  ( 18, 12.10, '2025-03-01', '2025-03-01', 4),
  ( 19, 12.50, '2025-04-01', '2025-04-01', 4),
  ( 20, 12.20, '2025-05-01', '2025-05-01', 4),

  -- Product 5: Erica Honey (base 12.00)
  ( 21, 12.00, '2025-01-01', '2025-01-01', 5),
  ( 22, 11.80, '2025-02-01', '2025-02-01', 5),
  ( 23, 12.20, '2025-03-01', '2025-03-01', 5),
  ( 24, 12.40, '2025-04-01', '2025-04-01', 5),
  ( 25, 12.10, '2025-05-01', '2025-05-01', 5),

  -- Product 6: Chewing Propolis (base 10.00)
  ( 26, 10.00, '2025-01-01', '2025-01-01', 6),
  ( 27, 10.10, '2025-02-01', '2025-02-01', 6),
  ( 28, 10.05, '2025-03-01', '2025-03-01', 6),
  ( 29, 10.20, '2025-04-01', '2025-04-01', 6),
  ( 30, 10.15, '2025-05-01', '2025-05-01', 6),

  -- Product 7: Royal Jelly (base  8.00)
  ( 31,  8.00, '2025-01-01', '2025-01-01', 7),
  ( 32,  7.95, '2025-02-01', '2025-02-01', 7),
  ( 33,  8.10, '2025-03-01', '2025-03-01', 7),
  ( 34,  8.05, '2025-04-01', '2025-04-01', 7),
  ( 35,  8.00, '2025-05-01', '2025-05-01', 7),

  -- Product 8: Edible Honeycomb (base 10.00)
  ( 36, 10.00, '2025-01-01', '2025-01-01', 8),
  ( 37, 10.25, '2025-02-01', '2025-02-01', 8),
  ( 38, 10.00, '2025-03-01', '2025-03-01', 8),
  ( 39, 10.30, '2025-04-01', '2025-04-01', 8),
  ( 40, 10.10, '2025-05-01', '2025-05-01', 8),

  -- Product 9: Fresh Pollen (base 12.00)
  ( 41, 12.00, '2025-01-01', '2025-01-01', 9),
  ( 42, 12.10, '2025-02-01', '2025-02-01', 9),
  ( 43, 12.25, '2025-03-01', '2025-03-01', 9),
  ( 44, 12.15, '2025-04-01', '2025-04-01', 9),
  ( 45, 12.30, '2025-05-01', '2025-05-01', 9),

  -- Product 10: Beeswax Candles (base 15.00)
  ( 46, 15.00, '2025-01-01', '2025-01-01', 10),
  ( 47, 15.50, '2025-02-01', '2025-02-01', 10),
  ( 48, 15.20, '2025-03-01', '2025-03-01', 10),
  ( 49, 15.60, '2025-04-01', '2025-04-01', 10),
  ( 50, 15.30, '2025-05-01', '2025-05-01', 10),

  -- Product 11: Beeswax Wraps (base 12.00)
  ( 51, 12.00, '2025-01-01', '2025-01-01', 11),
  ( 52, 12.05, '2025-02-01', '2025-02-01', 11),
  ( 53, 12.10, '2025-03-01', '2025-03-01', 11),
  ( 54, 12.00, '2025-04-01', '2025-04-01', 11),
  ( 55, 12.15, '2025-05-01', '2025-05-01', 11),

  -- Product 12: Propolis Tincture (base 20.00)
  ( 56, 20.00, '2025-01-01', '2025-01-01', 12),
  ( 57, 20.50, '2025-02-01', '2025-02-01', 12),
  ( 58, 20.30, '2025-03-01', '2025-03-01', 12),
  ( 59, 20.40, '2025-04-01', '2025-04-01', 12),
  ( 60, 20.20, '2025-05-01', '2025-05-01', 12),

  -- Product 13: Honey Soap (base  8.00)
  ( 61,  8.00, '2025-01-01', '2025-01-01', 13),
  ( 62,  8.20, '2025-02-01', '2025-02-01', 13),
  ( 63,  8.10, '2025-03-01', '2025-03-01', 13),
  ( 64,  8.25, '2025-04-01', '2025-04-01', 13),
  ( 65,  8.15, '2025-05-01', '2025-05-01', 13),

  -- Product 14: Beeswax Lip Balm (base  5.00)
  ( 66,  5.00, '2025-01-01', '2025-01-01', 14),
  ( 67,  5.10, '2025-02-01', '2025-02-01', 14),
  ( 68,  5.05, '2025-03-01', '2025-03-01', 14),
  ( 69,  5.20, '2025-04-01', '2025-04-01', 14),
  ( 70,  5.15, '2025-05-01', '2025-05-01', 14);
