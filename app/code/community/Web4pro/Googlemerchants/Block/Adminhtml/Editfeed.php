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
class Web4pro_Googlemerchants_Block_Adminhtml_Editfeed extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_headerText = 'Manage feed settings';

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'googlemerchants';
        $this->_controller = 'adminhtml_editfeed';
        $this->_addButton('save', array(
            'label' => Mage::helper('adminhtml')->__('Save'),
            'onclick' => 'editForm.submit();',
            'class' => 'save',
        ), 1);

        $this->_addButton('generate_feed', array(
            'label' => Mage::helper('adminhtml')->__('Generate feed .csv'),
            'class' => 'save',
            'onclick' => 'setLocation(\'' . $this->getGenerateFeedUrl() . '\')'
        ), 1);
        $this->_updateButton('reset', 'onclick', 'setLocation(\'' . $this->getResetFeedUrl() . '\')');
        $this->removeButton('back');
    }


    /**
     * @return mixed
     */
    public function getSaveUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/savefeed', array('store' => Mage::app()->getRequest()->getParam('store')));
    }

    /**
     * @return mixed
     */
    public function getValidationUrl()
    {
        //return false;
        return $this->getSaveUrl();
    }

    /**
     * @return mixed
     */
    public function getGenerateFeedUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/generatefeed');
    }

    /**
     * @return mixed
     */
    public function getResetFeedUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/resetfeed');
    }
}
