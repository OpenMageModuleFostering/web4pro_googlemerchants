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
class Web4pro_Googlemerchants_Block_Adminhtml_Googlecategory_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws Exception
     */

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
            )
        );
        $form->setUseAjax(true);
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('googlemerchants_form', array('legend' => Mage::helper('googlemerchants')->__('Related store category')));

        $fieldset->addField('category_to_linking', 'select', array(
            'label' => Mage::helper('googlemerchants')->__('Select store category'),
            'required' => false,
            'name' => 'category_to_linking',
            'options' => $this->_getStoreCategoriesOptions(),
            'disabled' => true,
        ));

        $fieldset->addField('category_to_linking_submit', 'button', array(
            'required' => true,
            'value' => 'Save',
            'onclick' => 'saveGCategoryLink()',
            'disabled' => true,
        ));
        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    protected function _getStoreCategoriesOptions()
    {
        $rootCatIds = Mage::helper('googlemerchants')->getRootCategoriesIds();
        $categoriesCollection = Mage::getSingleton('catalog/category')->getCollection()
            ->addAttributeToSelect('*')
            ->addIsActiveFilter();
        $retArray = array();
        $retArray[0] = '';
        foreach ($categoriesCollection as $category) {
            $catId = $category->getId();
            $catName = $category->getName();
            if (in_array($catId, $rootCatIds)) {
                continue;
            }
            $retArray[$catId] = $catName;
        }
        return $retArray;
    }

    /**
     * @return string
     */

    public function getFormHtml()
    {
        $googleCatCount = Mage::helper('googlemerchants')->getGoogleCategoriesCount();
        return ($googleCatCount > 0) ? parent::getFormHtml() : '';
    }

}