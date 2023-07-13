INSERT INTO `warpd`.`menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `created_by`, `deleted`) VALUES ('omc_customer', 'Temperature Compesation', 'Temperature Compesation', 'A,E,PX', '0', 'Ordering', 'OmcCustomerOrders', 'temperature_compesation', 'isw-grid', 'Temperature Compesation', '0', 'n');
INSERT INTO `warpd`.`menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `order`, `created_by`, `deleted`) VALUES ('Omc_customer', 'Pump Vs Tank Sales', 'Pump Vs Tank Sales', 'A,E,D,PX', '0', 'General', 'OmcCustomer', 'pump_tank_sales', 'isw-grid', 'Pump vs Tank Sales', '4', '0', 'n');
UPDATE `warpd`.`menus` SET `permission_controls` = 'A,E,D,PX' WHERE (`id` = '133');
INSERT INTO `warpd`.`menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `order`, `created_by`, `deleted`) VALUES ('omc_customer', 'RM Incentive Perf Monitoring', 'RM Incentive Perf Monitoring', 'A,E,D,PX', '0', 'General', 'OmcCustomer', 'incentive_monitoring', 'isw-grid', 'RM Incentive Perf Monitoring', '5', '0', 'n');
UPDATE `warpd`.`menus` SET `menu_name` = 'RM Incentive Perf Monitor' WHERE (`id` = '135');
UPDATE `warpd`.`menus` SET `parent` = '47', `icon` = 'icon-file', `order` = '7' WHERE (`id` = '133');
CREATE TABLE `warpd`.`temperature_compesations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `invoice_date` DATETIME NULL,
  `invoice_no` VARCHAR(45) NULL,
  `product_type_id` INT NULL,
  `volume_depot` VARCHAR(45) NULL,
  `dens_vac` VARCHAR(45) NULL,
  `temp_20` VARCHAR(45) NULL,
  `temp_depot` VARCHAR(45) NULL,
  `product_coeff` VARCHAR(45) NULL,
  `temp_coeff_1` VARCHAR(45) NULL,
  `temp_station` VARCHAR(45) NULL,
  `vol_15` VARCHAR(45) NULL,
  `temp_coeff_2` VARCHAR(45) NULL,
  `temp_vol_station` VARCHAR(45) NULL,
  `received_quantity` VARCHAR(45) NULL,
  `variance_depot` VARCHAR(45) NULL,
  `variance_received_qty` VARCHAR(45) NULL,
  `created` DATETIME NULL,
  `created_by` INT NULL,
  `modified` DATETIME NULL,
  `modified_by` INT NULL,
  PRIMARY KEY (`id`));

  ALTER TABLE `warpd`.`temperature_compesations` 
ADD COLUMN `omc_customer_id` INT NULL AFTER `id`;


ALTER TABLE `warpd`.`temperature_compesations` 
ADD COLUMN `deleted` VARCHAR(45) NULL AFTER `modified_by`;
ALTER TABLE `warpd`.`temperature_compesations` 
CHANGE COLUMN `deleted` `deleted` VARCHAR(45) NULL DEFAULT 'n' ;


