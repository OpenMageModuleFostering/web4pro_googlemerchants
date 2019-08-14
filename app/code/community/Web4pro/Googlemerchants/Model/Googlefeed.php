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
    protected $_attributesArray = NULL, $_productCollection = NULL, $_path = 'googlemerchants_options/general/google_feed_prameters', $_notAttachedCategoriesExists = false, $_attributesCollection,
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
        array('value' => 'availability', 'selected' => 'google_availability'));

    const PATH_TO_GENERATE = 'var/productsfeed';

    public function _construct()
    {
        parent::_construct();
        $this->_init('googlemerchants/googlefeed');
        $this->_setProductsCollection();
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
        $helper = Mage::helper('googlemerchants');
        $headerRow = $this->_getHeaderRow();
        $fileNameInConfig = Mage::getStoreConfig('googlemerchants_options/general/google_feedfilename');
        if (empty($fileNameInConfig)) {
            $fileNameInConfig = 'product_feed';
        }
        $fileName = $pathToSave . '/' . $fileNameInConfig . '.csv';
        $file = fopen($fileName, "w");
        fputcsv($file, $headerRow);
        $storeCode = Mage::helper('googlemerchants')->getSelectedStore();
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
                        $productArray [] = Mage::helper('googlemerchants')->getParentName($product);
                        break;
                    case 'google_tax' :
                        $productArray [] = Mage::getModel('googlemerchants/googletax')->getTaxValue($product);
                        break;
                    case 'google_product_link':
                        $productArray [] = Mage::helper('googlemerchants')->getProductUrl($product);
                        break;
                    case 'none':
                        $productArray [] = '';
                        break;
                    case 'price':
                        $productArray [] = $helper->getMinPrice($product);
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
        $feedConfig = Mage::getStoreConfig('googlemerchants_options/general/google_feed_prameters', Mage::helper('googlemerchants')->getSelectedStore());
        if (empty($feedConfig)) {
            return $this->_defaultParameters;
        }
        $unserializedConfig = unserialize($feedConfig);
        if (empty($unserializedConfig)) {
            return $this->_defaultParameters;
        }
        $feedConfig = unserialize($feedConfig);
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
    public function setFeedConfig($value, $storeCode = '')
    {

        $config = Mage::getModel('core/config_data');
        $config->setValue(serialize($value));
        $config->setPath($this->_path);
        $config->setScope('stores');
        $storeCode = Mage::helper('googlemerchants')->getSelectedStore();
        if (empty($storeCode)) {
            $storeCode = Mage::app()->getStore()->getCode();
        }
        $config->setScopeId(Mage::app()->getStore($storeCode)->getId());
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
        $options = array();
        $options [] = array('code' => 'product_id', 'label' => 'Product ID');
        $options [] = array('code' => 'google_category', 'label' => 'Google Category');
        $options [] = array('code' => 'shipping_cost', 'label' => 'Shipping Cost');
        $options [] = array('code' => 'parent_name', 'label' => 'Parent Name');
        $options [] = array('code' => 'google_tax', 'label' => 'Tax');
        $options [] = array('code' => 'google_product_link', 'label' => 'Product URL');
        $options [] = array('code' => 'google_availability', 'label' => 'Availability (in/out of stock)');

        foreach ($collection as $item) {
            $frontendLabel = $item->getFrontendLabel();
            if (!empty($frontendLabel)) {
                $options [] = array('code' => $item->getAttributeCode(), 'label' => $item->getFrontendLabel());
            }
        }
        $this->_attributeSelectOptions = $options;
        return $this->_attributeSelectOptions;
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
}