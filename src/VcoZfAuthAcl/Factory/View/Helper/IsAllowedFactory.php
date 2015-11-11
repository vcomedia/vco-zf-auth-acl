<?php
namespace VcoZfAuthAcl\Factory\View\Helper;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use VcoZfAuthAcl\View\Helper\IsAllowed;

class IsAllowedFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $sm = $serviceLocator->getServiceLocator();
        $config = $sm->get('Config');
        return new IsAllowed($config['VcoZfAuthAcl']);
    }
}