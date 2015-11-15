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
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Assertion\AssertionAggregate;
use Zend\Mvc\Application;
use Zend\Session\Container;

/**
 * Class Module
 *
 * @see ConfigProviderInterface
 * @see ViewHelperProviderInterface
 * @package VcoZfAuthAcl
 */

class Module implements ConfigProviderInterface, BootstrapListenerInterface, AutoloaderProviderInterface {

    public function onBootstrap (EventInterface $e) {
        $app = $e->getApplication();
        $eventManager = $app->getEventManager();
        $serviceManager = $app->getServiceManager();
        $config = $serviceManager->get('Config');
        
        if($config['VcoZfAuthAcl']['enabled'] == true) {
            $this->initAcl($e, $config['VcoZfAuthAcl']);
            $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAcl')); 
            $strategy = $serviceManager->get($config['VcoZfAuthAcl']['unauthorizedStrategy']);
            $app->getEventManager()->attach($strategy);
        }
    }
    
    /**
     * @description Initialise ACL for all modules/controllers/actions
     * @param MvcEvent $e
     */
    public function initAcl(MvcEvent $e, $config)
    {
        $app = $e->getApplication();
        $eventManager = $app->getEventManager();
        $serviceManager = $app->getServiceManager();
        
        $aclService = $serviceManager->get('VcoZfAuthAcl\Service\AclServiceInterface');
        $acl = $aclService->getAcl();
        /* @var $acl Acl */
        //deny everything by default
        $acl->deny();
        
        //add roles
        $roles = $config['acl']['roles']; 
        
        if(count($roles) > 0) {
            foreach($roles as $roleName => $roleParent) {
                if($roleName == '') {
                    throw new \Exception('Role name can not be empty');
                }
                $role = new GenericRole($roleName);
                $acl->addRole($role, $roleParent);
            }
        }
        //add resources
        $resources = $config['acl']['resources'];
        if(count($resources) > 0) {
            foreach($resources as $moduleName => $moduleResources) {
                $moduleName = strtolower($moduleName);
                if(!$acl->hasResource($moduleName)) {
                    $acl->addResource(new GenericResource($moduleName));
                }
                if(count($moduleResources) > 0) {
                    foreach($moduleResources as $moduleResource) {
                        $moduleResource = strtolower($moduleResource);
                        if(!$acl->hasResource($moduleResource)) {
                            $acl->addResource(new GenericResource($moduleResource), $moduleName);
                        }    
                    }
                }
            }
        }
        
        //allows
        $allows = $config['acl']['allow'];
        if(count($allows) > 0) {
            foreach($allows as $allow) {
                $assertionsConfig = $allow['assertions'];
                $assertion = null;
                if(is_array($assertionsConfig) && count($assertionsConfig) > 0) {
                    $assertion = new AssertionAggregate();
                    foreach($assertionsConfig as $assertClassName) {
                        $assertion->addAssertion(new $assertClassName());
                    }
                } else if(is_string($assertionsConfig) && !empty($assertionsConfig)) {
                    $assertion = new $assertionsConfig();
                }
                $acl->allow($allow['roles'], $allow['resources'], $allow['privileges'], $assertion);
            }
        }
                
        //denials
        $denials = $config['acl']['deny'];
        if(count($denials) > 0) {
            foreach($denials as $denial) {
                $assertionsConfig = $denial['assertions'];
                $assertion = null;
                if(is_array($assertionsConfig) && count($assertionsConfig) > 0) {
                    $assertion = new AssertionAggregate();
                    foreach($assertionsConfig as $assertClassName) {
                        $assertion->addAssertion(new $assertClassName());
                    }
                } else if(is_string($assertionsConfig) && !empty($assertionsConfig)) {
                    $assertion = new $assertionsConfig();
                }
                $acl->deny($denial['roles'], $denial['resources'], $denial['privileges'], $assertion);
            }
        }
    }

    /**
     * @description Check User has the correct permissions to view this page
     * @param MvcEvent $e
     */
    public function checkAcl(MvcEvent $e)
    {
        $app = $e->getApplication();
        $em = $app->getEventManager();
        $serviceManager = $app->getServiceManager();
        $matches = $e->getRouteMatch();
        $controllerParams = explode("\\", $matches->getParam('controller'));
        if(count($controllerParams) < 3) {
            throw new \Exception("Namespace missing from route.");
        }
        $moduleName = $controllerParams[0];
        $controllerName = $controllerParams[2];
        $actionName = $matches->getParam('action');
        $resourceName = strtolower("$moduleName:$controllerName"); 
     
        $authService = $serviceManager->get('Zend\Authentication\AuthenticationService');
        $config = $serviceManager->get('Config');
        
        $userRole = $config['VcoZfAuthAcl']['acl']['defaultRole'];
        
        if($authService->hasIdentity() && $userObject = $authService->getStorage()->read()) {
            $roleProperty = $config['VcoZfAuthAcl']['acl']['roleProperty'];
            $getter             = 'get' . ucfirst($roleProperty);
            //$userRole = $authService->getStorage()->read()->getRole();
            if (method_exists($userObject, $getter)) {
                $userRole = $userObject->$getter();
            } elseif (property_exists($userObject, $roleProperty)) {
                $userRole = $userObject->{$roleProperty};
            } else {
                throw new \UnexpectedValueException(
                    sprintf(
                        'Property (%s) in (%s) is not accessible. You should implement %s::%s()',
                        $roleProperty,
                        get_class($userObject),
                        get_class($userObject),
                        $getter
                    )
                );
            }
        } else if ($authService->hasIdentity()) {  //user was disabled/deleted, log them out
            $authService->clearIdentity();
        }
        
        $aclService = $serviceManager->get('VcoZfAuthAcl\Service\AclServiceInterface');
        $acl = $aclService->getAcl();
     
        if (!$acl->isAllowed($userRole, $resourceName, $actionName)) {
             $response = $e->getResponse();
             $request = $e->getRequest();
             $router = $e->getRouter();
             if (!$authService->hasIdentity()) {
                if(!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
                    $container = new Container('VcoZfAuthAcl');
                    $redirectUrl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                    $redirectUrl .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    $container->offsetSet('loginRedirectUrl', $redirectUrl);
                }
                $response->getHeaders()->addHeaderLine('Auth-Required', 1);
                $url = $router->assemble(array(), array('name' => 'login'));
                
                if($request->isXmlHttpRequest()) {  
                    //only trigger error response code for ajax request. do not forward to login page. 
                	$response->setStatusCode(500);
                } else {
                    $response->getHeaders()->addHeaderLine('Location', $url);
                    $response->setStatusCode(302);
                }
                
                //use the following instead of exit() to preserve unit testing and immediately end script
                $stopCallBack = function($event) use ($response){
                    $event->stopPropagation();
                    return $response;
                };
                $em->attach(MvcEvent::EVENT_ROUTE, $stopCallBack,-10000);
                return $response;
            } else {
                $e->setError(Application::ERROR_EXCEPTION);
                $e->setParam('identity', $authService->getIdentity());
                $e->setParam('controller', $controllerName);
                $e->setParam('action', $actionName);
        
                $errorMessage = sprintf("You are not authorized to access %s:%s:%s", $moduleName, $controllerName, $actionName);
                $e->setParam('exception', new \VcoZfAuthAcl\Exception\UnAuthorizedException($errorMessage));
                $e->getTarget()->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getConfig () {
        return require __DIR__ . '/config/module.config.php';
    }
    
    // TODO: remove following method and autoload_classmap.php file
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
}
