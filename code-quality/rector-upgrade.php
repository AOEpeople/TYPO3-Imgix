<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__ . '/../Classes',
            __DIR__ . '/../Tests',
            __DIR__ . '/../code-quality',
        ]
    );

    $rectorConfig->import(SetList::PHP_82);
    $rectorConfig->importNames(false);
    $rectorConfig->autoloadPaths([__DIR__ . '/../Classes']);
    $rectorConfig->cacheDirectory('.cache/rector/upgrade/');
    $rectorConfig->skip([]);
    $rectorConfig->rule(RemoveUnusedPrivatePropertyRector::class);
};
