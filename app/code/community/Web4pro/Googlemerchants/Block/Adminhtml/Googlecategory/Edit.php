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
class Web4pro_Googlemerchants_Block_Adminhtml_Googlecategory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_headerText = 'Attach google categories to store cateogries.';

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'googlemerchants';
        $this->_controller = 'adminhtml_googlecategory';
        $this->addButton('clear_google_categories', array(
            'label' => Mage::helper('adminhtml')->__('Clear google categories'),
            'class' => 'delete',
            'onclick' => 'setLocation(\'' . $this->getCrearCategoriesUrl() . '\')'
        ), 1);
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('save');
    }

    /**
     * @return string
     */
    protected function _getLinkedCategoryActionUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/getLinkedCategory');
    }

    /**
     * @return mixed
     */
    public function getSaveStoreCategoryLinkUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/saveStoreCategoryLink');
    }

    /**
     * @return mixed
     */
    public function getCrearCategoriesUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/clearGoogleCategories');
    }

} 