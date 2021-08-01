<?php

// ecs.php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    // A. standalone rule
    $services = $containerConfigurator->services();
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]]);
    $services->set(PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class);
    $services->set(PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class);
    $services->set(PhpCsFixer\Fixer\Strict\StrictParamFixer::class);
    $services->set(PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer::class);
    $services->set(SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff::class);
    $services->set(PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer::class);
    $services->set(PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class);
    $services->set(PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class);
    $services->set(PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer::class);
    $services->set(PhpCsFixer\Fixer\ClassNotation\FinalClassFixer::class);

    // B. full sets
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SETS, [SetList::CLEAN_CODE, SetList::PSR_12]);
    $parameters->set(Option::PATHS, [__DIR__ . '/../']);
    $parameters->set(Option::EXCLUDE_PATHS, [
        'node_modules/*',
        'vendor/*',
        'var/*',
        'public/bundles/*',
        'storage/*',
        'bootstrap/*',
        '.vagrant/*',
    ]);
};
