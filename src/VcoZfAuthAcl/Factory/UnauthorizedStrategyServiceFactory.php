<?php

namespace VcoZfAuthAcl\Factory;

use VcoZfAuthAcl\View\UnauthorizedStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \VcoZfAuthAcl\View\UnauthorizedStrategy}
 *
 */
class UnauthorizedStrategyServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \VcoZfAuthAcl\View\UnauthorizedStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        return new UnauthorizedStrategy($config['VcoZfAuthAcl']['unauthorizedTemplate']);
    }
}
