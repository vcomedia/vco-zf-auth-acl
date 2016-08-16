<?php
namespace VcoZfAuthAcl\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;
 
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
        $request = $this->getRequest();
                
        $this->authService->clearIdentity();
        $logOutMessage = $this->translator->translate($this->config['messages']['logoutSuccess']);
        
        if($request->isXmlHttpRequest()) {
           $jsonResponse = new JsonModel(
                array(
            	    'message' => $logOutMessage,
                    'success'=>true,
                )
            );
            return $jsonResponse;
        } else {
            $this->flashmessenger()->addSuccessMessage($logOutMessage);
            return $this->redirect()->toRoute('login');
        }
    }
}