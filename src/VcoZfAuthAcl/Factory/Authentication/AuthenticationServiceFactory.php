<?php
namespace VcoZfAuthAcl\Factory\Authentication;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationServiceFactory implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator            
     *
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator) {
        $authService = $serviceLocator->get('doctrine.authenticationservice.odm_default');
        $passwordService = $serviceLocator->get('VcoZfAuthAcl\passwordService');
        
        //set credentialCallable to use passwordService
        $authAdapter = $authService->getAdapter();
        $authAdapter->getOptions()->credentialCallable = function($identity, $credentialValue) use ($authAdapter, $passwordService) {
            $credentialProperty = $authAdapter->getOptions()->getCredentialProperty();
            $getter             = 'get' . ucfirst($credentialProperty);
            $savedHash = null;
    
            if (method_exists($identity, $getter)) {
                $savedHash = $identity->$getter();
            } elseif (property_exists($identity, $credentialProperty)) {
                $savedHash = $identity->{$credentialProperty};
            } else {
                throw new \UnexpectedValueException(
                    sprintf(
                        'Property (%s) in (%s) is not accessible. You should implement %s::%s()',
                        $credentialProperty,
                        get_class($identity),
                        get_class($identity),
                        $getter
                    )
                );
            }
            return $passwordService->verify($credentialValue, $savedHash);
        };
        return $authService;
    }
}