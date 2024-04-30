<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\SetList;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\YieldDataProviderRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictSetUpRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../Classes',
        __DIR__ . '/../Tests',
        __DIR__ . '/rector.php',
    ])
    ->withPhpSets(
        true
    )
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::STRICT_BOOLEANS,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::INSTANCEOF,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY
    ])
    ->withSkip([
        FinalizeClassesWithoutChildrenRector::class,
        ChangeAndIfToEarlyReturnRector::class,
        TypedPropertyFromStrictSetUpRector::class,
        AddMethodCallBasedStrictParamTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        YieldDataProviderRector::class,
        AddSeeTestAnnotationRector::class,

        // can be removed after we drop support for PHP 8.0
        ClassPropertyAssignToConstructorPromotionRector::class
    ])
    ->withAutoloadPaths([__DIR__ . '/../Classes'])
    ->registerService(RemoveUnusedPrivatePropertyRector::class);
