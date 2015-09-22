<?php
namespace VcoZfAuthAcl\Form;
 
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\EmailAddress;
 
class RecoverValidator implements InputFilterAwareInterface 
{
    protected $inputFilter;
 
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
                        'name' => 'identity',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                            array('name' => 'StringToLower'),
                        ),
                        'validators' => array(
//                             array(
//                                 'name' => 'StringLength',
//                                 'options' => array(
//                                     'encoding' => 'UTF-8',
//                                     'min' => 1,
//                                     'max' => 100,
//                                 ),
//                             ),
                            new EmailAddress(),
                        ),
                    )
                )
            );
        }       
            
        return $this->inputFilter;    
    }
}