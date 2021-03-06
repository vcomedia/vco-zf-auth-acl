<?php
namespace VcoZfAuthAcl\Validator;

use Zend\Validator\AbstractValidator;

class PasswordStrength extends AbstractValidator
{   
    protected $messageTemplates;
    
    const LENGTH = 'length';
    const UPPER  = 'upper';
    const LOWER  = 'lower';
    const DIGIT  = 'digit';
    const SPECIAL  = 'special';
    const DEFAULT_SPECIAL_CHARS = ' !\"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~';
    
    const OPTION_MIN_LENGTH = 'minLength';
    const OPTION_REQUIRE_UPPER = 'requireUpper';
    const OPTION_REQUIRE_LOWER = 'requireLower';
    const OPTION_REQUIRE_DIGIT = 'requireDigit';
    const OPTION_REQUIRE_SPECIAL_CHARACTERS = 'requireSpecialCharacters';
    const OPTION_SPECIAL_CHARACTERS = 'specialCharacters';

    public function __construct($options = null)
    {
        parent::__construct($options);
        $options = $this->getOptions();
        //set default options if non set
        if(!isset($options[self::OPTION_MIN_LENGTH])) {
            $options[self::OPTION_MIN_LENGTH] = 8;
        }
        if(!isset($options[self::OPTION_REQUIRE_UPPER])) {
            $options[self::OPTION_REQUIRE_UPPER] = true;
        }
        if(!isset($options[self::OPTION_REQUIRE_LOWER])) {
            $options[self::OPTION_REQUIRE_LOWER] = true;
        }
        if(!isset($options[self::OPTION_REQUIRE_DIGIT])) {
            $options[self::OPTION_REQUIRE_DIGIT] = true;
        }
        if(!isset($options[self::OPTION_REQUIRE_SPECIAL_CHARACTERS])) {
            $options[self::OPTION_REQUIRE_SPECIAL_CHARACTERS] = true;
        }
        if(!isset($options[self::OPTION_SPECIAL_CHARACTERS])) {
            $options[self::OPTION_SPECIAL_CHARACTERS] = self::DEFAULT_SPECIAL_CHARS;
        }
        
        $minLength = $this->getOption(self::OPTION_MIN_LENGTH);       
        $this->messageTemplates = array(
            self::LENGTH => "Password must be at least $minLength characters in length",
            self::UPPER  => "Password must contain at least one uppercase letter",
            self::LOWER  => "Password must contain at least one lowercase letter",
            self::DIGIT  => "Password must contain at least one digit character",
            self::SPECIAL  => "Password must contain at least one special character"
        );     
    }
    
    public function isValid($value)
    {
        $this->setValue($value);
        $isValid = true;
        
        $minLength = $this->getOption(self::OPTION_MIN_LENGTH);
        if (is_int($minLength) && strlen($value) < $minLength) {
            $this->error(self::LENGTH);
            $isValid = false;
        }

        $requireUpper = $this->getOption(self::OPTION_REQUIRE_UPPER);
        if ($requireUpper === true && !preg_match('/[A-Z]/', $value)) {
            $this->error(self::UPPER);
            $isValid = false;
        }

        $requireLower = $this->getOption(self::OPTION_REQUIRE_LOWER);
        if ($requireLower === true && !preg_match('/[a-z]/', $value)) {
            $this->error(self::LOWER);
            $isValid = false;
        }

        $requireDigit = $this->getOption(self::OPTION_REQUIRE_DIGIT);
        if ($requireDigit === true && !preg_match('/\d/', $value)) {
            $this->error(self::DIGIT);
            $isValid = false;
        }
        
        $requireSpecialCharacters = $this->getOption(self::OPTION_REQUIRE_SPECIAL_CHARACTERS);
        $specialCharacters = $this->getOption(self::OPTION_SPECIAL_CHARACTERS);
        if ($requireSpecialCharacters === true && strlen($specialCharacters) > 0 &&  !preg_match('/' . preg_quote($specialCharacters,'/') . '/', $value)) {
            $this->error(self::SPECIAL);
            $isValid = false;
        }

        return $isValid;
    }
}