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
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup  */

$installer->startSetup();

$customerTaxClassIds = $installer->getConnection()->fetchCol(
    "SELECT class_id FROM {$installer->getTable('tax_class')}
        WHERE class_type = 'CUSTOMER'
        ORDER BY class_id ASC"
);

if (count($customerTaxClassIds) > 0) {
    $installer->run(
        "UPDATE {$installer->getTable('customer_group')}
            SET tax_class_id = {$customerTaxClassIds[0]}
            WHERE tax_class_id NOT IN (".implode(',', $customerTaxClassIds).")"
    );
}

$installer->endSetup();
