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
 * @package     Mage_CatalogRule
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$tableName = $installer->getTable('catalogrule/rule');
$columnOptions = array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'UNSIGNED'  => true,
    'NULLABLE'  => false,
    'DEFAULT'   => 0,
    'COMMENT'   => 'Is Rule Enable For Subitems',
);
$installer->getConnection()->addColumn($tableName, 'sub_is_enable', $columnOptions);

$columnOptions = array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'LENGTH'    => 32,
    'COMMENT'   => 'Simple Action For Subitems',
);
$installer->getConnection()->addColumn($tableName, 'sub_simple_action', $columnOptions);

$columnOptions = array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'SCALE'     => 4,
    'PRECISION' => 12,
    'NULLABLE'  => false,
    'DEFAULT'   => '0.0000',
    'COMMENT'   => 'Discount Amount For Subitems',
);
$installer->getConnection()->addColumn($tableName, 'sub_discount_amount', $columnOptions);
