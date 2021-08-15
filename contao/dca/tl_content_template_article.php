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
use Contao\DataContainer;
use Contao\Image;
use Contao\System;

Controller::loadDataContainer('tl_article');

$GLOBALS['TL_DCA']['tl_content_template_article'] = $GLOBALS['TL_DCA']['tl_article'];

$GLOBALS['TL_DCA']['tl_content_template_article']['config']['ptable'] = 'tl_content_template';
unset($GLOBALS['TL_DCA']['tl_content_template_article']['list']['global_operations']['toggleNodes'], $GLOBALS['TL_DCA']['tl_content_template_article']['list']['operations']['toggle']);

$GLOBALS['TL_DCA']['tl_content_template_article']['list']['sorting'] = [
    'mode' => 4,
    'fields' => ['sorting'],
    'headerFields' => ['name'],
    'panelLayout' => 'search,limit',
    'child_record_callback' => function (array $row) {
        return '<div class="tl_content_left">'.Image::getHtml('article.svg', '', 'style="vertical-align: text-bottom"').' '.$row['title'].'</div>';
    },
];

$GLOBALS['TL_DCA']['tl_content_template_article']['fields']['alias']['save_callback'] = function ($value, DataContainer $dc) {
    $aliasExists = function (string $alias) use ($dc): bool {
        return
            $this->Database->prepare('SELECT id FROM tl_article WHERE alias=? AND content_template_source!=?')->execute($alias, $dc->id)->numRows > 0 &&
            $this->Database->prepare('SELECT id FROM tl_content_template_article WHERE alias=? AND id!=?')->execute($alias, $dc->id)->numRows > 0
        ;
    };

    // Generate an alias if there is none
    if (!$value) {
        $value = System::getContainer()->get('contao.slug')->generate($dc->activeRecord->title, $dc->activeRecord->pid, $aliasExists);
    } elseif (preg_match('/^[1-9]\d*$/', $value)) {
        throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $value));
    } elseif ($aliasExists($value)) {
        throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $value));
    }

    return $value;
};
