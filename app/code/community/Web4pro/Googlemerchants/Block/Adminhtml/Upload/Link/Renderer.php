<?php

class Web4pro_Googlemerchants_Block_Adminhtml_Upload_Link_Renderer extends Varien_Data_Form_Element_Abstract
{
    /**
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('label');
    }

    /**
     * @return string
     */

    public function getElementHtml()
    {
        return '';
    }

    public function getLabelHtml()
    {
        $html = '<div>
            <h3>Import google categories in two easy steps:</h3>
            <ul style="list-style-type: circle;">
                <li><p>1. Download file with google merchants categories <a href="https://support.google.com/merchants/answer/160081">here:</a></p>
                    <div id="upload-hint-image">
                        <img src="https://docs.google.com/drawings/d/1WB73TBx5S7Q9gfRzzh98BR_qLaHWGn8zJ-1KrKXhcvA/pub?w=749&h=462" alt="Download file with categories">
                    </div>
                </li>
                <li>
                    <span>2. Select downloaded file and click on button "Start import":</span>
                </li>
            </ul>
            </div>
        ';
        return $html;
    }

}