<?php
/**
 * WEB4PRO - Creating profitable online stores
 *
 *
 * @author    WEB4PRO <achernyshev@web4pro.com.ua>
 * @category  WEB4PRO
 * @package   Web4pro_Googlemerchants
 * @copyright Copyright (c) 2014 WEB4PRO (http://www.web4pro.net)
 * @license   http://www.web4pro.net/license.txt
 */
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$attributeSet = $installer->addAttributeSet(Mage_Catalog_Model_Product::ENTITY, 'Google Attribute Set');


$installer->addAttributeGroup('catalog_product', 'Google Attribute Set', 'Google Attributes', 1000);


$attrSetModel = Mage::getModel('eav/entity_attribute_set');


$installer->addAttribute('catalog_product', 'condition_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'position' => 0,
    'frontend' => '',
    'label' => 'Condition',
    'input' => 'select',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
    'option' => array(
        'values' => array(
            0 => 'new',
            1 => 'used',
            2 => 'refurbished',
        )
    ),
));


$installer->addAttribute('catalog_product', 'brand_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Brand',
    'input' => 'text',
    'class' => '',
    'position' => 1,
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false
));

$installer->addAttribute('catalog_product', 'gtin_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'GTIN',
    'position' => 2,
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false
));


$installer->addAttribute('catalog_product', 'mpn_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'MPN',
    'position' => 3,
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false
));


$installer->addAttribute('catalog_product', 'mpn_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'MPN',
    'input' => 'text',
    'position' => 4,
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false

));


$installer->addAttribute('catalog_product', 'identifier_exists', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Identifier Exists',
    'input' => 'boolean',
    'class' => '',
    'source' => '',
    'position' => 5,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
));

$installer->addAttribute('catalog_product', 'material_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Material',
    'position' => 6,
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false
));


$installer->addAttribute('catalog_product', 'gender_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Gender',
    'input' => 'select',
    'class' => '',
    'position' => 7,
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
    'option' => array(
        'values' => array(
            0 => 'male',
            1 => 'female',
            2 => 'unisex',
        )
    ),
));

$installer->addAttribute('catalog_product', 'age_group_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Age group',
    'position' => 7,
    'input' => 'select',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
    'option' => array(
        'values' => array(
            0 => 'newborn',
            1 => 'infant',
            2 => 'toddler',
            3 => 'kids',
            4 => 'adult',
        )
    ),
));


$installer->addAttribute('catalog_product', 'color_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Color',
    'input' => 'text',
    'class' => '',
    'position' => 8,
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false
));


$installer->addAttribute('catalog_product', 'size_type_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Size Type',
    'position' => 10,
    'input' => 'select',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
    'option' => array(
        'values' => array(
            0 => 'regular',
            1 => 'petite',
            2 => 'plus',
            3 => 'big and tall',
            4 => 'maternity',
        )
    ),
));


$installer->addAttribute('catalog_product', 'size_system_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Size System',
    'position' => 10,
    'input' => 'select',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
    'option' => array(
        'values' => array(
            0 => 'US',
            1 => 'UK',
            2 => 'EU',
            3 => 'DE',
            4 => 'FR',
            5 => 'JP',
            6 => 'CN (China)',
            7 => 'IT',
            8 => 'BR',
            9 => 'MEX',
            10 => 'AU',
        )
    ),
));

$installer->addAttribute('catalog_product', 'size_system_google', array(
    'group' => 'Google Attributes',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Size System',
    'position' => 10,
    'input' => 'select',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
    'option' => array(
        'values' => array(
            0 => 'US',
            1 => 'UK',
            2 => 'EU',
            3 => 'DE',
            4 => 'FR',
            5 => 'JP',
            6 => 'CN (China)',
            7 => 'IT',
            8 => 'BR',
            9 => 'MEX',
            10 => 'AU',
        )
    ),
));

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('googlemerchants_cats')};
CREATE TABLE {$this->getTable('googlemerchants_cats')} (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `parent_id` smallint(6) NULL,
  `store_cat_id` smallint(6) NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->endSetup();
