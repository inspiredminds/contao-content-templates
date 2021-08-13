<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoContentTemplates\Model;

use Contao\ArticleModel;

class ContentTemplateArticleModel extends ArticleModel
{
    protected static $strTable = 'tl_content_template_article';
}
