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
class Web4pro_Googlemerchants_Model_Googlecategory extends Mage_Core_Model_Abstract
{
    protected $_items = array(), $_iterator = 0, $_collection, $_model = NULL, $_rootCategoryId = NULL;
    const TREE_ROOT_NAME = 'Root google category';

    public function _construct()
    {
        parent::_construct();
        $this->_init('googlemerchants/googlecategory');
        $this->_collection = $this->getCollection();
        $this->_iterator = 0;
    }

    /**
     * @param $name
     * @param $parentCatName
     * @return bool
     */
    public function insertItem($name, $parentCatName)
    {
        $name = trim($name);
        $parentCatName = trim($parentCatName);
        $itemExist = $this->getCollection()->addFieldToFilter('title', array('eq' => $name));
        if ($itemExist->count() > 0) {
            return false;
        }
        $parentId = $this->getRootCategoryId();
        if ($parentCatName !== NULL) {
            $parentItem = $this->getCollection()->addFieldToFilter('title', array('eq' => $parentCatName))->getFirstItem();
            $parentCatId = $parentItem->getCategoryId();
            if (!empty($parentCatId)) {
                $parentId = $parentCatId;
            }
        }

        $table = Mage::getModel('core/resource')->getTableName('googlemerchants_cats');
        $write = Mage::getModel('core/resource')->getConnection('core_write');
        $write->insert($table, array('title' => $name, 'status' => 0, 'parent_id' => $parentId));
    }

    /**
     * @param string $rootCatName
     */
    public function insertRoot($rootCatName = '')
    {
        if (empty($rootCatName)) {
            $rootCatName = Web4pro_Googlemerchants_Model_Googlecategory::TREE_ROOT_NAME;
        }
        $rootItems = $this->getCollection()->addFieldToFilter('title', array('eq' => $rootCatName));
        if ($rootItems->count() > 0) {
            return;
        }
        $table = Mage::getModel('core/resource')->getTableName('googlemerchants_cats');
        $write = Mage::getModel('core/resource')->getConnection('core_write');
        $write->insert($table, array('title' => $rootCatName, 'status' => 0, 'parent_id' => NULL));

    }

    /**
     * @return null
     */
    public function getRootCategoryId()
    {
        if ($this->_rootCategoryId === NULL) {
            $rootCategoryName = Web4pro_Googlemerchants_Model_Googlecategory::TREE_ROOT_NAME;
            $rootCategory = $this->getCollection()->addFieldToFilter('title', array('eq' => $rootCategoryName))->getFirstItem();
            $this->_rootCategoryId = $rootCategory->getCategoryId();
        }
        return $this->_rootCategoryId;
    }

    /**
     *  clearing table with google categories
     */

    public function truncateCategoriesTable()
    {
        $coreResourceModel = Mage::getSingleton('core/resource');
        $resource = $coreResourceModel->getConnection('core_read');
        $resource->query("truncate table " . $coreResourceModel->getTableName('googlemerchants_cats'));
    }

    /**
     * @param null $categoryId
     * @return mixed
     */

    public function getChildrenCount($categoryId = NULL)
    {
        if ($categoryId === NULL) {
            $categoryId = $this->getCategoryId();
        }
        $collection = $this->getCollection()->addFieldToFilter('parent_id', array('eq' => $categoryId));
        $count = $collection->count();
        return $count;
    }

    /**
     * @param $name
     * @return null
     */

    protected function _getParentCatIdByName($name)
    {
        if ($name === NULL || empty($name)) {
            return NULL;
        }
        foreach ($this->_items as $item) {
            $itemName = $item->getTitle();
            if ($itemName == $name) {
                return $item->getCategoryId();
            }
        }
    }

    /**
     * @param $parentCategory
     * @return array
     */
    public function getChildrenCategories($parentCategory)
    {
        $children = array();
        if ($parentCategory instanceof Web4pro_Googlemerchants_Model_Googlecategory) {
            $children = $this->getCollection()->addFieldToFilter('parent_id', array('eq' => $parentCategory->getCategoryId()))
                ->addFieldToFilter('category_id', array('neq' => $this->getRootCategoryId()));
        } else if (is_numeric($parentCategory)) {
            $children = $this->getCollection()->addFieldToFilter('parent_id', array('eq' => $parentCategory))
                ->addFieldToFilter('category_id', array('neq' => $this->getRootCategoryId()));
        }
        return $children;
    }

    /**
     * @param $storeCatIds
     * @return string
     */
    public function getCategoriesStr($storeCatIds)
    {
        $categoriesStr = '';
        $categoriesArr = array();
        if (!empty($storeCatIds)) {
            if (is_array($storeCatIds)) {
                $storeCatIds = current($storeCatIds);
            }
            $category = $this->load($storeCatIds);
            $categoriesArr [] = $category->getTitle();
            $parentId = $category->getParentId();
            while ($parentId !== NULL) {
                $category = $this->load($parentId);
                $categoriesArr [] = $category->getTitle();
                $parentId = $category->getParrentId();
            }

        }
        return implode('>', $categoriesArr);
    }

    /**
     * @param $categoryId
     * @return bool
     */
    public function isRootCategory($categoryId)
    {
        $category = $this->load($categoryId);
        $parentCategoryId = $category->getData('parent_id');
        if ($parentCategoryId === NULL) {
            return true;
        }
        return false;
    }

}