<?php
namespace VcoZfAuthAcl\Form;
 
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
 
class Login extends Form
{
    protected $config;
    
    public function __construct($config)
    {
        $this->config = $config;
        
        parent::__construct('VcoZfAuthAcl\forms\login');
 
        $this->setAttributes(array('method' => 'post',));
 
        $this->add(
            array(
                'name' => 'identity',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'placeholder' => $this->config['messages']['identityFieldPlaceholder'],
                    'required' => 'required',
                ),
                'options' => array(
                    'label' => $this->config['messages']['identityFieldLabel'],
                    'label_attributes' => array(
                        'class' => 'control-label'
                    ),
                ),
            )
        );
 
        $this->add(
            array(
                'name' => 'password',
                'type' => 'Zend\Form\Element\Password',
                'attributes' => array(
                    'placeholder' => $this->config['messages']['passwordFieldPlaceholder'],
                    'required' => 'required',
                ),
                'options' => array(
                    'label' => $this->config['messages']['passwordFieldLabel'],
                    'label_attributes' => array(
                        'class' => 'control-label'
                    ),
                ),
            )
        );
 
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Zend\Form\Element\Submit',
                'attributes' => array(
                    'value' => $this->config['messages']['loginSubmitFieldLabel'],
                    'class' => 'button',
                    'id' => 'loginButton',
                ),
            )
        );
 
    }
}