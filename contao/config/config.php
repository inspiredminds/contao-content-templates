<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use InspiredMinds\ContaoContentTemplates\Model\ContentTemplateModel;

$GLOBALS['BE_MOD']['content']['content_templates'] = [
    'tables' => ['tl_content_template', 'tl_content'],
    'table' => &$GLOBALS['BE_MOD']['content']['article']['table'],
    'list' => &$GLOBALS['BE_MOD']['content']['article']['list'],
];

$GLOBALS['TL_MODELS']['tl_node'] = ContentTemplateModel::class;
