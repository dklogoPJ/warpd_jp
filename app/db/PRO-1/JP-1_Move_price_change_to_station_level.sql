SET FOREIGN_KEY_CHECKS=0;

UPDATE menus t
SET t.type       = 'omc_customer',
    t.controller = 'OmcCustomer'
WHERE t.id = 130;

CREATE TABLE IF NOT EXISTS omc_customer_price_changes (
    id              INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    omc_customer_id INT          NULL,
    description     VARCHAR(255) NULL,
    product_type_id INT          NULL,
    unit            VARCHAR(255) NULL,
    price           VARCHAR(100) NULL,
    deleted         CHAR         DEFAULT 'n',
    created         DATETIME     NULL,
    created_by      INT          NULL,
    modified        DATETIME     NULL,
    modified_by     INT          NULL
);

UPDATE menu_groups t
SET t.deleted = 'y'
WHERE t.id = 2391;

SET FOREIGN_KEY_CHECKS=1;