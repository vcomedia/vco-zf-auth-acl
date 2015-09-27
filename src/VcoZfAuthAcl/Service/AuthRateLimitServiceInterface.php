<?php
namespace VcoZfAuthAcl\Service;

interface AuthRateLimitServiceInterface {
    public function isAuthRateLimitExceeded($identity, $identityProperty);
    public function regsiterFailedLogin($identity, $identityProperty);
    public function purge($mins=120);
}