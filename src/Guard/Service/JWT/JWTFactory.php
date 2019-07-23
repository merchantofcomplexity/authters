<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT;

use Carbon\Carbon;
use InvalidArgumentException;
use Lcobucci\Jose\Parsing\Exception as JoseException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Lcobucci\JWT\Validation\InvalidToken;
use MerchantOfComplexity\Authters\Domain\User\IdentityId;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Value\BearerToken;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Value\JWTTokenCredential;
use MerchantOfComplexity\Authters\Support\Exception\InvalidJWTToken;
use Throwable;
use function get_class;

final class JWTFactory
{
    const AUDIENCE = 'ALL';
    const ISSUED_BY = 'APP';

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function createTokenCredential(IdentityId $identityId): JWTTokenCredential
    {
        $token = $this->configuration->createBuilder()
            ->issuedBy(self::ISSUED_BY)
            ->permittedFor(self::AUDIENCE)
            ->relatedTo($identityId->identify())
            ->issuedAt(Carbon::now()->toImmutable())
            ->getToken($this->configuration->getSigner(), $this->configuration->getSigningKey());

        return JWTTokenCredential::fromString((string)$token);
    }

    /**
     * @param BearerToken $bearerToken
     * @return IdentityId
     * @throws Throwable
     */
    public function parseToken(BearerToken $bearerToken): IdentityId
    {
        try {
            $plainToken = $this->validateToken($bearerToken);

            $this->assertValidSignature($plainToken);

            if ($identityId = $plainToken->claims()->get(RegisteredClaims::SUBJECT, false)) {
                return IdentityId::fromString($identityId);
            }

            throw InvalidJWTToken::invalidToken();
        } catch (Throwable $exception) {
            $this->onException($exception);
        }
    }

    protected function validateToken(BearerToken $bearerToken): Plain
    {
        /** @var Plain $token */
        $token = $this->configuration->getParser()->parse($bearerToken->getValue());

        $claims = [
            $audience = new PermittedFor(self::AUDIENCE),
            $issuedBy = new IssuedBy(self::ISSUED_BY)
        ];

        $this->configuration->getValidator()->assert($token, ...$claims);

        return $token;
    }

    protected function assertValidSignature(Plain $token): void
    {
        $key = $this->configuration->getSigningKey();

        if (!$this->configuration->getSigner()->verify($token->signature()->hash(), $token->payload(), $key)) {
            throw InvalidJWTToken::invalidSignature();
        }
    }

    /**
     * @param Throwable $exception
     * @return Throwable
     * @throws Throwable
     */
    protected function onException(Throwable $exception): Throwable
    {
        $handled = [
            InvalidToken::class,
            ConstraintViolation::class,
            JoseException::class,
            InvalidArgumentException::class
        ];

        if (in_array(get_class($exception), $handled)) {
            throw new InvalidJWTToken($exception->getMessage(), 403, $exception);
        }

        throw $exception;
    }
}