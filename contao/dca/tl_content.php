<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['content_template_source'] = [
    'foreignKey' => 'tl_content.id',
    'eval' => ['doNotCopy' => true],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];
