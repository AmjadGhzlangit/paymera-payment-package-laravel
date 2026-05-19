<?php

use YourVendor\Paymera\PaymeraServiceProvider;

uses(Orchestra\Testbench\TestCase::class)->in('Feature');

function getPackageProviders($app): array
{
    return [
        PaymeraServiceProvider::class,
    ];
}

function getPackageAliases($app): array
{
    return [
        'Paymera' => \YourVendor\Paymera\Facades\Paymera::class,
    ];
}
