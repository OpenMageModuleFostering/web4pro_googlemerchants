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
class Web4pro_Googlemerchants_Block_Adminhtml_Googlecategory_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_model = NULL, $_helper = NULL;

    /**
     * @param null $parenNodeCategory
     * @return array
     */

    public function getTree($parenNodeCategory = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    /**
     * @param null $parenNodeCategory
     * @return Mage_Core_Model_Abstract
     */

    public function getRoot($parenNodeCategory = NULL)
    {
        if ($parenNodeCategory === NULL) {
            return $this->_getModel()->load($this->_getModel()->getRootCategoryId());
        }
        return $this->_getModel()->load($parenNodeCategory->getCategoryId());
    }


    /**
     * @return bool
     */

    public function treeCanShow()
    {
        $googleCatCount = $this->_getHelper()->getGoogleCategoriesCount();
        return ($googleCatCount > 0) ? true : false;
    }

    /**
     * @param null $parenNodeCategoryId
     * @return mixed
     */
    public function getTreeJson($parenNodeCategoryId = NULL)
    {

        if ($parenNodeCategoryId === NULL) {
            $parenNodeCategoryId = (int)$this->_getModel()->getRootCategoryId();
        }
        $parenNodeCategory = $this->_getModel()->load($parenNodeCategoryId);
        $children = $parenNodeCategory->getChildrenCategories($parenNodeCategory);
        $childrensArr = array();
        foreach ($children as $child) {
            $childArray = $this->_getHelper()->categoryEntityToTreeStd($child);
            $childrensArr [] = $childArray;
        }
        $rootElement = $this->_getHelper()->categoryEntityToTreeStd($parenNodeCategory);
        $rootElement['children'] = $childrensArr;
        $rootElement['expanded'] = false;

        if ($rootElement['id'] == $this->_getModel()->getRootCategoryId()) {
            $rootElement['expanded'] = true;
            $json = Mage::helper('core')->jsonEncode(array($rootElement));
        } else {
            $rootElement = $childrensArr;
            $json = Mage::helper('core')->jsonEncode($rootElement);

        }

        return $json;
    }

    /**
     * @return false|Mage_Core_Model_Abstract|null
     */

    protected function _getModel()
    {
        if ($this->_model === NULL) {
            $this->_model = Mage::getModel('googlemerchants/googlecategory');
        }
        return $this->_model;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->unsetChild('store_switcher');
    }

    /**
     * @return Web4pro_Googlemerchants_Helper_Data
     */
    protected function _getHelper()
    {
        if ($this->_helper === NULL) {
            $this->_helper = Mage::helper('googlemerchants');
        }
        return $this->_helper;
    }

    /**
     * @return mixed
     */

    public function getLinkedCategoryActionUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/getLinkedCategory');
    }

    /**
     * @return mixed
     */
    public function getEditUrl()
    {
        return Mage::helper("adminhtml")->getUrl('googlemerchants/googlemerchants/edit');
    }

}