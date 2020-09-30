<?php


namespace Littledev\Tauth\Contracts;

interface JWTSubject
{
    public function getJWTSubject();
    public function getJWTExtraClaims(): array;
}
