<?php
namespace VcoZfAuthAcl\Validator;

use Zend\Validator\AbstractValidator;

class PasswordStrength extends AbstractValidator
{   
    const LENGTH = 'length';
    const UPPER  = 'upper';
    const LOWER  = 'lower';
    const DIGIT  = 'digit';

    public function __construct($options = null)
    {
        //TODO: make these configurable options
        parent::__construct($options);
    }
    
    protected $messageTemplates = array(
        self::LENGTH => "Password must be at least 8 characters in length",
        self::UPPER  => "Password must contain at least one uppercase letter",
        self::LOWER  => "Password must contain at least one lowercase letter",
        self::DIGIT  => "Password must contain at least one digit character"
    );

    public function isValid($value)
    {
        $this->setValue($value);

        $isValid = true;

        if (strlen($value) < 8) {
            $this->error(self::LENGTH);
            $isValid = false;
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $this->error(self::UPPER);
            $isValid = false;
        }

        if (!preg_match('/[a-z]/', $value)) {
            $this->error(self::LOWER);
            $isValid = false;
        }

        if (!preg_match('/\d/', $value)) {
            $this->error(self::DIGIT);
            $isValid = false;
        }

        return $isValid;
    }
}