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
                'action' => $this->getUrl('adminhtml/googlemerchants/savefeed', array('store' => Mage::helper('googlemerchants')->getStoreCodeFromPost(), 'isAjax' => true)),
                'method' => 'post',
            )
        );
        $this->setForm($form);
        $this->setTemplate('web4pro_googlemerchants/feed/form.phtml');

        return parent::_prepareForm();
    }

    /**
     * render select html fields for feed configuration
     * @param int $nameIndex
     * @param string $selectedVal
     * @return string
     */

    public function getSelectHtml($nameIndex = 0, $selectedVal = '')
    {
        $retStr = '<select class="required-entry select select-googlefeed" name="attribute-select[' . $nameIndex . ']">';
        $retStr .= $this->getOptionsHtml($selectedVal);
        $retStr .= '</select>';
        return $retStr;
    }

    /**
     * render options html fields for select fields
     * @param string $selectedVal
     * @return string
     */

    public function getOptionsHtml($selectedVal = '')
    {
        $options = Mage::getModel('googlemerchants/googlefeed')->getAttributesOptions();
        $retStr = '<option value=""></option>';
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
     * rendering the table body for feed table
     * @return string
     */

    public function getTableBodyHtml()
    {
        $data = Mage::getModel('googlemerchants/googlefeed')->getFeedConfig();
        $index = 0;
        $resStr = '';
        foreach ($data as $val) {
            $attributeExists = $this->_isAttributeExists($val['selected']['value']);
            if (!isset($val['value']) || !isset($val['selected']) || !$attributeExists) {
                continue;
            }
            $resStr .= '<tr id="table-row-' . $index . '" class="table-row-googlefeed">';
            $resStr .= '<td><input class="required-entry" name="feed-col-name[' . $index . ']" value="' . $val['value'] . '"/></td>';
            $resStr .= '<td><input name="feed-pref-name[' . $index . ']" value="' . $val['selected']['pref'] . '" class="prefix-postfix-input-field"/></td>';
            $resStr .= '<td>' . $this->getSelectHtml($index, $val['selected']['value']) . '</td>';
            $resStr .= '<td><input name="feed-postf-name[' . $index . ']" value="' . $val['selected']['postf'] . '"  class="prefix-postfix-input-field"/></td>';
            $resStr .= '<td>' . $this->getRemoveRowButtonHtml($index) . '</td>';
            $resStr .= '</tr>';
            $index++;
        }
        return $resStr;
    }

    /**
     * Check if attribute exists
     * @param $attributeCode
     */

    protected function _isAttributeExists($attributeCode)
    {
        $entity = 'catalog_product';
        $attr = Mage::getResourceModel('catalog/eav_attribute')
            ->loadByCode($entity,$attributeCode);
        $isDefaultAttr = Mage::getModel('googlemerchants/googlefeed')->isDefaultAttributeCode($attributeCode);
        if($attr->getId() || $isDefaultAttr){
            return true;
        }
        return false;
    }

    /**
     * @param int $nameIndex
     * @return string
     */

    protected function getRemoveRowButtonHtml($nameIndex = 0)
    {
        return '<button onclick="removeRow(' . $nameIndex . '); " class="delete delete-option">Remove</button>';
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

    public function getAvailableStores()
    {
        return Mage::helper('googlemerchants')->getStoresAssocArray();
    }
}