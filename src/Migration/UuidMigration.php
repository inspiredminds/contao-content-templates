<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoContentTemplates\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

class UuidMigration extends AbstractMigration
{
    private static $tables = ['tl_content', 'tl_article', 'tl_content_template_article'];

    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function shouldRun(): bool
    {
        foreach (self::$tables as $table) {
            if ($this->shouldRunForTable($table)) {
                return true;
            }
        }

        return false;
    }

    public function run(): MigrationResult
    {
        foreach (self::$tables as $table) {
            $this->runForTable($table);
        }

        return $this->createResult(true);
    }

    private function shouldRunForTable(string $table): bool
    {
        $sm = $this->db->getSchemaManager();

        if (!$sm->tablesExist([$table])) {
            return false;
        }

        if (!isset($sm->listTableColumns($table)['uuid'])) {
            return false;
        }

        return (int) $this->db->fetchOne("SELECT COUNT(*) FROM $table WHERE uuid = ''") > 0;
    }

    private function runForTable(string $table): void
    {
        $entries = $this->db->fetchAllAssociative("SELECT id FROM $table WHERE uuid = ''");

        foreach ($entries as $entry) {
            $uuid = Uuid::uuid4()->toString();
            $this->db->update($table, ['uuid' => $uuid], ['id' => $entry['id']]);
        }
    }
}
