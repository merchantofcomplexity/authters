<?php

namespace MerchantOfComplexity\Authters\Domain\Role;

use function get_class;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Role;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class RoleValue implements Role, Value
{
    const PREFIX = 'ROLE_';

    /**
     * @var string
     */
    private $role;

    protected function __construct(string $role)
    {
        $this->role = $role;
    }

    public static function fromString(string $roleName): self
    {
        Assert::startsWith($roleName, self::PREFIX);

        Assert::minLength($roleName, 8);

        return new self($roleName);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getValue()
    {
        return $this->getRole();
    }

    public function sameValueAs(Value $aValue): bool
    {
        return get_class($aValue) === get_class($this)
            && $this->getRole() === $aValue->getRole();
    }
}