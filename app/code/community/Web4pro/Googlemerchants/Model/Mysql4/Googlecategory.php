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
class Web4pro_Googlemerchants_Model_Mysql4_Googlecategory extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('googlemerchants/googlemerchants_cats', 'category_id');
    }
}