<?php
/**
 * VcoZfAuthAcl - Zend Framework 2 auth and acl module.
 *
 * @category Module
 * @package  VcoZfAuthAcl
 * @author   Vahag Dudukgian (valeeum)
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     http://github.com/vcomedia/vco-zf-auth-acl/
 */

namespace VcoZfAuthAcl;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\EventManager\EventInterface;

/**
 * Class Module
 *
 * @see ConfigProviderInterface
 * @see ViewHelperProviderInterface
 * @package VcoZfAuthAcl
 */

class Module implements ConfigProviderInterface, ServiceProviderInterface, BootstrapListenerInterface, AutoloaderProviderInterface {

    
    public function onBootstrap (EventInterface $e) {
        /**
         * Log any Uncaught Exceptions, including all Exceptions in the stack
          */
        $sharedManager = $e->getApplication()
            ->getEventManager()
            ->getSharedManager();
        $sm = $e->getApplication()->getServiceManager();
        
        $config = $sm->get('Config');

    }    
     
    /**
     * @return array
     */
    public function getConfig () {
        return require __DIR__ . '/config/module.config.php';
    }

    //TODO: remove following method and autoload_classmap.php file
    public function getAutoloaderConfig () {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

     /** @return array */
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'VcoZfAuthAcl' => 'VcoZfAuthAcl\Factory\LoggerFactory'
            )
        );
    }
}
