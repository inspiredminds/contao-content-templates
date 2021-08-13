<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Controller;
use Contao\Image;

Controller::loadDataContainer('tl_article');

$GLOBALS['TL_DCA']['tl_content_template_article'] = $GLOBALS['TL_DCA']['tl_article'];

$GLOBALS['TL_DCA']['tl_content_template_article']['config']['ptable'] = 'tl_content_template';
unset($GLOBALS['TL_DCA']['tl_content_template_article']['list']['global_operations']['toggleNodes']);

$GLOBALS['TL_DCA']['tl_content_template_article']['list']['sorting'] = [
    'mode' => 4,
    'fields' => ['sorting'],
    'headerFields' => ['name'],
    'panelLayout' => 'search,limit',
    'child_record_callback' => function (array $row) {
        return '<div class="tl_content_left">'.Image::getHtml('article.svg').' '.$row['title'].'</div>';
    },
];
