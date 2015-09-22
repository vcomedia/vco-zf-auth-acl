<?php
namespace VcoZfAuthAcl\Factory\Controller;

use VcoZfAuthAcl\Controller\LogoutController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LogoutControllerFactory implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator            
     *
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator) {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $authService = $realServiceLocator->get('Zend\Authentication\AuthenticationService');
        $config = $realServiceLocator->get('Config');
        
        $translator = $realServiceLocator->get('MVCTranslator');
                
        return new LogoutController($authService, $translator, $config['VcoZfAuthAcl']);
    }
}