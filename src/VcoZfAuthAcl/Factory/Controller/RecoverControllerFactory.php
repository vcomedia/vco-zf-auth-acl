<?php
namespace VcoZfAuthAcl\Factory\Controller;

use VcoZfAuthAcl\Controller\RecoverController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use VcoZfAuthAcl\Form\Recover;
use VcoZfAuthAcl\Form\RecoverValidator;
use Zend\Mail\Message;

class RecoverControllerFactory implements FactoryInterface {

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
        $mailTransport = $realServiceLocator->get($config['VcoZfAuthAcl']['smtpTransportService']);
        
        $recoverForm = new Recover($config['VcoZfAuthAcl']);
        $recoverFormValidator = new RecoverValidator();
        
        $viewRenderer = $realServiceLocator->get('ViewRenderer');
        $translator = $realServiceLocator->get('MVCTranslator');

        $mailConfig = isset($config['mail']) ? $config['mail'] : null;
        return new RecoverController($authService, $userService, $mailTransport,$recoverForm, $recoverFormValidator, $viewRenderer, $translator, $config['VcoZfAuthAcl'], $mailConfig);
    }
}