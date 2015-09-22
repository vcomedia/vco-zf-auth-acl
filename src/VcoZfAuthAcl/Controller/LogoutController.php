<?php
namespace VcoZfAuthAcl\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\I18n\Translator;
 
class LogoutController extends AbstractActionController
{
    protected $authService;
        
    protected $translator;
    
    protected $config;
    
    public function __construct(AuthenticationService $authService, Translator $translator, array $config) 
    {
        $this->authService = $authService;
        $this->translator = $translator;
        $this->config = $config;
    }
    
    /**
     * @description logs a user out of the application
     * @return ViewModel
     */
    public function logoutAction()
    {
        $this->authService->clearIdentity();
 
        $this->flashmessenger()->addSuccessMessage($this->translator->translate($this->config['messages']['logoutSuccess']));
        return $this->redirect()->toRoute('login');
    }
}