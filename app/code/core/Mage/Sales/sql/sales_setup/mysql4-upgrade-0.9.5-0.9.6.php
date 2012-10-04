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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->run("
ALTER TABLE `{$installer->getTable('sales_flat_quote_address_item')}` 
    ADD COLUMN `parent_item_id` INTEGER UNSIGNED AFTER `address_item_id`,
    ADD KEY `IDX_PARENT_ITEM_ID` (`parent_item_id`);
");

$installer->getConnection()->addConstraint(
    'SALES_FLAT_QUOTE_ADDRESS_ITEM_PARENT',
    $installer->getTable('sales_flat_quote_address_item'),
    'parent_item_id',
    $installer->getTable('sales_flat_quote_address_item'),
    'address_item_id'
);
