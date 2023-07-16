CREATE TABLE `trucks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `truck_no` varchar(45) DEFAULT NULL,
  `capacity` varchar(45) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;


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
CREATE TABLE `warpd`.`pump_tank_sales` (
  `id` INT NOT NULL,
  `omc_customer_id` INT NULL,
  `tank` VARCHAR(45) NULL,
  `open_stock` VARCHAR(45) NULL,
  `received_quantity` VARCHAR(45) NULL,
  `volume_depot` VARCHAR(45) NULL,
  `pump_day_sales` VARCHAR(45) NULL,
  `closing_stock` VARCHAR(45) NULL,
  `tank_day_sales` VARCHAR(45) NULL,
  `variance` VARCHAR(45) NULL,
  `variance_cedis` VARCHAR(45) NULL,
  `comments` VARCHAR(150) NULL,
  `created` DATETIME NULL,
  `created_by` INT NULL,
  `modified` DATETIME NULL,
  `modified_by` INT NULL,
  `deleted` VARCHAR(2) NULL DEFAULT 'n',
  PRIMARY KEY (`id`));
