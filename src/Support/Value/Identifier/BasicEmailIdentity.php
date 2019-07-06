<?php

namespace MerchantOfComplexity\Authters\Support\Value\Identifier;

use MerchantOfComplexity\Authters\Support\Contract\Value\EmailAddress;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentityEmail;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;

final class BasicEmailIdentity implements IdentityEmail, EmailAddress
{
    /**
     * @var EmailAddress
     */
    private $email;

    protected function __construct(EmailAddress $email)
    {
        $this->email = $email;
    }

    public static function fromString($email): self
    {
        $email = EmailValue::fromString($email);

        return new self($email);
    }

    public static function fromValue(EmailAddress $email): self
    {
        if ($email instanceof IdentityEmail) {
            $email = $email->getValue();

            return self::fromString($email);
        }

        return new self(clone $email);
    }

    public function getValue(): string
    {
        return $this->email->getValue();
    }

    public function identify(): string
    {
        return $this->getValue();
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->email->sameValueAs($aValue->email);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}