<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths(
        [
            __DIR__ . '/../Classes',
            __DIR__ . '/../Tests',
            __DIR__ . '/../code-quality',
        ]
    );

    // importDefaultSets
    $ecsConfig->import(SetList::COMMON);
    $ecsConfig->import(SetList::CLEAN_CODE);
    $ecsConfig->import(SetList::PSR_12);
    $ecsConfig->import(SetList::SYMPLIFY);

    // setDefaultConfig
    $ecsConfig->services()
        ->set(LineLengthFixer::class)
        ->call('configure', [[
            LineLengthFixer::LINE_LENGTH => 140,
            LineLengthFixer::INLINE_SHORT_LINES => false,
        ]]);
    $ecsConfig->indentation('spaces');
    $ecsConfig->lineEnding(PHP_EOL);
    $ecsConfig->cacheDirectory('.cache/ecs/default/');

    // Skip Rules and Sniffer
    $ecsConfig->skip(
        [
            '*/tests/fixtures/*',
            Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer::class => null,
            Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer::class => null,
            PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class => null,
            PhpCsFixer\Fixer\Import\OrderedImportsFixer::class => null,
            PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class => null,
            PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer::class => null,
            PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer::class => null,
            '\SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff.DuplicateSpaces' => null,
            '\SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff.PartialUse' => null,

            // It's OK, to skip this fixers, because they produce problems, when developing on windows-OS
            PhpCsFixer\Fixer\Basic\BracesFixer::class,
            PhpCsFixer\Fixer\Basic\CurlyBracesPositionFixer::class,
            PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer::class,
            PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer::class,
            PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer::class,
            PhpCsFixer\Fixer\Whitespace\LineEndingFixer::class,
            PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer::class,
            PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer::class,
            Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer::class,
            Symplify\CodingStandard\Fixer\LineLength\DocBlockLineLengthFixer::class,
            Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer::class,
            Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer::class,
            Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer::class,
        ]
    );
};
