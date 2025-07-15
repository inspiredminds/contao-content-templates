<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoContentTemplates\Model;

use Contao\Model;

/**
 * @property int    $id
 * @property string $name
 * @property bool   $disable_mapping
 */
class ContentTemplateModel extends Model
{
    public const TYPE_CONTENT = 'article';

    public const TYPE_TEMPLATE = 'template';

    protected static $strTable = 'tl_content_template';
}
