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
class Web4pro_Googlemerchants_Model_Googlefeed extends Mage_Core_Model_Abstract
{
    protected $_attributesArray = NULL, $_productCollection = NULL, $_helper = NULL, $_shouldGenerateFeedByCron = false, $_byCron = false,
        $_path = 'googlemerchants_options/general/google_feed_prameters',
        $_notAttachedCategoriesExists = false, $_attributesCollection,
        $_defaultParameters = array();

        protected $_defaultOptionValues = array(
                                    array('code' => 'product_id', 'label' => 'Product ID'),
                                    array('code' => 'google_category', 'label' => 'Google Category'),
                                    array('code' => 'shipping_cost', 'label' => 'Shipping Cost'),
                                    array('code' => 'parent_name', 'label' => 'Parent Name'),
                                    array('code' => 'google_tax', 'label' => 'Tax'),
                                    array('code' => 'google_product_link', 'label' => 'Product URL'),
                                    array('code' => 'google_availability', 'label' => 'Availability (in/out of stock)'));

    const PATH_TO_GENERATE = 'var/productsfeed';

    public function _construct()
    {
        parent::_construct();
        $this->_init('googlemerchants/googlefeed');
        $this->_helper = Mage::helper('googlemerchants');
        $this->_setProductsCollection();
        $this->_appendStoreFilter();
        $this->_shouldGenerateFeedByCron = Mage::getStoreConfig('googlemerchants_options/general/google_generate_feed_by_cron');
        $countryPref = $this->_getCountryPrefix();
        $this->_defaultParameters = array(
            array('value' => 'id', 'selected' => array('value' => 'product_id', 'pref' => '', 'postf' => '')),
            array('value' => 'title', 'selected' => array('value' => 'name', 'pref' => '', 'postf' => '')),
            array('value' => 'description', 'selected' => array('value' => 'description', 'pref' => '', 'postf' => '')),
            array('value' => 'price', 'selected' => array('value' => 'price', 'pref' => '', 'postf' => '')),
            array('value' => 'condition', 'selected' => array('value' => 'condition_google', 'pref' => '', 'postf' => '')),
            array('value' => 'image link', 'selected' => array('value' => 'image', 'pref' => '', 'postf' => '')),
            array('value' => 'gtin', 'selected' => array('value' => 'gtin_google', 'pref' => '', 'postf' => '')),
            array('value' => 'color', 'selected' => array('value' => 'color', 'pref' => '', 'postf' => '')),
            array('value' => 'size', 'selected' => array('value' => 'size', 'pref' => '', 'postf' => '')),
            array('value' => 'mpn', 'selected' => array('value' => 'mpn_google', 'pref' => '', 'postf' => '')),
            array('value' => 'category', 'selected' => array('value' => 'google_category', 'pref' => '', 'postf' => '')),
            array('value' => 'tax', 'selected' => array('value' => 'google_tax', 'pref' => $countryPref, 'postf' => '')),
            array('value' => 'link', 'selected' => array('value' => 'google_product_link', 'pref' => '', 'postf' => '')),
            array('value' => 'availability', 'selected' => array('value' => 'google_availability', 'pref' => '', 'postf' => '')),
            array('value' => 'shipping', 'selected' => array('value' => 'shipping_cost', 'pref' => $countryPref, 'postf' => '')));

    }

    /**
     * Getting default country code
     * @return string
     */

    protected function _getCountryPrefix()
    {
        $storeId = $this->_helper->getCurrentStoreId();
        $countryCode = Mage::getStoreConfig('general/country/default', $storeId);
        return $countryCode.'::';
    }

    /**
     * @param null $collection
     */
    protected function _setProductsCollection($collection = NULL)
    {
        if ($this->_productCollection === NULL) {
            $collection = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('visibility', array('in' => array(1, 2, 3, 4)))->addAttributeToSelect('*');
            $this->_productCollection = $collection;
        }
        $this->_setCollectionConfigConditions();
    }

    /**
     * Checking if attribute is default
     * @param $attributeCode
     * @return bool
     */

    public function isDefaultAttributeCode($attributeCode)
    {
        $isDefaultOptionValue = false;
        $isDefaultParameter = false;
        foreach($this->_defaultOptionValues as $val){
            if($val['code'] == $attributeCode){
                $isDefaultOptionValue = true;
            }
        }

        foreach($this->_defaultParameters as $param){
            if($param['selected'] == $attributeCode){
                $isDefaultParameter = true;
            }
        }
        return ($isDefaultParameter || $isDefaultOptionValue);
    }

    /**
     * @return null
     */
    public function getProductsCollection()
    {
        return $this->_productCollection;
    }

    /**
     * @return array
     */
    protected function _getHeaderRow()
    {
        $attributes = $this->getFeedConfig();
        $retArray = array();
        foreach ($attributes as $attribute) {
            if (!empty($attribute['value'])) {
                $retArray [] = $attribute['value'];
            }
        }
        return $retArray;
    }

    /**
     * generating feed file
     */
    public function generateFeed()
    {
        $attributes = $this->getFeedConfig();
        $pathToSave = Mage::getBaseDir() . '/' . self::PATH_TO_GENERATE;
        $pathToSave = $this->_createDirIfNotExists($pathToSave);
        $googleCategoriesModel = Mage::getModel('googlemerchants/googlecategory');
        $googleShippingModel = Mage::getModel('googlemerchants/googleshipping');
        $stockModel = Mage::getModel('cataloginventory/stock_item');
        $headerRow = $this->_getHeaderRow();
        $fileNameInConfig = Mage::getStoreConfig('googlemerchants_options/general/google_feedfilename');
        if (empty($fileNameInConfig)) {
            $fileNameInConfig = 'product_feed';
        }
        $fileName = $pathToSave . '/' . $fileNameInConfig . '.csv';
        $file = fopen($fileName, "w");
        fputcsv($file, $headerRow);
        $storeCode = $this->_helper->getSelectedStore();
        foreach ($this->_productCollection as $product) {
            $productArray = array();
            foreach ($attributes as $attribute) {
                $attributeCode = $attribute['selected']['value'];
                $pref = $attribute['selected']['pref'];
                $postf = $attribute['selected']['postf'];
                switch ($attributeCode) {
                    case 'product_id' :
                        $attributeCode = 'entity_id';
                        $entityId = $product->getData($attributeCode);
                        $productArray [] = $this->_appendPrefPostf($pref, $postf, $entityId);
                        break;
                    case 'google_category' :
                        $categoriesStr = $googleCategoriesModel->getCategoriesStr($product->getCategoryIds());
                        if (empty($categoriesStr)) {
                            $this->_notAttachedCategoriesExists = true;
                        }
                        $productArray [] = $this->_appendPrefPostf($pref, $postf,$categoriesStr);
                        break;
                    case 'shipping_cost' :
                        $productArray [] = $this->_appendPrefPostf($pref, $postf, $googleShippingModel->getShippingCost($product));
                        break;
                    case 'image' :
                        $image = '';
                        try {
                            $image = Mage::helper('catalog/image')->init($product, 'thumbnail');
                            if (empty($image)) {
                                $image = $product->getImageUrl();
                            }
                        } catch (Exception $e) {
                            Mage::logException($e);
                        }
                        if(is_string($image)) {
                            $productArray [] = $this->_appendPrefPostf($pref, $postf, $image);
                        }
                        else {
                            $productArray [] = $this->_appendPrefPostf($pref, $postf, '');
                        }
                        break;
                    case 'parent_name' :
                        $parentName = $this->_helper->getParentName($product);
                        $productArray [] = $this->_appendPrefPostf($pref, $postf, $parentName);
                        break;
                    case 'google_tax' :
                        $taxValue = Mage::getModel('googlemerchants/googletax')->getTaxValue($product);
                        $productArray [] = $this->_appendPrefPostf($pref, $postf, $taxValue);
                        break;
                    case 'google_product_link':
                        $productUrl = $this->_helper->getProductUrl($product);
                        $productArray [] = $this->_appendPrefPostf($pref, $postf, $productUrl);
                        break;
                    case 'none':
                        $productArray [] = $this->_appendPrefPostf($pref, $postf, '');
                        break;
                    case 'price':
                        $minPrice = $this->_helper->getMinPrice($product);
                        $productArray [] = $this->_appendPrefPostf($pref, $postf, $minPrice);
                        break;
                    case 'google_availability':

                        $stockStatus = $stockModel->loadByProduct($product)->getIsInStock();
                        if($stockStatus) {
                            $stockStatus = 'in stock';
                        } else {
                            $stockStatus = 'out of stock';
                        }
                        $productArray [] = $this->_appendPrefPostf($pref, $postf, $stockStatus);
                        break;
                    default :
                        $attribute = $product->getResource()->getAttribute($attributeCode);
                        if ($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
                            $attributeValue = $attribute->getFrontend()->getValue($product);
                            $value = $this->_appendPrefPostf($pref, $postf, $attributeValue);
                            $productArray [] = $value;
                        }
                        break;
                }
            }
            $productObj = new Varien_Object();
            $productObj->setData($productArray);
            Mage::dispatchEvent('feed_product_generate_data_after', array('product_data' => $productObj));
            $productArray = $productObj->getData();
            fputcsv($file, $productArray);
        }

        if ($this->_notAttachedCategoriesExists) {
            Mage::getSingleton('adminhtml/session')->addError('Some store categories are not attached to any google category.');
        }
        fclose($file);
    }

    /**
     *
     * @param $prefix
     * @param $postfix
     * @param $value
     * @return string
     */

    protected function _appendPrefPostf($prefix, $postfix, $value)
    {
        if(is_array($value)){
            $value = implode('',$value);
        }
        return $prefix.$value.$postfix;
    }

    /**
     * @param $path
     * @return mixed
     * creting directory and for google feed
     */

    protected function _createDirIfNotExists($path)
    {
        if (!file_exists($path)) {
            mkdir($path);
        }
        $files = glob($path . '/*');
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
        return $path;
    }

    /**
     * @return array
     * returning feed parameters
     */
    public function getFeedConfig()
    {
        $selectionStore = $this->_helper->getSelectedStore();
        $feedConfig = Mage::getStoreConfig('googlemerchants_options/general/google_feed_prameters', $selectionStore);
        $feedConfig = trim($feedConfig);
        $feedConfig = unserialize($feedConfig);
        if (empty($feedConfig)) {
            return $this->_defaultParameters;
        }
        $resArr = array();
        foreach ($feedConfig as $key => $field) {
            $resArr [] = array('value' => $key, 'selected' => $field);
        }
        return $resArr;
    }

    /**
     * @param $value
     * @param string $storeCode
     * @return $this
     * @throws Exception
     */
    public function setFeedConfig($value, $storeCode)
    {
        $config = Mage::getModel('adminhtml/system_config_backend_serialized_array');
        $config->setValue($value);
        $config->setPath($this->_path);
        $config->setScope('stores');
        $storeId = Mage::app()->getStore($storeCode)->getId();
        $config->setScopeId($storeId);
        $config->save();
        Mage::getConfig()->reinit();
        return $this;
    }

    /**
     * add conditions for propducts collection
     */
    protected function _setCollectionConfigConditions()
    {
        $onlyInStockCondition = Mage::getStoreConfig('googlemerchants_options/general/google_feed_export_outofstock');
        $onlyEabledCondition = Mage::getStoreConfig('googlemerchants_options/general/google_feed_export_disabled');
        $unvisibleCondition = Mage::getStoreConfig('googlemerchants_options/general/google_feed_export_unvisible');
        if ($onlyEabledCondition == 0) {
            $this->_productCollection->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
        }
        if ($onlyInStockCondition == 0) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($this->_productCollection);
        }
        if ($unvisibleCondition != 1) {
            $this->_productCollection->addAttributeToFilter('visibility', 4);
        }
    }

    /**
     * @return array
     * returns product attributes
     */
    public function getAttributesOptions()
    {
        $collection = $this->_getAttributesCollection();

        $options = $this->_defaultOptionValues;

        foreach ($collection as $item) {
            $frontendLabel = $item->getFrontendLabel();
            if (!empty($frontendLabel)) {
                $options [] = array('code' => $item->getAttributeCode(), 'label' => $item->getFrontendLabel());
            }
        }
        $this->_attributeSelectOptions = $options;
        return $this->_attributeSelectOptions;
    }

    public function generateFeedByCron()
    {
        $this->_byCron = true;
        if($this->_shouldGenerateFeedByCron){
            $this->generateFeed();
        }
    }

    /**
     * @return mixed
     */
    protected function _getAttributesCollection()
    {
        if ($this->_attributesCollection === NULL)
            $this->_attributesCollection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setItemObjectClass('catalog/resource_eav_attribute')
                ->setEntityTypeFilter(Mage::getResourceModel('catalog/product')->getTypeId())
                ->setOrder('frontend_label','ASC');

        return $this->_attributesCollection;
    }

    protected function _appendStoreFilter()
    {
        if($this->_byCron){
            $selectedStore = $this->_helper->getDefaultFrontendStoreView();
        }
        else{
            $storeCode = $this->_helper->getStoreCodeFromPost();
            $selectedStore = Mage::getModel("core/store")->load($storeCode);
        }
        $this->_productCollection->setStore($selectedStore->getId());
    }

}