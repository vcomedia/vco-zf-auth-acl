<?php
namespace VcoZfAuthAcl\Factory\Controller;

use VcoZfAuthAcl\Controller\LoginController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use VcoZfAuthAcl\Form\Login;
use VcoZfAuthAcl\Form\LoginValidator;

class LoginControllerFactory implements FactoryInterface {

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
        
        $loginForm = new Login($config['VcoZfAuthAcl']);
        $loginFormValidator = new LoginValidator();
        
        $translator = $realServiceLocator->get('MVCTranslator');
        $authRateLimitService = !empty($config['authRateLimitService']) ? $realServiceLocator->get($config['authRateLimitService']) : null;
        

        return new LoginController($authService, $loginForm, $loginFormValidator, $translator, $config['VcoZfAuthAcl'], $authRateLimitService);
    }
}