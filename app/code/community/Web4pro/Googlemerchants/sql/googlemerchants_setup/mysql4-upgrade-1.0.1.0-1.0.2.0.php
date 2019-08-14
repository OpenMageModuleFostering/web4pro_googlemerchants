<?php
/**
 * WEB4PRO - Creating profitable online stores
 *
 *
 * @author    WEB4PRO <achernyshev@web4pro.com.ua>
 * @category  WEB4PRO
 * @package   Web4pro_Googlemerchants
 * @copyright Copyright (c) 2015 WEB4PRO (http://www.web4pro.net)
 * @license   http://www.web4pro.net/license.txt
 */
$installer = $this;
$installer->startSetup();
$configTableName = $this->getTable('core/config_data');
$installer->run("UPDATE `{$configTableName}` SET `value`='' WHERE `path` = 'googlemerchants_options/general/google_feed_prameters'");
$installer->endSetup();
