<?php
namespace VcoZfAuthAcl\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Permissions\Acl\AclInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Class IsAllowed
 * @package View\Helper
 */
class IsAllowed extends AbstractHelper
{
    /**
     * @var AclInterface
     */
    protected $acl;

    /**
     * @var RoleInterface
     */
    protected $role;

    /**
     * @var AclInterface
     */
    protected static $defaultAcl;

    /**
     * @var RoleInterface
     */
    protected static $defaultRole;

    public function __construct($config = null) {
        if(is_array($config)) {
            if(isset($config['acl']) && !empty($config['acl']['defaultRole'])) {
                $defaultRole = $config['acl']['defaultRole'];
                if(!$defaultRole instanceof RoleInterface) {
                    $defaultRole = new GenericRole($defaultRole);
                }
                $this->setDefaultRole($defaultRole);
            }
        }
        if(isset($this->view->acl)) {
            $this->setAcl($this->view->acl);
            $this->setDefaultAcl($this->view->acl);           
        }
        
        $identity = $this->view->plugin('identity');
        die($identity->getRole() . 'sss');
    }
        
    /**
     * @param null $resource
     * @param null $privilege
     * @return $this
     */
    public function __invoke($resource = null, $privilege = null)
    {
        if (is_null($resource)) {
            return $this;
        }

        return $this->isAllowed($resource, $privilege);
    }

    /**
     * @param string|ResourceInterface $resource
     * @param string $privilege
     * @return bool
     * @throws \RuntimeException
     */
    public function isAllowed($resource, $privilege = null)
    {
        $acl = $this->getAcl();
        if (!$acl instanceof AclInterface) {
            throw new \RuntimeException('No ACL provided');
        } elseif (!$resource instanceof ResourceInterface) {
            $resource = new GenericResource($resource);
        }

        return $acl->isAllowed($this->getRole(), $resource, $privilege);
    }

    public static function setDefaultAcl(AclInterface $acl = null)
    {
        self::$defaultAcl = $acl;
    }

    /**
     * @param AclInterface $acl
     * @return $this
     */
    public function setAcl(AclInterface $acl = null)
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * @return AclInterface
     */
    protected function getAcl()
    {
        if ($this->acl instanceof AclInterface) {
            return $this->acl;
        }

        return self::$defaultAcl;
    }

    /**
     * @param RoleInterface $role
     */
    public static function setDefaultRole(RoleInterface $role = null)
    {
        self::$defaultRole = $role;
    }

    /**
     * @param RoleInterface $role
     * @return $this
     */
    public function setRole(RoleInterface $role = null)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return RoleInterface
     */
    protected function getRole()
    {
        if ($this->role instanceof RoleInterface) {
            return $this->role;
        }

        return self::$defaultRole;
    }
}