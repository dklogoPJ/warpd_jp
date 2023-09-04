
/**
 * Author: Gideon Amissah
 * This procedure will help query the dynamic table
 */

/** ****************************************/
DROP PROCEDURE IF EXISTS generate_omc_customer_daily_sales_query;

DELIMITER //

CREATE PROCEDURE generate_omc_customer_daily_sales_query (
  IN param_form_id VARCHAR(100), -- The Form id to filter on
  IN param_omc_customer_id VARCHAR(100), -- The omc customer id to filter on
  IN param_record_dt VARCHAR(10), -- The sales sheet record date to filter on
  IN param_filter_columns VARCHAR(255), -- The selected column (omc_sales_form_fields, coma separated string ids)
  IN param_filter_primary_option VARCHAR(255), -- The selected rows (omc_sales_form_primary_field_options, coma separated string ids)
  IN param_include_primary_option_total CHAR(1), -- Passing a 'T' will include the omc_sales_form_primary_field_options with is_total='yes', Passing default '' will not include those records.
  IN param_use_column_name CHAR(1), -- Passing a 'T' will use the omc_sales_form_fields.field_names as column names. Passing default '' will use omc_sales_form_fields.id as column names
  OUT result_string TEXT
)
BEGIN

  DECLARE done BOOLEAN DEFAULT FALSE;
  DECLARE column_field_id INT(11);
  DECLARE column_field_name VARCHAR (255);
  DECLARE cur CURSOR FOR SELECT osff.id AS 'field_id', osff.field_name
    FROM omc_sales_forms osf
    LEFT JOIN omc_sales_form_fields osff ON osf.id = osff.omc_sales_form_id
    WHERE osf.id = param_form_id
    AND osff.deleted = 'n'
    ORDER BY osff.field_order;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  SET @form_columns := '';

  OPEN cur;

    read_loop: LOOP
      FETCH cur INTO column_field_id, column_field_name;
      IF done THEN
        LEAVE read_loop;
      END IF;

      SET @field_name := column_field_id;
      IF (param_use_column_name = 'T') THEN
          SET @field_name := LOWER(REPLACE(REPLACE(REPLACE(column_field_name, ' ', '_'), '(', ''), ')', ''));
      END IF;

      SET @a_column := CONCAT('MAX(CASE WHEN (omc_sales_form_field_id=''',column_field_id,''') THEN value ELSE NULL END) AS ''',@field_name,'''');

      -- If param_filter_columns is passed then select those columns else return all columns
      IF (param_filter_columns <> '') THEN
          IF (FIND_IN_SET(column_field_id,  param_filter_columns) > 0) THEN
              SET @form_columns := CONCAT_WS(',',@form_columns, @a_column);
          END IF;
      ELSE
          SET @form_columns := CONCAT_WS(',',@form_columns, @a_column);
      END IF;

    END LOOP;

  CLOSE cur;

  SET @query := CONCAT(
      'SELECT ocds.record_dt, osfpfo.option_name',
      @form_columns,
      ' FROM omc_customer_daily_sale_fields T1
           LEFT JOIN omc_customer_daily_sale_primary_fields ocdspf ON ocdspf.id = T1.omc_customer_daily_sale_primary_field_id
           LEFT JOIN omc_customer_daily_sales ocds ON ocdspf.omc_customer_daily_sale_id = ocds.id
           LEFT JOIN omc_sales_form_primary_field_options osfpfo ON ocdspf.omc_sales_form_primary_field_option_id = osfpfo.id',
      ' WHERE ocds.omc_sale_form_id = ',
      param_form_id,
      ' AND ocds.omc_customer_id = ',
      param_omc_customer_id,
      ' AND ocds.deleted = ''n'''
      );

  -- Addition condition filters here
  IF (param_record_dt <> '') THEN
      SET @query := CONCAT(@query, ' AND ocds.record_dt LIKE ''',param_record_dt,'%''');
  END IF;

  IF (param_include_primary_option_total = 'T') THEN
      SET @query := CONCAT(@query, ' AND osfpfo.is_total IN(''no'',''yes'')');
  ELSE
      SET @query := CONCAT(@query, ' AND osfpfo.is_total = ''no''');
  END IF;

  IF (param_filter_primary_option <> '') THEN
      SET @query := CONCAT(@query, ' AND osfpfo.id IN (',param_filter_primary_option,')');
  END IF;

  -- Last query statement below
  SET @query := CONCAT(@query, ' GROUP BY T1.omc_customer_daily_sale_primary_field_id');

  SET result_string = @query;

  SELECT result_string;
 /* PREPARE stmt FROM @query;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;*/

END //
DELIMITER ;
/*********************************************************************/

-- execute the stored procedure as an example
DROP PROCEDURE IF EXISTS example_execute_query;

DELIMITER //

CREATE PROCEDURE example_execute_query (
    param_form_id VARCHAR(100), -- The Form id to filter on
    param_omc_customer_id VARCHAR(100), -- The omc customer id to filter on
    param_record_dt VARCHAR(10), -- The sales sheet record date to filter on
    param_filter_columns VARCHAR(255), -- The selected column (omc_sales_form_fields, coma separated string ids)
    param_filter_primary_option VARCHAR(255) -- The selected rows (omc_sales_form_primary_field_options, coma separated string ids)
)
BEGIN

    CALL generate_omc_customer_daily_sales_query(
            param_form_id,
            param_omc_customer_id,
            param_record_dt,
            param_filter_columns,
            param_filter_primary_option,
            'T',
            'T',
            @query_string
        );

-- display the result
-- SELECT @val;

-- EXECUTE @query_string
    PREPARE stmt FROM @query_string;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

END //
DELIMITER ;

CALL example_execute_query(8, 1, '','','');

DROP PROCEDURE IF EXISTS example_execute_query;



DROP PROCEDURE IF EXISTS dsrp_get_query;

DELIMITER //

CREATE PROCEDURE dsrp_get_query (
    param_form_id VARCHAR(100), -- The Form id to filter on
    param_omc_customer_id VARCHAR(100), -- The omc customer id to filter on
    param_record_dt VARCHAR(10), -- The sales sheet record date to filter on
    param_filter_columns VARCHAR(255), -- The selected column (omc_sales_form_fields, coma separated string ids)
    param_filter_primary_option VARCHAR(255) -- The selected rows (omc_sales_form_primary_field_options, coma separated string ids)
)
BEGIN

    CALL generate_omc_customer_daily_sales_query(
            param_form_id,
            param_omc_customer_id,
            param_record_dt,
            param_filter_columns,
            param_filter_primary_option,
            'F',
            'F',
            @query_string
        );

END //
DELIMITER ;
