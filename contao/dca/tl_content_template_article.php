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

// Copy the tl_article DCA
$GLOBALS['TL_DCA']['tl_content_template_article'] = $GLOBALS['TL_DCA']['tl_article'];

// Set the ptable
$GLOBALS['TL_DCA']['tl_content_template_article']['config']['ptable'] = 'tl_content_template';

// Remove toggle icons
unset(
    $GLOBALS['TL_DCA']['tl_content_template_article']['list']['global_operations']['toggleNodes'],
    $GLOBALS['TL_DCA']['tl_content_template_article']['list']['operations']['toggle'],
);

// Only allow core callbacks (without tl_article::checkPermission)
$allowedOnloadCallbacks = [
    ['tl_article', 'checkPermission'],
    ['tl_article', 'addCustomLayoutSectionReferences'],
];

$callbacks = [];

foreach ($GLOBALS['TL_DCA']['tl_content_template_article']['config']['onload_callback'] as $callback) {
    if (\is_array($callback) && !\in_array($callback, $allowedOnloadCallbacks, true)) {
        continue;
    }

    $callbacks[] = $callback;
}

$GLOBALS['TL_DCA']['tl_content_template_article']['config']['onload_callback'] = $callbacks;

// Unset some callbacks (no permission check)
unset(
    $GLOBALS['TL_DCA']['tl_content_template_article']['list']['operations']['edit']['button_callback'],
    $GLOBALS['TL_DCA']['tl_content_template_article']['list']['operations']['copy']['button_callback'],
    $GLOBALS['TL_DCA']['tl_content_template_article']['list']['operations']['cut']['button_callback'],
    $GLOBALS['TL_DCA']['tl_content_template_article']['list']['operations']['delete']['button_callback']
);

// Configure list sorting
$GLOBALS['TL_DCA']['tl_content_template_article']['list']['sorting'] = [
    'mode' => 4,
    'fields' => ['sorting'],
    'headerFields' => ['name'],
    'panelLayout' => 'search,limit',
    'child_record_callback' => function (array $row) {
        return '<div class="tl_content_left">'.Image::getHtml('article.svg', '', 'style="vertical-align: text-bottom"').' '.$row['title'].'</div>';
    },
];

// Added via loadDataContainer
unset($GLOBALS['TL_DCA']['tl_content_template_article']['config']['ondelete_callback']);
