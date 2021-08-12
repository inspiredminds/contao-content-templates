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

use Contao\Model;

class ContentTemplateModel extends Model
{
    public const TYPE_CONTENT = 'article';
    public const TYPE_TEMPLATE = 'template';

    protected static $strTable = 'tl_content_template';
}
