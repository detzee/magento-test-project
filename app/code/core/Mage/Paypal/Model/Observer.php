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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * PayPal module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paypal_Model_Observer
{
    /**
     * Goes to reports.paypal.com and fetches Settlement reports.
     * @return Mage_Paypal_Model_Observer
     */
    public function fetchReports()
    {
        try {
            $reports = Mage::getModel('paypal/report_settlement');
            /* @var $reports Mage_Paypal_Model_Report_Settlement */
            $credentials = $reports->getSftpCredentials(true);
            foreach ($credentials as $config) {
                try {
                    $reports->fetchAndSave($config);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Clean unfinished transaction
     *
     * @return Mage_Paypal_Model_Observer
     */
    public function cleanTransactions()
    {
        /** @var $date Mage_Core_Model_Date */
        $date = Mage::getModel('core/date');
        $createdBefore = strtotime('-1 hour', $date->timestamp());

        /** @var $collection Mage_Paypal_Model_Resource_Payment_Transaction_Collection */
        $collection = Mage::getModel('paypal/payment_transaction')->getCollection();
        $collection->addCreatedBeforeFilter($date->gmtDate(null, $createdBefore));

        /** @var $method Mage_Paypal_Model_Payflowlink */
        $method = Mage::helper('payment')->getMethodInstance(Mage_Paypal_Model_Config::METHOD_PAYFLOWLINK);

        /** @var $item Mage_Paypal_Model_Payment_Transaction */
        foreach ($collection as $item) {
            try {
                $method->void(new Varien_Object(array(
                    'transaction_id' => $item->getTxnId(),
                    'store' => $item->getAdditionalInformation('store_id')
                )));
                $item->delete();
            } catch (Mage_Paypal_Exception $e) {
                $item->delete();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('hss_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
    public function setResponseAfterSaveOrder(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('hss_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && in_array($payment->getMethod(), Mage::helper('paypal/hss')->getHssMethods())) {
                /* @var $controller Mage_Core_Controller_Varien_Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = Mage::helper('core')->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY
                );

                if (empty($result['error'])) {
                    $controller->loadLayout('checkout_onepage_review');
                    $html = $controller->getLayout()->getBlock('paypal.iframe')->toHtml();
                    $result['update_section'] = array(
                        'name' => 'paypaliframe',
                        'html' => $html
                    );
                    $result['redirect'] = false;
                    $result['success'] = false;
                    $controller->getResponse()->clearHeader('Location');
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }

        return $this;
    }
}
