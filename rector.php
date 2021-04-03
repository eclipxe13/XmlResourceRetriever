<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Assign\CombinedAssignRector;
use Rector\Core\Configuration\Option;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator) {
    // get parameters
    $parameters = $containerConfigurator->parameters();

    // Define what rule sets will be applied
    $parameters->set(Option::SETS, [
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::PHP_73,
        PHPUnitSetList::PHPUNIT_70,
        PHPUnitSetList::PHPUNIT_75,
        PHPUnitSetList::PHPUNIT_80,
        PHPUnitSetList::PHPUNIT_90,
        PHPUnitSetList::PHPUNIT_91,
        SetList::CODE_QUALITY,
        SetList::CODE_QUALITY_STRICT,
    ]);

    $parameters->set(Option::SKIP, [
        CombinedAssignRector::class,
    ]);
};
