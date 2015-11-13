<?php

/**
 *Customer Telephone field apart from address telephone field 
 * */
$this->addAttribute('customer', 'cus_tele', array(
    'type' => 'varchar',  
    'label' => 'Customer telephone',
    'input' => 'text',
    'position' => 119,
    'required' => false, //or true
    'is_system' => 0,
    'visible_on_front' => 1
   
));
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'cus_tele');
$attribute->setData('used_in_forms', array(
    'adminhtml_customer',
    'checkout_register',
    'customer_account_create',
    'customer_account_edit',
));
$attribute->setData('is_user_defined', 0);
$attribute->save();

//

/** @var Mage_Customer_Model_Resource_Setup $this */
//$this->addAttribute('customer','default_pickup', array(
//        'type' => 'int',
//        'label'  => 'Default PickUp Address',
//        'input'  => 'text',
//        'backend' => 'mycloset_membership/customer_attribute_backend_pickup',
//        'required'  => false,
//        'sort_order' => 82,
//        'visible' => false,
//    )
//);
/*
 * reference field in the customer registration 1st form
 * edited by neenu
 * 
 */
 $this->addAttribute('customer', 'cus_reference', array(
    'type' => 'varchar',  
    'label' => 'If you have a referral code, please enter it here',
    'input' => 'text',
    'position' => 600,
    'required' => false, //or true
    'is_system' => 0,
    'visible_on_front' => 1
   
));
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'cus_reference');
$attribute->setData('used_in_forms', array(
    'adminhtml_customer',
    'checkout_register',
    'customer_account_create',
    'customer_account_edit',
));
$attribute->setData('is_user_defined', 0);
$attribute->save();




/**
 *  Membership Type
 * */
$this->addAttribute('customer', 'mem_type', array(
    'type' => 'text',
    'label' => 'Membership type',
    'input' => 'select',
    'position' => 120,
    'required' => true, //or true
    'is_system' => 0,
    'visible_on_front' => 1,
    'source' => 'membership/entity_memberships'
));
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'mem_type');
$attribute->setData('used_in_forms', array(
    'adminhtml_customer',
    'checkout_register',
    'customer_account_create',
    'customer_account_edit',
));
$attribute->setData('is_user_defined', 0);
$attribute->save();


/**
 *  Cutomer Attribute in product information
 * */

//
//
//$data = array(
//    'type' => 'text',
//    'label' => 'Customer Name',
//    'input' => 'select',
//
//    'sort_order' => 001, // YOU MIGHT NEED TO CHANGE THIS VALUE TO PUT THE ATTRIBUTE IN THE FIRST POSITION
//    
//    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//    'required' => '0',
//    'comparable' => '0',
//    'searchable' => '0',
//    'is_configurable' => '1',
//    'user_defined' => '1',
//    'visible_on_front' => 0, //want to show on frontend?
//    'visible_in_advanced_search' => 0,
//    'is_html_allowed_on_front' => 0,
//    'required' => 1,
//    'unique' => false,
//    'apply_to' => 'simple', //simple,configurable,bundled,grouped,virtual,downloadable
//    'is_configurable' => false,
//    'source' => 'membership/entity_userdetails'
//);
//
//$this->addAttribute('catalog_product', 'customer_name', $data);
//
//$this->addAttributeToSet(
//        'catalog_product', 'Default', 'General', 'customer_name'
//); //Default = attribute set, General = attribute group
//

/**
 *  Cutomer Attribute in product information
 * */



$data1 = array(
    'type' => 'int',
    'label' => 'Customer Id',
    'input' => 'select',

    'sort_order' => 002, // YOU MIGHT NEED TO CHANGE THIS VALUE TO PUT THE ATTRIBUTE IN THE FIRST POSITION
    
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required' => '0',
    'comparable' => '0',
    'searchable' => '0',
    'is_configurable' => '1',
    'user_defined' => '1',
    'visible_on_front' => 0, //want to show on frontend?
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'required' => 1,
    'unique' => false,
    'apply_to' => 'simple', //simple,configurable,bundled,grouped,virtual,downloadable
    'is_configurable' => false,
    'source' => 'membership/entity_userdetails'
);

$this->addAttribute('catalog_product', 'customer_id', $data1);

$this->addAttributeToSet(
        'catalog_product', 'Default', 'General', 'customer_id'
); //Default = attribute set, General = attribute group

/**
 * Edited by neenu
 * for new pickup details table
 */

$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `closet_pickup_details` (
`pickup_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`pickup_user_id` INT( 10 ) NOT NULL ,
`pickup_category_id` INT( 10 ) NOT NULL ,
`pickup_comment` LONGTEXT NOT NULL ,
`pickup_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
INDEX ( `pickup_user_id` , `pickup_category_id` )
) ENGINE = innodb;
");


