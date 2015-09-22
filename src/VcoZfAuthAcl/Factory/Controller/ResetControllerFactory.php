<?php
namespace VcoZfAuthAcl\Factory\Controller;

use VcoZfAuthAcl\Controller\ResetController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use VcoZfAuthAcl\Form\Reset;
use VcoZfAuthAcl\Form\ResetValidator;

class ResetControllerFactory implements FactoryInterface {

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
        $userService = $realServiceLocator->get($config['VcoZfAuthAcl']['userService']);
        
        $resetForm = new Reset($config['VcoZfAuthAcl']);
        $resetFormValidator = new ResetValidator();
        
        $translator = $realServiceLocator->get('MVCTranslator');
        $passwordService = $realServiceLocator->get('VcoZfAuthAcl\passwordService');
        
        return new ResetController($authService, $userService, $passwordService, $resetForm, $resetFormValidator, $translator, $config['VcoZfAuthAcl']);
    }
}