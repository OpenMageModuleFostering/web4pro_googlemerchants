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
    protected $_attributesArray = NULL, $_productCollection = NULL, $_helper = NULL, $_byCron = false,
        $_path = 'googlemerchants_options/general/google_feed_prameters',
        $_notAttachedCategoriesExists = false, $_attributesCollection,
        $_defaultParameters = array(
        array('value' => 'id', 'selected' => 'product_id'),
        array('value' => 'title', 'selected' => 'name'),
        array('value' => 'description', 'selected' => 'description'),
        array('value' => 'price', 'selected' => 'price'),
        array('value' => 'condition', 'selected' => 'condition_google'),
        array('value' => 'image link', 'selected' => 'image'),
        array('value' => 'gtin', 'selected' => 'gtin_google'),
        array('value' => 'color', 'selected' => 'color'),
        array('value' => 'size', 'selected' => 'size'),
        array('value' => 'mpn', 'selected' => 'mpn_google'),
        array('value' => 'category', 'selected' => 'google_category'),
        array('value' => 'tax', 'selected' => 'google_tax'),
        array('value' => 'link', 'selected' => 'google_product_link'),
        array('value' => 'availability', 'selected' => 'google_availability')),

        $_defaultOptionValues = array(
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
    }

    /**
     * @param null $collection
     */
    protected function _setProductsCollection($collection = NULL)
    {
        if ($this->_productCollection === NULL) {
            $collection = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('visibility', array('in' => array(1, 2, 3, 4)))->addAttributeToSelect('*');
            //$collection->setStoreId();
            $this->_productCollection = $collection;
        }
        $this->_setCollectionConfigConditions();
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
                $attributeCode = $attribute['selected'];
                switch ($attributeCode) {
                    case 'product_id' :
                        $attributeCode = 'entity_id';
                        $productArray [] = $product->getData($attributeCode);
                        break;
                    case 'google_category' :
                        $categoriesStr = $googleCategoriesModel->getCategoriesStr($product->getCategoryIds());
                        if (empty($categoriesStr)) {
                            $this->_notAttachedCategoriesExists = true;
                        }
                        $productArray [] = $categoriesStr;
                        break;
                    case 'shipping_cost' :
                        $productArray [] = $googleShippingModel->getShippingCost($product);
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
                            $productArray [] = $image;
                        }
                        else {
                            $productArray [] = '';
                        }
                        break;
                    case 'parent_name' :
                        $productArray [] = $this->_helper->getParentName($product);
                        break;
                    case 'google_tax' :
                        $productArray [] = Mage::getModel('googlemerchants/googletax')->getTaxValue($product);
                        break;
                    case 'google_product_link':
                        $productArray [] = $this->_helper->getProductUrl($product);
                        break;
                    case 'none':
                        $productArray [] = '';
                        break;
                    case 'price':
                        $productArray [] = $this->_helper->getMinPrice($product);
                        break;
                    case 'google_availability':

                        $stockStatus = $stockModel->loadByProduct($product)->getIsInStock();
                        if($stockStatus) {
                            $stockStatus = 'in stock';
                        } else {
                            $stockStatus = 'out of stock';
                        }
                        $productArray [] = $stockStatus;
                        break;
                    default :
                        $attribute = $product->getResource()->getAttribute($attributeCode);
                        if ($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
                            $productArray [] = $attribute->getFrontend()->getValue($product);
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
        $feedConfig = unserialize($feedConfig);
        if (empty($feedConfig)) {
            return $this->_defaultParameters;
        }
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
        $inStockCondition = Mage::getStoreConfig('googlemerchants_options/general/google_feed_export_outofstock');
        $enabledCondition = Mage::getStoreConfig('googlemerchants_options/general/google_feed_export_disabled');
        $unvisibleCondition = Mage::getStoreConfig('googlemerchants_options/general/google_feed_export_unvisible');
        if ($enabledCondition == 1) {
            $this->_productCollection->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
        }
        if ($inStockCondition == 1) {
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
        $this->generateFeed();
    }

    /**
     * @return mixed
     */
    protected function _getAttributesCollection()
    {
        if ($this->_attributesCollection === NULL)
            $this->_attributesCollection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setItemObjectClass('catalog/resource_eav_attribute')
                ->setEntityTypeFilter(Mage::getResourceModel('catalog/product')->getTypeId());
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
        $this->_productCollection->setPageSize(20)
            ->setCurPage(1);
    }

}