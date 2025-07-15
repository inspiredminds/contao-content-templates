<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoContentTemplates;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoContentTemplatesBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
