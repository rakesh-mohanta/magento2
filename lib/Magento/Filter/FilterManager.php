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
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Filter;

/**
 * Magento Filter Manager
 */
class FilterManager
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $filterInstances = array();

    /**
     * @var FilterManager\Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $factoryInstances;

    /**
     * @param \Magento\ObjectManager $objectManger
     * @param FilterManager\Config $config
     */
    public function __construct(
        \Magento\ObjectManager $objectManger,
        FilterManager\Config $config
    ) {
        $this->objectManager = $objectManger;
        $this->config = $config;
    }

    /**
     * Get filter object
     *
     * @param string $filterAlias
     * @param array $arguments
     * @return \Zend_Filter_Interface
     * @throws \UnexpectedValueException
     */
    public function get($filterAlias, array $arguments = array())
    {
        $filter = $this->createFilterInstance($filterAlias, $arguments);
        if (!$filter instanceof \Zend_Filter_Interface) {
            throw new \UnexpectedValueException('Filter object must implement Zend_Filter_Interface interface, '
                . get_class($filter) . ' was given.');
        }
        return $filter;
    }

    /**
     * Create filter instance
     *
     * @param string $filterAlias
     * @param array $arguments
     * @return \Zend_Filter_Interface
     * @throws \InvalidArgumentException
     */
    protected function createFilterInstance($filterAlias, $arguments)
    {
        /** @var FactoryInterface $factory */
        foreach ($this->getFilterFactories() as $factory) {
            if ($factory->canCreateFilter($filterAlias)) {
                return $factory->createFilter($filterAlias, $arguments);
            }
        }
        throw new \InvalidArgumentException('Filter was not found by given alias ' . $filterAlias);
    }

    /**
     * Get registered factories
     *
     * @return FactoryInterface[]
     * @throws \UnexpectedValueException
     */
    protected function getFilterFactories()
    {
        if (null === $this->factoryInstances) {
            foreach ($this->config->getFactories() as $class) {
                $factory = $this->objectManager->create($class);
                if (!$factory instanceof FactoryInterface) {
                    throw new \UnexpectedValueException(
                        'Filter factory must implement FilterFactoryInterface interface, '
                            . get_class($factory) . ' was given.'
                    );
                }
                $this->factoryInstances[] = $factory;
            }
        }
        return $this->factoryInstances;
    }

    /**
     * Create filter and filer value
     *
     * @param string $filterAlias
     * @param array $arguments
     * @return mixed
     */
    public function __call($filterAlias, array $arguments = array())
    {
        $value = array_shift($arguments);
        if (count($arguments)) {
            $arguments = array_shift($arguments);
            if (!is_array($arguments)) {
                $arguments = array($arguments);
            }
        }
        return $this->createFilterInstance($filterAlias, $arguments)->filter($value);
    }
}
