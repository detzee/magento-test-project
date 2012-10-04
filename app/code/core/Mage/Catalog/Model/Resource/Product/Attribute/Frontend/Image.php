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
 * Product image attribute frontend
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Attribute_Frontend_Image
    extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    const IMAGE_PATH_SEGMENT = 'catalog/product/';

    /**
     * Retreive image url
     *
     * @param Varien_Object $object
     * @return string|false
     */
    public function getUrl($object)
    {
        $url   = false;
        $image = $object->getData($this->getAttribute()->getAttributeCode());
        if ($image) {
            $url = Mage::getBaseUrl('media') . self::IMAGE_PATH_SEGMENT . $image;
        }
        return $url;
    }
}
