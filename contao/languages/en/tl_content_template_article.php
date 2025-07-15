<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

use Contao\System;

System::loadLanguageFile('tl_article');

$GLOBALS['TL_LANG']['tl_content_template_article'] = $GLOBALS['TL_LANG']['tl_article'];

unset($GLOBALS['TL_LANG']['tl_content_template_article']['pasteafter']);
