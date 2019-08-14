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
class Web4pro_Googlemerchants_Adminhtml_GooglemerchantsController extends Mage_Adminhtml_Controller_Action
{

    protected $_fileInputData = NULL;
    protected $_categoriesSimpleArray = array();

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('googlemerchants/googlemerchants')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }

    /**
     *  renders google categories tree and store categories select
     */
    public function indexAction()
    {
        $googleCategoriesCount = Mage::getModel('googlemerchants/googlecategory')->getCollection()->count();
        if ($googleCategoriesCount == 0) {
            if(ini_get('safe_mode'))
            {
                Mage::getSingleton('adminhtml/session')->addWarning('Please note, that PHP is running in safe mode. For correct import please increase the parameter "max_execution_time" in the php.ini file.');
            }
            $this->_redirect('adminhtml/googlemerchants/downloadtxt');
        }
        Mage::getSingleton('core/session')->addNotice('Please append all store categories to google categories.');
        $this->_initAction();
        $this->renderLayout();

    }

    /**
     * downloading file from google and export google categories to store
     */
    public function downloadtxtAction()
    {
        $googleCategoriesCount = Mage::getModel('googlemerchants/googlecategory')->getCollection()->count();
        if ($googleCategoriesCount != 0) {
            $this->_redirect('adminhtml/googlemerchants/index');
        }
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * exporting txt with google categories file to store
     */
    public function savetxtAction()
    {
        set_time_limit (10000);
        if (isset($_FILES['fileinputname']['name']) && $_FILES['fileinputname']['name'] != '') {
            try {
                $uploader = new Varien_File_Uploader('fileinputname');
                $uploader->setAllowedExtensions(array('txt'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $path = Mage::getBaseDir('media') . DS . 'txt' . DS;

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $files = glob($path . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                $saveResult = $uploader->save($path, $_FILES['fileinputname']['name']);
                $path = $saveResult['path'] . $saveResult['name'];
                $fileContents = file_get_contents($path);
                $rows = explode("\n", $fileContents);
                $model = Mage::getModel('googlemerchants/googlecategory');
                //$model->truncateCategoriesTable();
                $model->insertRoot('');
                foreach ($rows as $row => $data) {
                    if ($row == 0) {
                        continue;
                    }
                    $cols = explode('>', $data);
                    foreach ($cols as $index => $col) {
                        if (!empty($col)) {
                            if ($index > 0) {
                                $model->insertItem($col, $cols[$index - 1]);
                            } else {
                                $model->insertItem($col, NULL);
                            }
                        }
                    }
                }
                $this->_redirect('adminhtml/googlemerchants/index');

            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirect('adminhtml/googlemerchants/downloadtxt');
                $error = true;
            }
        } else {
            Mage::getSingleton('core/session')->addError('Please select file with google categories.');
            $this->_redirect('adminhtml/googlemerchants/downloadtxt');
        }
    }

    /**
     * saving categories attachment
     */

    public function saveStoreCategoryLinkAction()
    {
        if ($this->getRequest()->isAjax()) {
            $post = $this->getRequest()->getPost();
            if (isset($post['google_category_id']) && isset($post['store_category_id'])) {
                $model = Mage::getModel('googlemerchants/googlecategory');
                $this->getResponse()->setHeader('Content-type', 'application/json');
                if ($model->isRootCategory($post['google_category_id'])) {
                    $attachToRootError = __('Can not attach to store categories to Root google category');
                    Mage::getSingleton('core/session')->addError($attachToRootError);
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('error' => 'true')));
                } else {
                    $googleCategory = $model->load($post['google_category_id']);
                    $googleCategoryName = $googleCategory->getTitle();
                    $googleCategory->setStoreCatId($post['store_category_id']);

                    $storeCategory = Mage::getModel('catalog/category')->load($post['store_category_id']);
                    $storeCategoryName = $storeCategory->getName();
                    $googleCategory->save();
                    $messageBlockHtml = $this->getLayout()->createBlock('core/messages')->addSuccess('Google category "' . $googleCategoryName . '" has been attached to store category "' . $storeCategoryName . '."')->toHtml();
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('error' => 'false', 'message_block_html' => '<div class="ajax-msg">'.$messageBlockHtml.'</div>')));
                }
            }
        } else {
            exit();
        }
    }

    /**
     * return name of store category attached to google category
     */

    public function getLinkedCategoryAction()
    {
        if ($this->getRequest()->isAjax()) {
            $post = $this->getRequest()->getPost();
            $storeCatId = NULL;
            if (isset($post['google_category_id'])) {
                $responce = array();
                $googleCatId = $post['google_category_id'];
                $model = Mage::getModel('googlemerchants/googlecategory');

                $googleCategory = $model->load($googleCatId);
                if ($model->isRootCategory($googleCatId)) {
                    $responce['store_category_id'] = NULL;
                    $responce['is_root'] = true;
                } else {
                    $storeCatId = $googleCategory->getStoreCatId();
                    $responce['store_category_id'] = $storeCatId;
                    $responce['is_root'] = false;
                }

            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responce));
        } else {
            exit();
        }
    }

    public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('googlemerchants/adminhtml_googlecategory_tree')
                ->getTreeJson($this->getRequest()->getParam('id'))
        );
    }

    /**
     *  load settings for feed
     */
    public function feedAction()
    {
        $googleCategoriesCount = $count = Mage::getModel('googlemerchants/googlecategory')->getCollection()->count();
        $importGoogleCategoriesUrl = Mage::helper("adminhtml")->getUrl('adminhtml/googlemerchants/index');
        if ($googleCategoriesCount == 0) {
            Mage::getSingleton('core/session')->addError('Please <a href="' . $importGoogleCategoriesUrl . '">import</a> google categories and attach them to store categories.');
        }
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * saving feed settings
     */
    public function savefeedAction()
    {
        $result = array();
        try {
            $post = $this->getRequest()->getPost();
            $storeCode = Mage::helper('googlemerchants')->getStoreCodeFromPost();
            $storeCode = empty($storeCode) ? Mage::helper('googlemerchants')->getDefaultFrontendStoreView() : $storeCode;
            $valuesToSave = array();
            $names = $post['feed-col-name'];
            $values = $post['attribute-select'];
            $prefixes = $post['feed-pref-name'];
            $postfixes = $post['feed-postf-name'];
            foreach ($names as $key => $value) {
                $valuesToSave [$value] = array('value' => $values[$key], 'pref' => $prefixes[$key], 'postf' => $postfixes[$key]);
            }
            Mage::getModel('googlemerchants/googlefeed')->setFeedConfig($valuesToSave, $storeCode);
            $result['error'] = false;
        } catch (Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        } else {
            $this->_redirect('adminhtml/googlemerchants/feed/store/'.$storeCode);
            if(!$result['error'])
            {
                Mage::getSingleton('core/session')->addSuccess('Feed configuration has been saved.');
            }
        }
    }

    /**
     * resetting feed setting for selected store
     *
     */

    public function resetfeedAction()
    {
        $storeCode = Mage::helper('googlemerchants');
        Mage::getModel('googlemerchants/googlefeed')->setFeedConfig('', $storeCode);
        $this->_redirect('adminhtml/googlemerchants/feed');
    }

    /**
     * generate feed and put file to diresctory [base]/var/productsfeed/[filename] by default
     */

    public function generatefeedAction()
    {
        $model = Mage::getModel('googlemerchants/googlefeed');
        $model->generateFeed();
        $this->_redirect('adminhtml/googlemerchants/feed');

    }

    /**
     * loading feed dynamically
     */
    public function feedAjaxAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('googlemerchants/adminhtml_editfeed_edit_form')->toHtml());
    }

    /**
     * return url for clearing google cateogries
     */

    public function clearGoogleCategoriesAction()
    {
        Mage::getSingleton('adminhtml/session')->setLatestSelectedGoogleCategoryId(NULL);
        Mage::getSingleton('adminhtml/session')->setLatestSelectedStoreCategoryId(NULL);
        Mage::getModel('googlemerchants/googlecategory')->truncateCategoriesTable();
        $this->_redirect('adminhtml/googlemerchants/downloadtxt');
    }
}