-- Tree Cats Page ID plugin - installation SQL
-- version 1.2.0
-- Date November 22, 2026

CREATE TABLE IF NOT EXISTS `cot_treecatspage_id_trees` (
  `tree_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tree_title` VARCHAR(255) NOT NULL DEFAULT '',
  `tree_description` TEXT DEFAULT NULL,
  `tree_count_pages` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `tree_created` INT UNSIGNED NOT NULL DEFAULT 0,
  `tree_updated` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`tree_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Whitelist categories with custom hierarchy (parent_id)
CREATE TABLE IF NOT EXISTS `cot_treecatspage_id_cats` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tree_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `cat_code` VARCHAR(255) NOT NULL DEFAULT '',
  `parent_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `cat_sort` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_tree_id` (`tree_id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_cat_code` (`cat_code`),
  UNIQUE KEY `uniq_tree_cat` (`tree_id`, `cat_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cot_treecatspage_id_i18n` (
  `tree_id` INT UNSIGNED NOT NULL,
  `field_name` VARCHAR(64) NOT NULL,
  `lang` CHAR(2) NOT NULL DEFAULT 'en',
  `value` TEXT,
  PRIMARY KEY (`tree_id`, `field_name`, `lang`),
  CONSTRAINT `fk_treecatspage_id_i18n_tree` FOREIGN KEY (`tree_id`) REFERENCES `cot_treecatspage_id_trees` (`tree_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
