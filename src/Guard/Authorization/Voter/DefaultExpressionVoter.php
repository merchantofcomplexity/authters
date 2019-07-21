<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Voter;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MerchantOfComplexity\Authters\Guard\Authorization\Expression\ExpressionLanguage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\RoleHierarchy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

final class DefaultExpressionVoter implements Votable
{
    const ALIAS = 'expression_voter.default';

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    /**
     * @var RoleHierarchy
     */
    private $roleHierarchy;

    public function __construct(ExpressionLanguage $expressionLanguage,
                                TrustResolver $trustResolver,
                                RoleHierarchy $roleHierarchy = null)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->trustResolver = $trustResolver;
        $this->roleHierarchy = $roleHierarchy;
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider)
    {
        $this->expressionLanguage->registerProvider($provider);
    }

    public function vote(Tokenable $token, array $attributes, object $subject = null): int
    {
        $vote = self::ACCESS_ABSTAIN;

        $variables = null;

        foreach ($attributes as $attribute) {
            if (!$this->supportAttribute($attribute)) {
                continue;
            }

            if (!$attribute instanceof Expression) {
                $attribute = new Expression($attribute);
            }

            if (null === $variables) {
                $variables = $this->getVariables($token, $subject);
            }

            $vote = self::ACCESS_DENIED;

            if ($this->expressionLanguage->evaluate($attribute, $variables)) {
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    private function supportAttribute($attribute): bool
    {
        return $attribute instanceof Expression
            || Str::contains($attribute, '(') && Str::contains($attribute, ')');
    }

    private function getVariables(Tokenable $token, $subject): array
    {
        $variables = [
            'token' => $token,
            'identity' => $token->getIdentity(),
            'subject' => $subject,
            'roles' => $this->getTokenRoles($token),
            'trust_resolver' => $this->trustResolver
        ];

        if ($subject instanceof Request) {
            $variables['request'] = $subject;
        }

        return $variables;
    }

    private function getTokenRoles(Tokenable $token): array
    {
        $roles = $token->getRoleNames();

        if ($this->roleHierarchy) {
            return $this->roleHierarchy->getReachableRoles(...$roles);
        }

        return $roles;
    }
}