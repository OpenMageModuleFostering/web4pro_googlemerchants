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
class Web4pro_Googlemerchants_Block_Adminhtml_Upload_File extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws Exception
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/savetxt', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);

        $this->setForm($form);


        $fieldset = $form->addFieldset('googlemerchants_form', array('legend' => Mage::helper('googlemerchants')->__('Uploading file with google categories.')));
        $fieldset->addType('extended_link', 'Web4pro_Googlemerchants_Block_Adminhtml_Upload_Link_Renderer');

        $fieldset->addField('title', 'extended_link', array(
            'class' => 'required-entry',
            //'value'  => 'Click <a href="https://support.google.com/merchants/answer/160081" target="_blank">here</a> for downloading google categories file.',
            'disabled' => false,
            'readonly' => true,

        ));

        $fieldset->addField('fileinputname', 'file', array(
            'required' => true,
            'name' => 'fileinputname',
            'class' => 'form-button-custom'
        ));
        $fieldset->addField('submit', 'submit', array(
            'value' => 'Start Import',
            'class' => 'form-button form-button-custom',
            'action' => $this->getUrl('*/*/savetxt'),
            'tabindex' => 1,
        ));

        return parent::_prepareForm();
    }
}
