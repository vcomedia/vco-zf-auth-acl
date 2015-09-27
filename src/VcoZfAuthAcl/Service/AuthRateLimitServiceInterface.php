<?php
namespace VcoZfAuthAcl\Service;

interface AuthRateLimitServiceInterface {
    public function isAuthRateLimitExceeded($identity, $identityParameter);
    public function regsiterFailedLogin($identity, $identityParameter);
    public function purge($mins=120);
}