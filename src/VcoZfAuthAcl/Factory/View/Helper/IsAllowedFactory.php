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
        $authService = $sm->get('Zend\Authentication\AuthenticationService');
        $aclService = $sm->get('VcoZfAuthAcl\Service\AclServiceInterface');
        return new IsAllowed($authService, $aclService->getAcl(), $config['VcoZfAuthAcl']);
    }
}