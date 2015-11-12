<?php
namespace VcoZfAuthAcl\Service;

use Zend\Permissions\Acl\AclInterface;

class AclService implements AclServiceInterface {
    
    protected $acl;
    
    public function __construct(AclInterface $acl) {
        $this->acl = $acl;
    }
    
    public function getAcl() {
        return $this->acl;
    }
}