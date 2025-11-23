<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

use Contao\Rector\Set\SetList;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;

return RectorConfig::configure()
    ->withSets([SetList::CONTAO])
    ->withPaths([
        __DIR__.'/contao',
        __DIR__.'/src',
        __DIR__.'/ecs.php',
        __DIR__.'/rector.php',
    ])
    ->withSkip([
        FirstClassCallableRector::class,
    ])
    ->withParallel()
    ->withCache(sys_get_temp_dir().'/rector_cache')
;
