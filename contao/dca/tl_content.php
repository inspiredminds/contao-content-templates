<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_content']['config']['sql']['keys']['ptable,uuid'] = 'unique';

$GLOBALS['TL_DCA']['tl_content']['fields']['uuid'] = [
    'label' => ['UUID', 'UUID'],
    'eval' => ['doNotCopy' => true],
    'sql' => ['type' => 'string', 'length' => 36, 'default' => ''],
];
