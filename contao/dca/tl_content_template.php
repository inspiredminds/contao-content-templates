<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Image;

$GLOBALS['TL_DCA']['tl_content_template'] = [
    'config' => [
        'dataContainer' => 'Table',
        'ctable' => ['tl_content_template_article'],
        'enableVersioning' => true,
        'switchToEdit' => true,
        'markAsCopy' => 'name',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['name'],
            'flag' => 1,
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['name'],
            'format' => '%s',
            'label_callback' => function (array $row, string $label) {
                return Image::getHtml('regular.svg', '', 'style="vertical-align: text-bottom"').' '.$label;
            },
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_content_template_article',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'palettes' => [
        'default' => '{content_template_legend},name,disable_mapping',
    ],

    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'disable_mapping' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'm12'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
    ],
];
