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
class Web4pro_Googlemerchants_Model_Googleshipping
{
    protected $_selectedMethod = NULL;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_getAvailableShippingMethods();
    }

    /**
     * @return mixed|null
     */
    protected function _getSelectedMethod()
    {
        if ($this->_selectedMethod === NULL) {
            $this->_selectedMethod = Mage::getStoreConfig('googlemerchants_options/general/google_shiping_options');
        }
        return $this->_selectedMethod;
    }

    /**
     * @return array
     */
    protected function _getAvailableShippingMethods()
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

        $options = array();
        foreach ($methods as $_ccode => $_carrier) {
            $_methodOptions = array();
            if ($_methods = $_carrier->getAllowedMethods()) {
                foreach ($_methods as $_mcode => $_method) {
                    $_code = $_mcode;
                    $_methodOptions[] = array('value' => $_code, 'label' => $_method);
                }

                if (!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                    $_title = $_ccode;

                $options[] = array('value' => $_methodOptions, 'label' => $_title);
            }
        }
        return $options;
    }

    /**
     * @param $product
     * @return string
     * method for calculating shipping cost
     */
    public function getShippingCost($product)
    {

        $defaultStoreId = Mage::helper('googlemerchants')->getSelectedStore();
        $item = Mage::getModel('sales/quote_item')->setProduct($product)->setQty(1);
        $store = Mage::getModel('core/store')->load($defaultStoreId);
        $methodCode = $this->_getSelectedMethod();
        $request = Mage::getModel('shipping/rate_request')
            ->setAllItems(array($item))
            ->setDestCountryId('*')
            ->setPackageValue($product->getFinalPrice())
            ->setPackageValueWithDiscount($product->getFinalPrice())
            ->setPackageWeight($product->getWeight())
            ->setPackageQty(1)
            ->setPackagePhysicalValue($product->getFinalPrice())
            ->setFreeMethodWeight(0)
            ->setStoreId($store->getId())
            ->setWebsiteId($store->getWebsiteId())
            ->setFreeShipping(0)
            ->setBaseCurrency($store->getBaseCurrency())
            ->setBaseSubtotalInclTax($product->getFinalPrice());

        $model = Mage::getModel('shipping/shipping')->collectRates($request);

        foreach ($model->getResult()->getAllRates() as $rate) {
            $carrier = $rate->getCarrier();
            if ($carrier == $methodCode) {
                return $rate->getPrice();
            }
        }
        return '';
    }


}