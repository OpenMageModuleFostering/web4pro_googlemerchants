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
class Web4pro_Googlemerchants_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var null
     */
    protected $_defaultStoreId = NULL;

    /**
     * @param Web4pro_Googlemerchants_Model_Googlecategory $entity
     * @return array
     */
    public function categoryEntityToTreeStd(Web4pro_Googlemerchants_Model_Googlecategory $entity)
    {
        $retObject = new stdClass();
        $categoryName = $entity->getTitle();
        $categoryId = $entity->getCategoryId();
        $childrenCount = Mage::getModel('googlemerchants/googlecategory')->getChildrenCount($categoryId);
        $class = 'folder active-category';

        $retArray = array('text' => $categoryName, 'id' => $categoryId, 'cls' => $class, 'allowDrop' => false, 'allowDrag' => false,
            'store' => 0, 'expanded' => false);

        if ($childrenCount > 0) {
            $retArray['children'] = array();
        }
        return $retArray;
    }

    /**
     * @return array
     */
    public function getRootCategoriesIds()
    {
        $rootCatIds = array();
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $rootCatIds [] = $store->getRootCategoryId();
                }
            }
        }
        return array_unique($rootCatIds);
    }

    /**
     * @return mixed
     */
    public function getGoogleCategoriesCount()
    {
        return Mage::getModel('googlemerchants/googlecategory')->getCollection()->count();
    }

    /**
     * @param $code
     * @return bool|Mage_Core_Model_Store
     */
    public function getStoreByCode($code)
    {

        $stores = array_keys(Mage::app()->getStores());
        foreach ($stores as $id) {
            $store = Mage::app()->getStore($id);
            if ($store->getCode() == $code) {
                return $store;
            }
        }
        return false;
    }

    /**
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getSelectedStore()
    {
        $storeCode = Mage::app()->getRequest()->getParam('store');
        if (empty($storeCode)) {
            $defaultStoreId = Mage::app()
                ->getWebsite(true)
                ->getDefaultGroup()
                ->getDefaultStoreId();
            $store = Mage::getModel('core/store')->load($defaultStoreId);
            $storeCode = $store->getCode();
        }
        return $storeCode;
    }

    /**
     * @param $product
     * @return string
     */
    public function getParentName($product)
    {
        $parentIds = array();
        if ($product->getTypeId() == "simple") {
            $parentIds = $this->getParentIds($product);
            if (isset($parentIds[0])) {
                $parentProduct = Mage::getModel('catalog/product')->load($parentIds[0]);
                return $parentProduct->getName();
            }
        }
        return '';
    }

    public function getParentIds($product)
    {
        $parentIds = array();
        if ($product->getTypeId() == "simple") {
            $parentIdsGrouped = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
            $parentIdsConfigurable = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
            $parentIdsBundle = Mage::getModel('bundle/product_type')->getParentIdsByChild($product->getId());
            $parentIds = array_merge($parentIdsGrouped, $parentIdsConfigurable, $parentIdsBundle);
        }
        return $parentIds;
    }

    public function getProductUrl($product)
    {
        $productVisibility = $product->getVisibility();
        $urlKey = $product->getProductUrl();
        if ($productVisibility != 4) {
            $parentProductId = $this->getParentIds($product);
            $parentCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('visibility', array('eq' => 4));
            $parentProduct = $parentCollection->getFirstItem();
            $urlKey = $parentProduct->getProductUrl();
        }
        return $urlKey;
    }

    public function getMinPrice($product)
    {
        $productType = $product->getTypeID();
        $priseStr = '';
        switch ($productType) {
            case 'configurable':
                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
                $minPrice = "";
                foreach($childProducts as $child){
                    $_child = Mage::getModel('catalog/product')->load($child->getId());

                    if($minPrice == '' || $minPrice > $_child->getPrice() )
                        $minPrice =  $_child->getPrice();

                }
                $priseStr = $minPrice;
                break;
            case 'grouped':
                $productIds = $product->getTypeInstance()->getChildrenIds($product->getId());
                $prices = array();
                foreach ($productIds as $ids) {
                    foreach ($ids as $id) {
                        $aProduct = Mage::getModel('catalog/product')->load($id);
                        $prices[] = (float) $aProduct->getPriceModel()->getPrice($aProduct);
                    }
                }
                sort($prices);
                if(is_array($prices)) {
                    $priseStr = (string) current($prices);
                }
                break;
            case 'bundle':
                $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
                    $product->getTypeInstance(true)->getOptionsIds($product), $product);
                foreach ($selectionCollection as $option) {
                    if ($option->getPrice() != "0.0000") {
                        $bundlePrices [] = $option->getPrice();
                    }
                }
                sort($bundlePrices);
                if(is_array($bundlePrices)) {
                    $priseStr = current($bundlePrices);
                }
                break;
            case 'downloadable':
                $priseStr = sprintf("%0.2f", $product->getFinalPrice());
                break;
            default:
                $priseStr = sprintf("%0.2f", $product->getFinalPrice());
        }
        return $priseStr;
    }
}