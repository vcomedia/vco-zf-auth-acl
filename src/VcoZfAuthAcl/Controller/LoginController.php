<?php
namespace VcoZfAuthAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Crypt\Password\PasswordInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\I18n\Translator;
use VcoZfAuthAcl\Service\AuthRateLimitServiceInterface;

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
                $authAdapter->setIdentity($identity);
                $authAdapter->setCredential($loginFormData['password']);
                
                $identity = $loginFormData['identity'];
                $identityParameter = $authAdapter->getOptions()->getIdentityParameter();
                
                //check if too many auth attempts
                if($this->authRateLimitService && $this->authRateLimitService->isAuthRateLimitExceeded($identity, $identityParameter)){
                    $this->flashMessenger()->addErrorMessage($this->translator->translate($this->config['messages']['loginFailedRateLimit']));
                } else {    //otherwise ok to attempt login          
                    $authenticationResult = $this->authService->authenticate();
                    
                    if ($authenticationResult->isValid()) {
                        //$identity = $authenticationResult->getIdentity();        
                        return $this->redirect()->toRoute($this->config['onLoginRedirectRouteName']);
                    } else {
                        //register failed auth attempt
                        $this->flashMessenger()->addErrorMessage($this->translator->translate($this->config['messages']['emailPasswordNoMatch']));
                        if($this->authRateLimitService) {
                            $this->authRateLimitService->regsiterFailedLogin($identity, $identityParameter);
                        }
                    }
                }
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