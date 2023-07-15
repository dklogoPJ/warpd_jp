SET FOREIGN_KEY_CHECKS=0;

UPDATE warpd.menus t
SET t.type       = 'omc_customer',
    t.controller = 'OmcCustomer'
WHERE t.id = 130;

drop table if exists omc_customer_price_changes;
create table omc_customer_price_changes
(
    id              int auto_increment primary key not null,
    omc_customer_id int          null,
    description     varchar(255) null,
    product_type_id int          null,
    unit            varchar(255) null,
    price           varchar(100) null,
    deleted         char         default 'n',
    created         datetime     null,
    created_by      int          null,
    modified        datetime     null,
    modified_by     int          null
);

UPDATE warpd.menu_groups t
SET t.deleted = 'y'
WHERE t.id = 2391;


SET FOREIGN_KEY_CHECKS=1;