# phpMyAdmin MySQL-Dump
# Database : `ccgv`

#
# Table structure for table `coupon_email_track`
#

CREATE TABLE coupon_email_track (
  unique_id int(11) NOT NULL auto_increment,
  coupon_id int(11) NOT NULL default '0',
  customer_id_sent int(11) NOT NULL default '0',
  sent_firstname varchar(32) default NULL,
  sent_lastname varchar(32) default NULL,
  emailed_to varchar(32) default NULL,
  date_sent datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (unique_id)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;


#
# Table structure for table `coupon_gv_customer`
#

CREATE TABLE coupon_gv_customer (
  customer_id int(5) NOT NULL default '0',
  amount decimal(8,4) NOT NULL default '0.0000',
  PRIMARY KEY  (customer_id),
  KEY customer_id (customer_id)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;


#
# Table structure for table `coupon_gv_queue`
#

CREATE TABLE coupon_gv_queue (
  unique_id int(5) NOT NULL auto_increment,
  customer_id int(5) NOT NULL default '0',
  order_id int(5) NOT NULL default '0',
  amount decimal(8,4) NOT NULL default '0.0000',
  date_created datetime NOT NULL default '0000-00-00 00:00:00',
  ipaddr varchar(32) NOT NULL default '',
  release_flag char(1) NOT NULL default 'N',
  PRIMARY KEY  (unique_id),
  KEY uid (unique_id,customer_id,order_id)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;


#
# Table structure for table `coupon_redeem_track`
#

CREATE TABLE coupon_redeem_track (
  unique_id int(11) NOT NULL auto_increment,
  coupon_id int(11) NOT NULL default '0',
  customer_id int(11) NOT NULL default '0',
  redeem_date datetime NOT NULL default '0000-00-00 00:00:00',
  redeem_ip varchar(32) NOT NULL default '',
  order_id int(11) NOT NULL default '0',
  PRIMARY KEY  (unique_id)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;


#
# Table structure for table `coupons`
#

CREATE TABLE coupons (
  coupon_id int(11) NOT NULL auto_increment,
  coupon_type char(1) NOT NULL default 'F',
  coupon_code varchar(32) NOT NULL default '',
  coupon_amount decimal(8,4) NOT NULL default '0.0000',
  coupon_minimum_order decimal(8,4) NOT NULL default '0.0000',
  coupon_start_date datetime NOT NULL default '0000-00-00 00:00:00',
  coupon_expire_date datetime NOT NULL default '0000-00-00 00:00:00',
  uses_per_coupon int(5) NOT NULL default '1',
  uses_per_user int(5) NOT NULL default '0',
  restrict_to_products varchar(255) default NULL,
  restrict_to_categories varchar(255) default NULL,
  restrict_to_customers text,
  coupon_active char(1) NOT NULL default 'Y',
  date_created datetime NOT NULL default '0000-00-00 00:00:00',
  date_modified datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (coupon_id)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;


#
# Table structure for table `coupons_description`
#

CREATE TABLE coupons_description (
  coupon_id int(11) NOT NULL default '0',
  language_id int(11) NOT NULL default '0',
  coupon_name varchar(32) NOT NULL default '',
  coupon_description text,
  KEY coupon_id (coupon_id)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Welcome Gift Voucher Amount', 'NEW_SIGNUP_GIFT_VOUCHER_AMOUNT', '0', 'Welcome Gift Voucher Amount: If you do not wish to send a Gift Voucher in your create account email put 0 for no amount else if you do place the amount here i.e. 10.00 or 50.00 no currency signs', 1, 31, NULL, '2003-12-05 05:01:41', NULL, NULL);
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Welcome Discount Coupon Code', 'NEW_SIGNUP_DISCOUNT_COUPON', '', 'Welcome Discount Coupon Code: if you do not want to send a coupon in your create account email leave blank else place the coupon code you wish to use', 1, 32, NULL, '2003-12-05 05:01:41', NULL, NULL);

INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Total', 'MODULE_ORDER_TOTAL_COUPON_STATUS', 'true', 'Do you want to display the Discount Coupon value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_COUPON_SORT_ORDER', '9', 'Sort order of display.', '6', '2', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Shipping', 'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING', 'true', 'Include Shipping in calculation', '6', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Tax', 'MODULE_ORDER_TOTAL_COUPON_INC_TAX', 'true', 'Include Tax in calculation.', '6', '6','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Re-calculate Tax', 'MODULE_ORDER_TOTAL_COUPON_CALC_TAX', 'None', 'Re-Calculate Tax', '6', '7','tep_cfg_select_option(array(\'None\', \'Standard\', \'Credit Note\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_ORDER_TOTAL_COUPON_TAX_CLASS', '0', 'Use the following tax class when treating Discount Coupon as Credit Note.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now());

INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Total', 'MODULE_ORDER_TOTAL_GV_STATUS', 'true', 'Do you want to display the Gift Voucher value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_GV_SORT_ORDER', '740', 'Sort order of display.', '6', '2', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Queue Purchases', 'MODULE_ORDER_TOTAL_GV_QUEUE', 'true', 'Do you want to queue purchases of the Gift Voucher?', '6', '3','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Shipping', 'MODULE_ORDER_TOTAL_GV_INC_SHIPPING', 'true', 'Include Shipping in calculation', '6', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Tax', 'MODULE_ORDER_TOTAL_GV_INC_TAX', 'true', 'Include Tax in calculation.', '6', '6','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Re-calculate Tax', 'MODULE_ORDER_TOTAL_GV_CALC_TAX', 'None', 'Re-Calculate Tax', '6', '7','tep_cfg_select_option(array(\'None\', \'Standard\', \'Credit Note\'), ', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_ORDER_TOTAL_GV_TAX_CLASS', '0', 'Use the following tax class when treating Gift Voucher as Credit Note.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now());
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Credit including Tax', 'MODULE_ORDER_TOTAL_GV_CREDIT_TAX', 'false', 'Add tax to purchased Gift Voucher when crediting to Account', '6', '8','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
