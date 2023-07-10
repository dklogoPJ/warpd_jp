UPDATE `warpd`.`menus` SET `title` = 'Customer Order Operations Input' WHERE (`id` = '54');
UPDATE `warpd`.`menus` SET `description` = 'Customer Order Operations Input' WHERE (`id` = '54');

UPDATE `warpd`.`menus` SET `menu_name` = 'Customer Order Operations Input' WHERE (`id` = '54');


CREATE TABLE `warpd`.`trucks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `truck_no` VARCHAR(45) NULL,
  `capacity` VARCHAR(45) NULL,
  `transporter` VARCHAR(100) NULL,
  `created` DATETIME NULL,
  `created_by` INT NULL,
  `modified` DATETIME NULL,
  `modified_by` INT NULL,
  PRIMARY KEY (`id`));

INSERT INTO `warpd`.`menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `order`, `created_by`) 
VALUES ('omc', 'Truck Management', 'Truck Management', 'A,E,D', '106', 'System Setup', 'OmcAdmin', 'truck', 'icon_file', 'Truck Management', '9', '0');

UPDATE `warpd`.`menus` SET `icon` = 'icon-file' WHERE (`id` = '128');

ALTER TABLE `warpd`.`omc_bdc_distributions` 
ADD COLUMN `unit_price` VARCHAR(20) NULL AFTER `driver`,
ADD COLUMN `total_amount` VARCHAR(20) NULL AFTER `unit_price`;


ALTER TABLE `warpd`.`trucks` 
CHANGE COLUMN `transporter` `name` VARCHAR(100) NULL DEFAULT NULL ;


UPDATE `warpd`.`menus` SET `title` = 'Customer Orders Approval', `menu_name` = 'Customer Orders Approval' WHERE (`id` = '33');
UPDATE `warpd`.`menus` SET `title` = 'Product Loading', `menu_name` = 'Product Loading' WHERE (`id` = '87');
UPDATE `warpd`.`menus` SET `title` = 'Products Invoicing', `menu_name` = 'Products Invoicing' WHERE (`id` = '30');
INSERT INTO `warpd`.`menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `created_by`, `deleted`) VALUES ('omc', 'Margin Analysis', 'Margin Analysis', 'PX', '39', 'Reporting', 'OmcReporting', 'margin_analysis', 'icon-file', 'Margin Analysis', '0', 'n');
INSERT INTO `warpd`.`menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `created_by`, `deleted`) VALUES ('omc', 'Price Change', 'Price Change', 'A,E,PX', '0', 'General', 'Omc', 'price_change', 'isw-grid', 'Price Change', '0', 'n');


CREATE TABLE `omc_margins` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `omc_id` int(11) DEFAULT NULL,
  `bdc_id` int(11) DEFAULT NULL,
  `product_type_id` int(11) DEFAULT '0',
  `omc_customer_id` int(11) DEFAULT NULL,
  `customer_segment` varchar(255) DEFAULT NULL,
  `pump_price` varchar(255) DEFAULT NULL,
  `bdc_ex_ref` varchar(255) DEFAULT NULL,
  `govt_tax` varchar(255) DEFAULT NULL,
  `vat` varchar(255) DEFAULT NULL,
  `uppf` varchar(255) DEFAULT NULL,
  `margin` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted` char(1) DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;


CREATE TABLE `omc_price_changes` (
  `id` int(11) NOT NULL,
  `omc_id` int(11) DEFAULT NULL,
  `product_type_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` varchar(100) DEFAULT '0.00',
  `unit` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted` char(1) DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;


UPDATE `warpd`.`menus` SET `order` = '3' WHERE (`id` = '103');
UPDATE `warpd`.`menus` SET `order` = '5' WHERE (`id` = '104');
INSERT INTO `warpd`.`menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `order`, `created_by`, `deleted`) VALUES ('omc_customer', 'Pre Order Delivery', 'Pre Order Delivery Entering', 'E,PX', '47', 'Ordering', 'OmcCustomerOrders', 'pre_orders_delivery', 'icon-file', 'Pre Order Delivery', '2', '0', 'n');
INSERT INTO `warpd`.`menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `order`, `created_by`, `deleted`) VALUES ('omc_customer', 'Post-Discharge ', 'Post-Discharge', 'E,PX', '47', 'Ordering', 'OmcCustomerOrders', 'post_discharge', 'icon-file', 'Post-Discharge', '4', '0', 'n');
UPDATE `warpd`.`menus` SET `title` = 'Pre-Discharge', `description` = 'Pre-Discharge Entering', `action` = 'pre_discharge', `menu_name` = 'Pre-Discharge' WHERE (`id` = '131');
