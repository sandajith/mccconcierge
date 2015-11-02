<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency model
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mycloset_Membership_Model_Currency extends Mage_Directory_Model_Currency
{
    
    public function formatPrecision($price, $precision, $options = array(), $includeContainer = true,
                                    $addBrackets = false)
    {
        if (!isset($options['precision'])) {
            $options['precision'] = $precision;
        }
        if ($includeContainer) {
            return '<span class="price" >' . ($addBrackets ? '[' : '') . $this->formatTxt($price, $options) .
                ($addBrackets ? ']' : '') . '</span><br>';
        }
        return $this->formatTxt($price, $options);
    }

    /**
     * Returns the formatted price
     *
     * @param float $price
     * @param null|array $options
     * @return string
     */
    public function formatTxt($price, $options = array())
    {
        if (!is_numeric($price)) {
            $price = Mage::app()->getLocale()->getNumber($price);
        }
        /**
         * Fix problem with 12 000 000, 1 200 000
         *
         * %f - the argument is treated as a float, and presented as a floating-point number (locale aware).
         * %F - the argument is treated as a float, and presented as a floating-point number (non-locale aware).
         */
        $price = sprintf("%F", $price);
        if ($price == -0) {
            $price = 0;
        }
        return Mage::app()->getLocale()->currency($this->getCode())->toCurrency($price, $options);
    }

}
