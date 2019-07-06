<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns;

use DateTimeImmutable;
use DateTimeZone;

trait HasTokenClock
{
    /**
     * @var DateTimeImmutable
     */
    protected $clock;

    protected function enableClock(): void
    {
        $this->clock = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }

    protected function disableClock(): void
    {
        $this->clock = null;
    }

    protected function resetClock(): void
    {
        $this->enableClock();
    }

    protected function formatClock(): ?string
    {
        if ($this->clock) {
            return $this->clock->format(DATE_ATOM);
        }

        return null;
    }
}