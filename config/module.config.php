<?php
/**
 * VcoZfAuthAcl - Zend Framework 2 auth and acl module.
 *
 * @category Module
 * @package  VcoZfAuthAcl
 * @author   Vahag Dudukgian (valeeum)
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     http://github.com/vcomedia/vco-zf-auth-acl/
 */

namespace VcoZfAuthAcl;

//empty translator.  used by poedit to pick up messages. still need view to translate normally. 
$translator = new \Zend\I18n\Translator\Translator;

return array(
    'VcoZfAuthAcl' => array(
        'enabled' => false,
    ),
    'service_manager' => array(
        'invokables' => array(
            'VcoZfAuthAcl\passwordService' => 'Zend\Crypt\Password\Bcrypt'  // used to hash and verify passwords
        ),
        'factories' => array(
            'Zend\Authentication\AuthenticationService' => 'VcoZfAuthAcl\Factory\Authentication\AuthenticationServiceFactory',
            'VcoZfAuthAcl\View\UnauthorizedStrategy' => 'VcoZfAuthAcl\Factory\UnauthorizedStrategyServiceFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'isAllowed' => 'VcoZfAuthAcl\Factory\View\Helper\IsAllowedFactory',
        )       
    )
);