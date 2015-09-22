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

class LoginController extends AbstractActionController
{
    protected $authService;

    protected $loginForm;
    
    protected $loginFormValidator;
    
    protected $translator;
    
    protected $config;
    
    public function __construct(AuthenticationService $authService, FormInterface $loginForm, 
        InputFilterAwareInterface $loginFormValidator, Translator $translator, array $config) 
    {
        $this->authService = $authService;
        $this->loginForm = $loginForm;
        $this->loginFormValidator = $loginFormValidator;
        $this->translator = $translator;
        $this->config = $config;
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
                $this->loginFormData = $this->loginForm->getData();
                $authAdapter = $this->authService->getAdapter();
                $authAdapter->setIdentity($this->loginFormData['identity']);
                $authAdapter->setCredential($this->loginFormData['password']);
                $authenticationResult = $this->authService->authenticate();
                
                if ($authenticationResult->isValid()) {
                    //$identity = $authenticationResult->getIdentity();        
                    return $this->redirect()->toRoute($this->config['onLoginRedirectRouteName']);
                }
            }
             
            $this->flashMessenger()->addErrorMessage($this->translator->translate($this->config['messages']['emailPasswordNoMatch']));
        }
 
        $viewModel = new ViewModel(
            array(
                'form' => $this->loginForm,
                'errorMessages' =>  array_merge($this->flashMessenger()->getErrorMessages(), $this->flashMessenger()->getCurrentErrorMessages()),
                'successMessages' => array_merge($this->flashMessenger()->getSuccessMessages(), $this->flashMessenger()->getCurrentSuccessMessages()),
            )
        );
        
        $this->flashMessenger()->clearMessages('success');
        $this->flashMessenger()->clearMessages('error');
        
        if(empty($this->config['layoutName'])) {
            $viewModel->setTerminal(true);
        } else {
            $this->layout($this->config['layoutName']);
        }

        $viewModel->setTemplate($this->config['viewPath']['login']);
 
        return $viewModel;
    }
}