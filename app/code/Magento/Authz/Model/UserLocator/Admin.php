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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Authz\Model\UserLocator;

use Magento\Authz\Model\UserLocatorInterface;
use Magento\Authz\Model\UserIdentifier;
use Magento\Backend\Model\Auth\Session as AdminSession;

/**
 * Admin user locator.
 */
class Admin implements UserLocatorInterface
{
    /** @var AdminSession */
    protected $_adminSession;

    /**
     * Initialize dependencies.
     *
     * @param AdminSession $adminSession
     */
    public function __construct(AdminSession $adminSession)
    {
        $this->_adminSession = $adminSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->_adminSession->hasUser() ? (int)$this->_adminSession->getUser()->getId() : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserType()
    {
        return UserIdentifier::USER_TYPE_ADMIN;
    }
}
