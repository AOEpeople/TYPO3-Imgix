<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__ . '/../Classes',
            __DIR__ . '/../Tests',
            __DIR__ . '/../code-quality',
        ]
    );
    if (strpos(PHP_VERSION, '8.0') === 0) {
        $rectorConfig->phpVersion(Rector\Core\ValueObject\PhpVersion::PHP_80);
    }
    if (strpos(PHP_VERSION, '8.1') === 0) {
        $rectorConfig->phpVersion(Rector\Core\ValueObject\PhpVersion::PHP_81);
    }
    if (strpos(PHP_VERSION, '8.2') === 0) {
        $rectorConfig->phpVersion(Rector\Core\ValueObject\PhpVersion::PHP_82);
    }

    $rectorConfig->sets([
            SetList::CODE_QUALITY,
            SetList::CODING_STYLE,
            SetList::DEAD_CODE,
            SetList::EARLY_RETURN,
            SetList::PHP_74,
            SetList::PRIVATIZATION,
            SetList::TYPE_DECLARATION,
            SetList::MYSQL_TO_MYSQLI,
            SetList::TYPE_DECLARATION,
            SetList::NAMING,
        ]
    );
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_CODE_QUALITY);
    $rectorConfig->importNames(false);
    $rectorConfig->autoloadPaths([__DIR__ . '/../Classes']);
    $rectorConfig->cacheDirectory('.cache/rector/default/');

    $rectorConfig->skip(
        [
            // remove on upgrade on PHP 8.1
            Rector\CodingStyle\Rector\ClassConst\RemoveFinalFromConstRector::class,
            // end

            Rector\DeadCode\Rector\Cast\RecastingRemovalRector::class,
            Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector::class,
            Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector::class,
            Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector::class,

            Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class,
            Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector::class,
            Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector::class,
            Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
            Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,

            // Don't use it:
            // Because that would make the PHP-code less readable (e.g. because it would break with 'TYPO3-naming-rules')
            Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector::class,
            Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector::class,
            Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector::class,
            Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector::class,
            Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector::class,
            Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector::class,
            Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector::class,
            Rector\PHPUnit\CodeQuality\Rector\Class_\YieldDataProviderRector::class,
        ]
    );

    $rectorConfig->rule(RemoveUnusedPrivatePropertyRector::class);
};
