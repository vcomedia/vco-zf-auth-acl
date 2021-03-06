<?php
namespace VcoZfAuthAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use VcoZfAuthAcl\Service\UserServiceInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Message;
use Zend\Mime\Part;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class RecoverController extends AbstractActionController
{
    protected $authService;
    
    protected $userService;
    
    protected $mailTransport;

    protected $recoverForm;
    
    protected $recoverFormValidator;
    
    protected $viewRenderer;
    
    protected $translator;
    
    protected $config;
    
    protected $mailConfig;
    
    //TODO: $userService class needs to live within module
    public function __construct(AuthenticationServiceInterface $authService, UserServiceInterface $userService, TransportInterface $mailTransport,FormInterface $recoverForm, 
        InputFilterAwareInterface $recoverFormValidator, PhpRenderer $viewRenderer, Translator $translator, array $config, $mailConfig) 
    {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->mailTransport = $mailTransport;
        $this->recoverForm = $recoverForm;
        $this->recoverFormValidator = $recoverFormValidator;
        $this->viewRenderer = $viewRenderer;
        $this->translator = $translator;
        $this->config = $config;
        $this->mailConfig = $mailConfig;
    }
    
    /**
     * @description creates a recover form and processes said form.
     * @return ViewModel
     */
    public function recoverAction() 
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute($this->config['onLoginRedirectRouteName']);
        }
 
        $request = $this->getRequest();
 
        if ($request->isPost()) {
            $this->recoverForm->setInputFilter($this->recoverFormValidator->getInputFilter());
            $this->recoverForm->setData($request->getPost());
 
            if ($this->recoverForm->isValid()) {
                $recoverFormData = $this->recoverForm->getData();
                $identityProperty = $this->authService->getAdapter()->getOptions()->getIdentityProperty();
                $roleProperty = $this->config['acl']['roleProperty'];
                $userObject = $this->userService->setPasswordReset($recoverFormData['identity'], $identityProperty);
                
                if($userObject) {
                    $getter = 'get' . ucfirst($this->config['userEmailAddressProperty']);
                    if (method_exists($userObject, $getter)) {
                        $emailAddress = $userObject->$getter();
                    } elseif (property_exists($userObject, $roleProperty)) {
                        $emailAddress = $userObject->{$identityProperty};
                    } else {
                        throw new \UnexpectedValueException(
                            sprintf(
                                'Property (%s) in (%s) is not accessible. You should implement %s::%s()',
                                $identityProperty,
                                get_class($userObject),
                                get_class($userObject),
                                $getter
                            )
                        );
                    }
                
                    $emailView = new ViewModel();
                    $emailView->setTemplate($this->config['viewPath']['emailTemplate'])
                        ->setVariables(array(
                            'user' => $userObject, 
                            'identityProperty' => $identityProperty, 
                            'identityValue' => $recoverFormData['identity'],
                            'email' => $emailAddress,
                            'config' => $this->config
                        ));
                    $emailContent = $this->viewRenderer->render($emailView);

                    if(!empty($this->config['viewPath']['emailLayout'])) {
                        $viewLayout = new \Zend\View\Model\ViewModel();
                        $viewLayout->setTemplate($this->config['viewPath']['emailLayout'])
                             ->setVariables(array(
                                    'content' => $emailContent
                        ));
                        $emailContent = $this->viewRenderer->render($viewLayout);
                    }

                    $html = new Part($emailContent);
                    $html->type = "text/html";
                    
                    $body = new \Zend\Mime\Message();
                    $body->setParts(array($html));
                    
                    $message = new Message();  
                    $message->setSubject($this->translator->translate($this->config['messages']['passwordResetEmailSubject']));
                    
                    $fromEmail = isset($this->mailConfig['defaultFrom']) ? $this->mailConfig['defaultFrom'] : $this->mailTransport->getOptions()->getConnectionConfig()['username'];
                    
                    $message->addFrom($fromEmail);
                    $message->addTo($emailAddress);
                    $message->setBody($body);
                    
                    $this->mailTransport->send($message);
                }
            }
            $successMessage = $this->translator->translate($this->config['messages']['recoverSubmitSuccess']);
            if($request->isXmlHttpRequest()) {
               $jsonResponse = new JsonModel(
                    array(
                        'code' => 'recover-success',
                	    'message' => $successMessage,
                        'success'=>true,
                    )
                );
                return $jsonResponse;                
            } else {
                $this->flashMessenger()->addSuccessMessage($successMessage);
                return $this->redirect()->toRoute('recover');
            }
        }
 
        $viewModel = new ViewModel(
            array(
                'form' => $this->recoverForm
            )
        );
        
        if(empty($this->config['layoutName'])) {
            $viewModel->setTerminal(true);
        } else {
            $this->layout($this->config['layoutName']);
        }

        $viewModel->setTemplate($this->config['viewPath']['recover']);  //'storefront/index/recover.phtml'
 
        return $viewModel;
    }
}