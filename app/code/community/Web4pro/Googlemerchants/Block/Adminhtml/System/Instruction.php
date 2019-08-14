<?php

class Web4pro_Googlemerchants_Block_Adminhtml_System_Instruction extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '
                <h3><p>Google merchants service allows you to improve sales by better indexing of your store products, as well as the availability of goods in the google search results.</p></h3>
                                <h3 align="center">Google merchants extension setting up:</h3>
                                <ul style="list-style-type: decimal;">
                                    <li>See <a href="https://support.google.com/merchants/answer/188494" target="_blank"> the documentation </a> for google feed.</li>
                                    <li>Set up necessary product attributes on “Google attributes” tab:
                                    <img src="https://docs.google.com/drawings/d/1BdC5Fj0IgBs0evVO4m_32glUadIIVHAy-em6oeFGpJA/pub?w=960&h=720" alt="Set up google attributes" width="650" height="400">
                                    </li>
                                    <li>Go to <a href="' . $this->_getGoogleCategoriesUrl() . '" target="_blank">Web4pro → Manage Google Merchants settings → Manage Google Categories </a> and follow the instructions.</li>
                                    <li>Attach all categories in your store to Google categories. Just click on google category, select store category and click “Save”:
                                        <img src="https://docs.google.com/drawings/d/1Y2IYm9WfmFMCU9YRbbrKPdPspinBE1paJoW8nE4n0X8/pub?w=640&h=413" alt="Upload filewith categories">
                                    </li>
                                    <li><p>And the last step is setting up feed. You need to choose necessary attributes, which you would like to import.</p>
                                        <p>Go to <a href="' . $this->_getFeedSettingsUrl() . '">Web4pro → Manage Google Merchants settings → Configure Feed settings </a>:
                                            and setup all necessary attributes according to <a href="https://support.google.com/merchants/answer/188494" target="_blank">feed specification</a>:</p>
                                            <p><b>Please Note:</b> some fields in google feen need the postfix or the prefix to be setted.</p>
                                            <img src="https://docs.google.com/drawings/d/1ZDkUUOBwtkLoFHrifmgSgzM87wln1qNlAuwJ6M5vzJw/pub?w=854&h=615" alt="Set up feed settings" width="650">
                                        </li>

                                    <li>Set up necessary settings of importing products  (Web4pro → Manage Google Merchants settings → Manage Google Merchants settings -> Manage Google Feed settings) ↓ :
                                    </li>

                                <ul>
                                </br>
                                </br>
                                </br>
                                <h3 align="center">Set up import settings:</h3>
        ';
        return $html;
    }

    protected function _getGoogleCategoriesUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/index');
    }

    protected function _getFeedSettingsUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/feed');
    }

}