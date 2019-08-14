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
class Web4pro_Googlemerchants_Block_Adminhtml_Upload_Form_Container extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_headerText = 'Upload file with google categories';

    /**
     * @return Web4pro_Googlemerchants_Block_Adminhtml_Upload_Form_Container
     */
    protected function _prepareLayout()
    {
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                $childId = $this->_prepareButtonBlockId($id);
                $this->_addButtonChildBlock($childId);
            }
        }
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('save');
        return $this;
    }

    /**
     * @return string
     */
    public function getFormHtml()
    {
        $form = $this->getLayout()->createBlock('googlemerchants/adminhtml_upload_file');
        return $form->toHtml();
    }

}