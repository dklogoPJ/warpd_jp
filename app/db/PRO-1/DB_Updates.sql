SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `created_by`, `deleted`, created)
VALUES ('omc_customer', 'Temperature Compensation', 'Temperature Compensation', 'A,E,PX', '0', 'Ordering', 'OmcCustomerOrders', 'temperature_compensation', 'isw-grid', 'Temperature Compensation', '0', 'n', NOW());
INSERT INTO `menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `order`, `created_by`, `deleted`, created)
VALUES ('omc_customer', 'Pump Vs Tank Sales', 'Pump Vs Tank Sales', 'A,E,D,PX', '0', 'General', 'OmcCustomer', 'pump_tank_sales', 'isw-grid', 'Pump vs Tank Sales', '4', '0', 'n', NOW());
UPDATE `menus` SET `permission_controls` = 'A,E,D,PX' WHERE (`id` = '133');
INSERT INTO `menus` (`type`, `title`, `description`, `permission_controls`, `parent`, `menu_group`, `controller`, `action`, `icon`, `menu_name`, `order`, `created_by`, `deleted`, created)
VALUES ('omc_customer', 'RM Incentive Perf Monitoring', 'RM Incentive Perf Monitoring', 'A,E,D,PX', '0', 'General', 'OmcCustomer', 'incentive_monitoring', 'isw-grid', 'RM Incentive Perf Monitoring', '5', '0', 'n', NOW());

UPDATE `menus` SET `menu_name` = 'RM Incentive Perf Monitor' WHERE (`id` = '135');
UPDATE `menus` SET `parent` = '47', `icon` = 'icon-file', `order` = '7' WHERE (`id` = '133');


DROP PROCEDURE IF EXISTS add_new_tables;

DELIMITER $$

CREATE PROCEDURE add_new_tables()
BEGIN

    CREATE TABLE IF NOT EXISTS `temperature_compensations` (
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

    IF NOT EXISTS (SELECT * FROM information_schema.columns
       WHERE table_name='temperature_compensations'
        AND table_schema=DATABASE()
        AND column_name='omc_customer_id') THEN
        ALTER TABLE `temperature_compensations` ADD COLUMN `omc_customer_id` INT(10) NULL AFTER `id`;
    END IF;

    IF NOT EXISTS (SELECT * FROM information_schema.columns
       WHERE table_name='temperature_compensations'
         AND table_schema=DATABASE()
         AND column_name='deleted') THEN
        ALTER TABLE `temperature_compensations` ADD COLUMN `deleted` VARCHAR(45) NULL AFTER `modified_by`;
        ALTER TABLE `temperature_compensations` CHANGE COLUMN `deleted` `deleted` VARCHAR(45) NULL DEFAULT 'n';
    END IF;

    CREATE TABLE IF NOT EXISTS `pump_tank_sales` (
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

END $$
DELIMITER ;

CALL add_new_tables;

DROP PROCEDURE IF EXISTS add_new_tables;

SET FOREIGN_KEY_CHECKS=1;