INSERT INTO `warpd`.`menus` (`type`, `url_type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `order`, `created`, `created_by`, `deleted`) VALUES ('omc', 'normal', 'Price Change', 'Price Change', 'E,D,PX', '0', 'System Setup', 'OmcAdmin', 'price_change', 'isw-grid', 'Price Change', '3', '2023-09-05 20:06:11', '0', 'n');
UPDATE `warpd`.`menus` SET `icon` = 'isw-list' WHERE (`id` = '174');
UPDATE `warpd`.`menus` SET `permission_controls` = 'A,E,D,PX' WHERE (`id` = '174');
UPDATE `warpd`.`menus` SET `menu_group` = 'Operations' WHERE (`id` = '174');
