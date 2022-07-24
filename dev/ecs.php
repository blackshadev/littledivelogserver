<?php

// ecs.php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([SetList::CLEAN_CODE, SetList::PSR_12]);

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);

    $ecsConfig->rules([
        PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class,
        PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class,
        PhpCsFixer\Fixer\Strict\StrictParamFixer::class,
        PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class,
        PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer::class,
        PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class,
        PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class,
        PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer::class,
        PhpCsFixer\Fixer\ClassNotation\FinalClassFixer::class,

    ]);

    $ecsConfig->paths([
        __DIR__,
        __DIR__ . '/../app',
        __DIR__ . '/../tests',
        __DIR__ . '/../routes',
        __DIR__ . '/../database',
        __DIR__ . '/../config',
        __DIR__ . '/../packages',
        __DIR__ . '/../public/index.php',
    ]);
};
