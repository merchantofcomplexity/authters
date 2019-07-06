<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

class AuthtersValueFailure extends AuthenticationException
{
    private $propertyPath;
    private $value;
    private $constraints;

    public function __construct(string $message, int $code, $propertyPath, $value, array $constraints = [])
    {
        parent::__construct($message, $code);

        $this->propertyPath = $propertyPath;
        $this->value = $value;
        $this->constraints = $constraints;
    }

    public function getPropertyPath()
    {
        return $this->propertyPath;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }
}