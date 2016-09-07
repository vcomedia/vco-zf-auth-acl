<?php
namespace VcoZfAuthAcl\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class NoPasswordAuthAdapter implements AdapterInterface
{
  protected $identity;

  public function setIdentity($identity)
  {
    $this->identity = $identity;
    return $this;
  }

  public function authenticate()
  {
    return new Result(
      Result::SUCCESS,
      $this->identity,
      array('Authentication successful.')
    );
  }
}