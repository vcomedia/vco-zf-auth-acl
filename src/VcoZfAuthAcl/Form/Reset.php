<?php
namespace VcoZfAuthAcl\Form;
 
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
 
class Reset extends Form
{
    protected $config;
    
    public function __construct($config)
    {
        $this->config = $config;
        
        parent::__construct('VcoZfAuthAcl\forms\reset');
 
        $this->setAttributes(array('method' => 'post',));
 
        $this->add(
            array(
                'name' => 'password',
                'type' => 'Zend\Form\Element\Password',
                'attributes' => array(
                    'placeholder' => $this->config['messages']['resetPasswordFieldPlaceholder'],
                    'required' => 'required',
                ),
                'options' => array(
                    'label' => $this->config['messages']['resetPasswordFieldLabel'],
                    'label_attributes' => array(
                        'class' => 'control-label'
                    ),
                ),
            )
        );
        
        $this->add(
            array(
                'name' => 'confirm',
                'type' => 'Zend\Form\Element\Password',
                'attributes' => array(
                    'placeholder' => $this->config['messages']['resetConfirmPasswordFieldPlaceholder'],
                    'required' => 'required',
                ),
                'options' => array(
                    'label' => $this->config['messages']['resetConfirmPasswordFieldLabel'],
                    'label_attributes' => array(
                        'class' => 'control-label'
                    ),
                )
            )
        );        
        
        
        //resetConfirmPasswordFieldLabel
 
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Zend\Form\Element\Submit',
                'attributes' => array(
                    'value' => $this->config['messages']['resetSubmitFieldLabel'],
                    'class' => 'button',
                    'id' => 'resetButton',
                ),
            )
        );
 
    }
}