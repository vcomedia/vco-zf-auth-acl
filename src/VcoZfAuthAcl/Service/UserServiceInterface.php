<?php
namespace VcoZfAuthAcl\Service;

interface UserServiceInterface {
    
    /*
     * @return UserInterface
     */
    public function setPasswordReset($identity, $identityParameter = 'email');
    
    /*
     * @return UserInterface
     */
    public function getUserByGuid($guid);

     /*
     * @parameter $user UserInterface
     */
    public function saveUser($user);
}