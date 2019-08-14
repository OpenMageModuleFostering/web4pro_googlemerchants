<?php

class Web4pro_Googlemerchants_Model_Storefeedconfig
{
    public function toOptionArray() {
        return Mage::helper('googlemerchants')->getStoresAssocArray();
    }
}