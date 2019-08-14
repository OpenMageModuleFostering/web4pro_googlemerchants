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
class Web4pro_Googlemerchants_Model_Googletax
{
    /**
     * @param $product
     * @return mixed
     * return tax for products for generating feed
     */
    public function getTaxValue($product)
    {
        $store = Mage::app()->getStore(Mage::helper('googlemerchants')->getSelectedStore());
        $taxCalculation = Mage::getModel('tax/calculation');
        $request = $taxCalculation->getRateRequest(null, null, null, $store);
        $taxClassId = $product->getTaxClassId();
        return $taxCalculation->getRate($request->setProductClassId($taxClassId));
    }
}