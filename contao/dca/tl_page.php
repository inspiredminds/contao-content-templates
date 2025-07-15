<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

$GLOBALS['TL_DCA']['tl_page']['list']['global_operations'] = [
    'apply_content_template' => [
        'href' => 'apply_content_template',
        'icon' => 'article.svg',
    ],
] + $GLOBALS['TL_DCA']['tl_page']['list']['global_operations'];
