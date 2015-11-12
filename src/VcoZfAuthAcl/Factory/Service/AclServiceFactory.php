<?php
namespace VcoZfAuthAcl\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use VcoZfAuthAcl\Service\AclService;
use Zend\Permissions\Acl\Acl;

class ProductServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $acl = new Acl();
        return new AclService($acl);
    }
}