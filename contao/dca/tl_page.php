<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_page']['list']['global_operations'] = [
    'apply_content_template' => [
        'href' => 'apply_content_template',
        'icon' => 'article.svg',
    ],
] + $GLOBALS['TL_DCA']['tl_page']['list']['global_operations'];
