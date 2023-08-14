SET FOREIGN_KEY_CHECKS=0;

alter table lpg_settings
drop column lpg_type;

alter table lpg_settings
    add name varchar(45) null after omc_customer_id;

alter table lube_settings
drop column lubes;

alter table lube_settings
    add name varchar(150) null after station_category;

alter table omc_sales_form_fields
    add field_action_source_column varchar(50) null after field_action_sources;

alter table omc_sales_form_primary_field_options
    add option_link_type varchar(50) null after option_name;

alter table omc_sales_form_primary_field_options
drop column product_type_id;

alter table omc_sales_form_primary_field_options
    add option_link_id int(11) unsigned null after option_link_type;

SET FOREIGN_KEY_CHECKS=1;


