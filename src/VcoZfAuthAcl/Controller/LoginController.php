<?php
namespace VcoZfAuthAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Mvc\I18n\Translator;
use VcoZfAuthAcl\Service\AuthRateLimitServiceInterface;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class LoginController extends AbstractActionController
{
    protected $authService;

    protected $loginForm;
    
    protected $loginFormValidator;
    
    protected $translator;
    
    protected $config;
    
    protected $authRateLimitService;
    
    public function __construct(AuthenticationService $authService, FormInterface $loginForm, 
        InputFilterAwareInterface $loginFormValidator, Translator $translator, array $config, AuthRateLimitServiceInterface $authRateLimitService = null) 
    {
        $this->authService = $authService;
        $this->loginForm = $loginForm;
        $this->loginFormValidator = $loginFormValidator;
        $this->translator = $translator;
        $this->config = $config;
        $this->authRateLimitService = $authRateLimitService;
    }
    
    /**
     * @description creates a login form and processes said form.
     * @return ViewModel
     */
    public function loginAction() 
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute($this->config['onLoginRedirectRouteName']);
        }
 
        $request = $this->getRequest();
 
        if ($request->isPost()) {
            $this->loginForm->setInputFilter($this->loginFormValidator->getInputFilter());
            $this->loginForm->setData($request->getPost());
 
            if ($this->loginForm->isValid()) {
                $loginFormData = $this->loginForm->getData();
                
                $authAdapter = $this->authService->getAdapter();
                
                $identity = $loginFormData['identity'];
                $identityProperty = $authAdapter->getOptions()->getIdentityProperty();
                
                //check if too many auth attempts
                if($this->authRateLimitService && $this->authRateLimitService->isAuthRateLimitExceeded($identity, $identityProperty)){
                    $errorRateLimitMessage = $this->translator->translate($this->config['messages']['loginFailedRateLimit']);
                    if($request->isXmlHttpRequest()) {
                        $jsonResponse = new JsonModel(
                            array(
                                'code' => 'error-rate-limit',
                        	    'message' => $errorRateLimitMessage,
                                'success'=>false,
                            )
                        );
                        return $jsonResponse;                        
                    } else {
                        $this->flashMessenger()->addErrorMessage($errorRateLimitMessage);
                    }
                } else {    //otherwise ok to attempt login          
                    $authAdapter->setIdentity($identity);
                    $authAdapter->setCredential($loginFormData['password']);
                    $authenticationResult = $this->authService->authenticate();
                    
                    if ($authenticationResult->isValid() && $request->isXmlHttpRequest()) { 
                        $jsonResponse = new JsonModel(
                            array(
                                'code' => 'login-success',
                        	    'message' => 'Successfully logged in.',
                                'success'=>true,
                            )
                        );
                        return $jsonResponse;
                    } else if ($authenticationResult->isValid()) { 
                        $container = new Container('VcoZfAuthAcl');
                        if($container->offsetExists('loginRedirectUrl')) {
                            $loginRedirectUrl = $container->offsetGet('loginRedirectUrl');
                            $container->offsetUnset('loginRedirectUrl');
                            return $this->redirect()->toUrl($loginRedirectUrl);
                        } else {
                            return $this->redirect()->toRoute($this->config['onLoginRedirectRouteName']);
                        }
                    } else {
                        //register failed auth attempt
                        if($this->authRateLimitService) {
                            $this->authRateLimitService->regsiterFailedLogin($identity, $identityProperty);
                        }
                        $emailPasswordNoMatchMessage = $this->translator->translate($this->config['messages']['emailPasswordNoMatch']);
                        if($request->isXmlHttpRequest()) {
                            $jsonResponse = new JsonModel(
                                array(
                                    'code' => 'email-password-nomatch',
                            	    'message' => $emailPasswordNoMatchMessage,
                                    'success'=>false,
                                )
                            );
                            return $jsonResponse;                               
                        } else {
                            $this->flashMessenger()->addErrorMessage($emailPasswordNoMatchMessage);
                        }
                    }
                }
            } else if($request->isXmlHttpRequest()) {  //invalid input
                $messages = $this->loginForm->getMessages();
                $jsonResponse = new JsonModel(
                    array(
                        'code' => 'invalid-input',
                	    'message' => $messages,
                        'success'=>false,
                    )
                );
                return $jsonResponse; 
            }
        }
 
        $viewModel = new ViewModel(
            array(
                'form' => $this->loginForm
            )
        );
        
        if(empty($this->config['layoutName'])) {
            $viewModel->setTerminal(true);
        } else {
            $this->layout($this->config['layoutName']);
        }

        $viewModel->setTemplate($this->config['viewPath']['login']);
 
        return $viewModel;
    }
}