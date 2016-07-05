-- Replication tables
/**
api_partners
catalogue
catalogue_brands
catalogue_categories
catalogue_categories_items
catalogue_categories_join_shop
catalogue_prices
catalogue_storage
catalogue_tags
catalogue_tags_items
catalogue_tags_sizes
catalogue_tags_types
catalogue_view_then_buy
counters
shop_currency
shop_delivery
shop_discounts
shop_geo_regions
shop_payment
structure_banners
structure_docdata
structure_docdata_lang
structure_documents
 */

USE DATABASE v6_z95;

-- ADD Indexes
ALTER TABLE `api_partners` ADD INDEX `idx_price_site_id` (`price_id`,`site_id`);
ALTER TABLE `catalogue_storage` ADD INDEX `idx_date_size` (`size`,`last_update`);
ALTER TABLE `catalogue` ADD INDEX `idx_date_name` (`cat_title`,`last_update`);
ALTER TABLE `catalogue_tags_items` ADD INDEX `idx_date_tag` (`tag_id`,`last_update`);
ALTER TABLE `shop_delivery` ADD INDEX `idx_date` (`last_update`);

-- ADD Sizes, types table
CREATE TABLE `catalogue_tags_sizes` (
  `id` int(11) unsigned NOT NULL COMMENT 'Size id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Size name',
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_name` (`name`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `catalogue_tags_types` (
  `id` int(11) unsigned NOT NULL COMMENT 'Type id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Type name',
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_name` (`name`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add mass import sizes, types procedures

DELIMITER //
CREATE PROCEDURE TYPES(OUT total VARCHAR(255))
	BEGIN
	   SET @total = 0;

		DELETE FROM catalogue_tags_types;

    UPDATE catalogue SET `last_update` = NOW() WHERE cat_status = 1;

		SELECT CONCAT_WS(' ', 'Inserted types:', COUNT(*)) INTO @total FROM catalogue_tags_types;

		SELECT @total AS 'Result';
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE SIZES(OUT total VARCHAR(255))
	BEGIN
	   SET @total = 0;

		DELETE FROM catalogue_tags_sizes;

    UPDATE catalogue_storage SET `last_update` = NOW() WHERE count > 0;

		SELECT CONCAT_WS(' ', 'Inserted sizes:', COUNT(*)) INTO @total FROM catalogue_tags_sizes;

		SELECT @total AS 'Result';
END//
DELIMITER ;

CALL SIZES(@total);
CALL TYPES(@total);


### Triggers catalogue AFTER INSERT &  AFTER UPDATE

DELIMITER $$
CREATE TRIGGER insertType
    AFTER INSERT ON catalogue
    FOR EACH ROW
      BEGIN
         DECLARE increment INTEGER;

         IF TRIM(NEW.cat_title) !='' THEN

         SET @increment :=  (SELECT IFNULL(MAX(id), 0) + 1 FROM catalogue_tags_types);

			INSERT INTO catalogue_tags_types (`id`,`name`) VALUES (@increment, LOWER(TRIM(NEW.cat_title)))
				ON DUPLICATE KEY UPDATE `name` = (LOWER(TRIM(NEW.cat_title)));

         END IF;
    END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER updateType
    AFTER UPDATE ON catalogue
    FOR EACH ROW
      BEGIN
         DECLARE increment INTEGER;

         IF TRIM(NEW.cat_title) !='' THEN


         SET @increment :=  (SELECT IFNULL(MAX(id), 0) + 1 FROM catalogue_tags_types);

			INSERT INTO catalogue_tags_types (`id`,`name`) VALUES (@increment, LOWER(TRIM(NEW.cat_title)))
				ON DUPLICATE KEY UPDATE `name` = (LOWER(TRIM(NEW.cat_title)));

         END IF;
    END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER insertSizes
    AFTER INSERT ON catalogue_storage
    FOR EACH ROW
      BEGIN

        DECLARE increment INTEGER;

         IF TRIM(NEW.size) !='' THEN

         SET @increment :=  (SELECT IFNULL(MAX(id), 0) + 1 FROM catalogue_tags_sizes);

			INSERT INTO catalogue_tags_sizes (`id`,`name`) VALUES (@increment, TRIM(NEW.size))
				ON DUPLICATE KEY UPDATE `name` = (TRIM(NEW.size));

         END IF;
    END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER updateSizes
    AFTER UPDATE ON catalogue_storage
    FOR EACH ROW
      BEGIN

        DECLARE increment INTEGER;

         IF TRIM(NEW.size) !='' THEN

         SET @increment :=  (SELECT IFNULL(MAX(id), 0) + 1 FROM catalogue_tags_sizes);

			INSERT INTO catalogue_tags_sizes (`id`,`name`) VALUES (@increment, TRIM(NEW.size))
				ON DUPLICATE KEY UPDATE `name` = (TRIM(NEW.size));

         END IF;
    END$$
DELIMITER;

CREATE TABLE `syncronize` (
  `command` varchar(255) NOT NULL DEFAULT '' COMMENT 'Executed command',
  `last_sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last execution time',
  `data_receive` varchar(255) DEFAULT '0.00' COMMENT 'Data transfered',
  `elapsed` decimal(10,3) DEFAULT NULL COMMENT 'Elapsed time',
  PRIMARY KEY (`command`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- FRONTEND

CREATE TABLE `products_relationship` (
  `product_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Product ID',
  `category_id` smallint(5) unsigned DEFAULT NULL COMMENT 'Product category rel',
  `tag_id` int(11) unsigned DEFAULT NULL COMMENT 'Product tag rel',
  `tag_meta_id` tinyint(1) unsigned DEFAULT NULL COMMENT 'Tag meta id',
  UNIQUE KEY `uni_all` (`product_id`,`category_id`,`tag_id`,`tag_meta_id`),
  UNIQUE KEY `uni_product_tags` (`product_id`,`tag_id`,`tag_meta_id`),
  UNIQUE KEY `uni_product_category` (`product_id`,`category_id`),
  KEY `category_id_idx` (`category_id`),
  KEY `tag_id_idx` (`tag_id`),
  KEY `fk_tag_rel` (`tag_id`,`tag_meta_id`),
  CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tag_rel` FOREIGN KEY (`tag_id`, `tag_meta_id`) REFERENCES `tags` (`id`, `tag_meta_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product Tags relationships';


