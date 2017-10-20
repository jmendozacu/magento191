<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Relatedproducts
 * @version    1.4.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Relatedproducts_Model_Mysql4_Relatedproducts_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_table = Mage::getSingleton('core/resource')->getTableName('relatedproducts/relatedproducts');
        $this->_init('relatedproducts/relatedproducts');
    }

    public function addProductFilter($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        $this->addFieldToFilter('product_id', array('in' => $productIds));
        return $this;
    }

    public function addStoreFilter($storeId = null)
    {
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $this->addFieldToFilter('store_id', array('eq' => $storeId));
        return $this;
    }
}