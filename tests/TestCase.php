<?php

namespace Tests;

use Closure;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function skipUnlessFortifyFeature(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    protected function captureValidationException(Closure $callback): ValidationException
    {
        try {
            $callback();
        } catch (ValidationException $exception) {
            return $exception;
        }

        $this->fail('Expected a validation exception to be thrown.');
    }
}
