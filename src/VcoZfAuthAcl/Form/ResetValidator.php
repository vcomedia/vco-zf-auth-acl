<?php
namespace VcoZfAuthAcl\Form;
 
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
 
class ResetValidator implements InputFilterAwareInterface 
{
    protected $inputFilter;
    protected $passwordStrengthOptions;
    
    public function __construct($passwordStrengthOptions) {
        $this->passwordStrengthOptions = $passwordStrengthOptions;
    }
 
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }
 
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new InputFilter();
            $factory = new InputFactory();
 
            $this->inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'password',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => '\VcoZfAuthAcl\Validator\PasswordStrength',
                                'options' => $this->passwordStrengthOptions,
                            ),
                        ),
                    )
                )
            );
            
            $this->inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'confirm',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Identical',
                                'options' => array(
                                    'token' => 'password',
                                ),
                            ),
                        )
                    )
                )
            );
        }       
            
        return $this->inputFilter;    
    }
}