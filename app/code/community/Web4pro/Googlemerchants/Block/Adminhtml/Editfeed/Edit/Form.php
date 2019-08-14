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
class Web4pro_Googlemerchants_Block_Adminhtml_Editfeed_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_attributesCollection = NULL, $_attributeSelectOptions = NULL;

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('adminhtml/googlemerchants/savefeed', array('store' => Mage::app()->getRequest()->getParam('store'), 'isAjax' => true)),
                'method' => 'post',
            )
        );
        $this->setForm($form);
        $this->setTemplate('web4pro_googlemerchants/feed/form.phtml');

        return parent::_prepareForm();
    }

    /**
     * @param int $nameIndex
     * @param string $selectedVal
     * @return string
     */

    public function getSelectHtml($nameIndex = 0, $selectedVal = '')
    {
        $retStr = '<select name="attribute-select[' . $nameIndex . ']">';
        $retStr .= $this->getOptionsHtml($selectedVal);
        $retStr .= '</select>';
        return $retStr;
    }

    /**
     * @param string $selectedVal
     * @return string
     */

    public function getOptionsHtml($selectedVal = '')
    {
        $options = Mage::getModel('googlemerchants/googlefeed')->getAttributesOptions();
        $retStr = '<option value="none"></option>';
        foreach ($options as $option) {
            if ($option['code'] == $selectedVal) {
                $retStr .= '<option value="' . addslashes($option['code']) . '" selected>' . addslashes($option['label']) . '</option>';
            } else {
                $retStr .= '<option value="' . addslashes($option['code']) . '">' . addslashes($option['label']) . '</option>';
            }
        }
        return $retStr;
    }

    /**
     * @return string
     */

    public function getTableBodyHtml()
    {
        $data = Mage::getModel('googlemerchants/googlefeed')->getFeedConfig();
        $index = 0;
        $resStr = '';
        foreach ($data as $val) {
            if (!isset($val['value']) || !isset($val['selected'])) {
                continue;
            }
            $resStr .= '<tr id="table-row-' . $index . '">';
            $resStr .= '<td><input name="feed-col-name[' . $index . ']" value="' . $val['value'] . '"/></td>';
            $resStr .= '<td>' . $this->getSelectHtml($index, $val['selected']) . '</td>';
            $resStr .= '<td>' . $this->getRemoveRowButtonHtml($index) . '</td>';
            $resStr .= '</tr>';
            $index++;
        }

        return $resStr;
    }

    /**
     * @param int $nameIndex
     * @return string
     */

    protected function getRemoveRowButtonHtml($nameIndex = 0)
    {
        return '<button onclick="removeRow(' . $nameIndex . ')">Remove</button>';
    }

    /**
     * @return string
     */

    public function getSaveUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/savefeed', array('store' => Mage::app()->getRequest()->getParam('store')));
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getSaveUrl();
    }

    /**
     * @return mixed
     */
    public function getFeedAjaxUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/feedajax');
    }

}