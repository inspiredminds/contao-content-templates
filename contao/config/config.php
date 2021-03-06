<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use InspiredMinds\ContaoContentTemplates\Model\ContentTemplateArticleModel;
use InspiredMinds\ContaoContentTemplates\Model\ContentTemplateModel;

$GLOBALS['BE_MOD']['content']['content_templates'] = [
    'tables' => ['tl_content_template', 'tl_content_template_article', 'tl_content'],
];

$GLOBALS['TL_MODELS']['tl_content_template'] = ContentTemplateModel::class;
$GLOBALS['TL_MODELS']['tl_content_template_article'] = ContentTemplateArticleModel::class;
