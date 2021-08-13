<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoContentTemplates\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

/**
 * @Callback(table="tl_content", target="config.oncreate")
 * @Callback(table="tl_article", target="config.oncreate")
 * @Callback(table="tl_content_template_article", target="config.oncreate")
 */
class CreateUuidListener
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function __invoke(string $table, int $id): void
    {
        $this->db->update($table, ['uuid' => Uuid::uuid4()->toString()], ['id' => $id]);
    }
}
