<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Catalog Observer
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Observer
{
    /**
     * Process catalog ata related with store data changes
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Catalog_Model_Observer
     */
    public function storeEdit(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */
        if ($store->dataHasChangedFor('group_id')) {
            Mage::app()->reinitStores();
            /**
             * @see Mage_Catalog_Model_Indexer_Url
             */
            //Mage::getModel('catalog/url')->refreshRewrites($store->getId());

            /**
             * @see Mage_Catalog_Model_Category_Indexer_Product
             */
            /*Mage::getResourceModel('catalog/category')->refreshProductIndex(
                array(),
                array(),
                array($store->getId())
            );*/
            if (Mage::helper('catalog/category_flat')->isEnabled(true)) {
                Mage::getResourceModel('catalog/category_flat')->synchronize(null, array($store->getId()));
            }
            Mage::getResourceSingleton('catalog/product')->refreshEnabledIndex($store);
        }
        return $this;
    }

    /**
     * Process catalog data related with new store
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Catalog_Model_Observer
     */
    public function storeAdd(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */
        Mage::app()->reinitStores();
        Mage::getConfig()->reinit();

        /**
         * @see Mage_Catalog_Model_Indexer_Url
         */
        //Mage::getModel('catalog/url')->refreshRewrites($store->getId());
        /**
         * @see Mage_Catalog_Model_Category_Indexer_Product
         */
        /*Mage::getResourceSingleton('catalog/category')->refreshProductIndex(
            array(),
            array(),
            array($store->getId())
        );*/
        if (Mage::helper('catalog/category_flat')->isEnabled(true)) {
            Mage::getResourceModel('catalog/category_flat')
                ->synchronize(null, array($store->getId()));
        }
        Mage::getResourceModel('catalog/product')->refreshEnabledIndex($store);
        return $this;
    }

    /**
     * Process catalog data related with store group root category
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Catalog_Model_Observer
     */
    public function storeGroupSave(Varien_Event_Observer $observer)
    {
        $group = $observer->getEvent()->getGroup();
        /* @var $group Mage_Core_Model_Store_Group */
        if ($group->dataHasChangedFor('root_category_id') || $group->dataHasChangedFor('website_id')) {
            Mage::app()->reinitStores();
            foreach ($group->getStores() as $store) {
                /**
                 * @see Mage_Catalog_Model_Indexer_Url
                 */
                //Mage::getModel('catalog/url')->refreshRewrites($store->getId());
                /**
                 * @see Mage_Catalog_Model_Category_Indexer_Product
                 */
                /*Mage::getResourceSingleton('catalog/category')->refreshProductIndex(
                    array(),
                    array(),
                    array($store->getId())
                );*/
                if (Mage::helper('catalog/category_flat')->isEnabled(true)) {
                    Mage::getResourceModel('catalog/category_flat')
                        ->synchronize(null, array($store->getId()));
                }
            }
        }
        return $this;
    }

    /**
     * Process delete of store
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Catalog_Model_Observer
     */
    public function storeDelete(Varien_Event_Observer $observer)
    {
        if (Mage::helper('catalog/category_flat')->isEnabled(true)) {
            $store = $observer->getEvent()->getStore();
            Mage::getResourceModel('catalog/category_flat')->deleteStores($store->getId());
        }
        return $this;
    }

    /**
     * Process catalog data after category move
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Catalog_Model_Observer
     */
    public function categoryMove(Varien_Event_Observer $observer)
    {
        $categoryId = $observer->getEvent()->getCategoryId();
        $prevParentId = $observer->getEvent()->getPrevParentId();
        $parentId = $observer->getEvent()->getParentId();
        /**
         * @see Mage_Catalog_Model_Indexer_Url
         */
        //Mage::getModel('catalog/url')->refreshCategoryRewrite($categoryId);
        /**
         * @see Mage_Catalog_Model_Category_Indexer_Product
         */
        /*Mage::getResourceSingleton('catalog/category')->refreshProductIndex(array(
            $categoryId, $prevParentId, $parentId
        ));*/
        //Mage::getModel('catalog/category')->load($prevParentId)->save();
        //Mage::getModel('catalog/category')->load($parentId)->save();
        if (Mage::helper('catalog/category_flat')->isEnabled(true)) {
            Mage::getResourceModel('catalog/category_flat')
                ->move($categoryId, $prevParentId, $parentId);
        }
        return $this;
    }

    /**
     * Process catalog data after products import
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Catalog_Model_Observer
     */
    public function catalogProductImportAfter(Varien_Event_Observer $observer)
    {
        Mage::getModel('catalog/url')->refreshRewrites();
        Mage::getResourceSingleton('catalog/category')->refreshProductIndex();
        return $this;
    }

    /**
     * Catalog Product Compare Items Clean
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Catalog_Model_Observer
     */
    public function catalogProductCompareClean(Varien_Event_Observer $observer)
    {
        Mage::getModel('catalog/product_compare_item')->clean();
        return $this;
    }

    /**
     * After save event of category
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Catalog_Model_Observer
     */
    public function categorySaveAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('catalog/category_flat')->isEnabled(true)) {
            $category = $observer->getEvent()->getCategory();
            Mage::getResourceModel('catalog/category_flat')->synchronize($category);
        }
        return $this;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Catalog_Model_Observer
     */
    public function catalogCheckIsUsingStaticUrlsAllowed(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getData('store_id');
        $result  = $observer->getEvent()->getData('result');
        $result->isAllowed = Mage::helper('catalog')->setStoreId($storeId)->isUsingStaticUrlsAllowed();
    }
}
