<?php
namespace VcoZfAuthAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Mvc\I18n\Translator;
use VcoZfAuthAcl\Service\UserServiceInterface;
use Zend\Crypt\Password\PasswordInterface;

class ResetController extends AbstractActionController
{
    protected $authService;
    
    protected $userService;
    
    protected $passwordService;
    
    protected $resetForm;
    
    protected $resetFormValidator;
    
    protected $translator;
    
    protected $config;
    
    public function __construct(AuthenticationService $authService, UserServiceInterface $userService, PasswordInterface $passwordService, FormInterface $resetForm, 
        InputFilterAwareInterface $resetFormValidator, Translator $translator, array $config) 
    {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->passwordService = $passwordService;
        $this->resetForm = $resetForm;
        $this->resetFormValidator = $resetFormValidator;
        $this->translator = $translator;
        $this->config = $config;
    }
    
    /**
     * @description creates a reset form and processes said form.
     * @return ViewModel
     */
    public function resetAction() 
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute($this->config['onLoginRedirectRouteName']);
        }
 
        //check if guid invalid
        $id = $this->params('id');
        $user = $this->userService->getUserByGuid($id);

        //TODO: create abstract embedded document with magic methods
        if(!$user || $user->passwordReset->getDateCreated()->diff(new \DateTime())->i > 60) { //link valid for one hour
            $this->flashMessenger()->addErrorMessage($this->translator->translate($this->config['messages']['resetGuidInvalid']));
            return $this->redirect()->toRoute('recover');
        }
        
        $request = $this->getRequest();
 
        if ($request->isPost()) {
            $this->resetForm->setInputFilter($this->resetFormValidator->getInputFilter());
            $this->resetForm->setData($request->getPost());
 
            if ($this->resetForm->isValid()) {
                $this->resetFormData = $this->resetForm->getData();
                $user->setPassword($this->passwordService->create($this->resetFormData['password']));
                $user->setPasswordReset(null);
                $this->userService->saveUser($user);
                
                $this->flashMessenger()->addSuccessMessage($this->translator->translate($this->config['messages']['passwordUpdateSuccess']));
                return $this->redirect()->toRoute('login');
            }
             
            $this->flashMessenger()->addErrorMessage($this->translator->translate($this->config['messages']['passwordConfirmNoMatch']));
        }
 
        $viewModel = new ViewModel(
            array(
                'form' => $this->resetForm,
                'id' => $id,
                'errorMessages' =>  array_merge($this->flashMessenger()->getErrorMessages(), $this->flashMessenger()->getCurrentErrorMessages()),
                'successMessages' => array_merge($this->flashMessenger()->getSuccessMessages(), $this->flashMessenger()->getCurrentSuccessMessages()),
            )
        );
        
        if(empty($this->config['layoutName'])) {
            $viewModel->setTerminal(true);
        } else {
            $this->layout($this->config['layoutName']);
        }

        $viewModel->setTemplate($this->config['viewPath']['reset']);  //'storefront/index/reset.phtml'
 
        return $viewModel;
    }
}