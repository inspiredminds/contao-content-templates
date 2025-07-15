<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoContentTemplates\EventListener\DataContainer;

use Contao\Backend;
use Contao\BackendUser;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\CoreBundle\Slug\Slug;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Security;

/**
 * This service contains all necessary DCA callbacks for tl_content_template_article.
 */
class ContentTemplateArticleListener
{
    public function __construct(
        private readonly Connection $db,
        private readonly Slug $slug,
        private readonly Security $security,
    ) {
    }

    /**
     * @Callback(table="tl_content_template_article", target="fields.alias.save")
     */
    public function onAliasSaveCallback($value, DataContainer $dc)
    {
        $aliasExists = (fn (string $alias): bool => $this->db->fetchOne('SELECT COUNT(id) FROM tl_article WHERE alias=? AND content_template_source!=?', [$alias, $dc->id]) > 0
        && $this->db->fetchOne('SELECT COUNT(id) FROM tl_content_template_article WHERE alias=? AND id!=?', [$alias, $dc->id]) > 0);

        // Generate an alias if there is none
        if (!$value) {
            $value = $this->slug->generate($dc->activeRecord->title, $dc->activeRecord->pid, $aliasExists);
        } elseif (preg_match('/^[1-9]\d*$/', (string) $value)) {
            throw new \Exception(\sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $value));
        } elseif ($aliasExists($value)) {
            throw new \Exception(\sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $value));
        }

        return $value;
    }

    /**
     * @Callback(table="tl_content_template_article", target="list.operations.editheader.button")
     */
    public function onListOperationsEditButtonCallback(array $row, string|null $href, string $label, string $title, string|null $icon, string $attributes): string
    {
        $user = $this->getUser();

        if (method_exists($user, 'canEditFieldsOf')) {
            if (!$user->canEditFieldsOf('tl_content_template_article')) {
                return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', (string) $icon)).' ';
            }
        } else {
            if (!$this->security->isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELDS_OF_TABLE, 'tl_content_template_article')) {
                return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', (string) $icon)).' ';
            }
        }

        return $this->getButton($row, $href, $label, $title, $icon, $attributes);
    }

    private function getUser(): BackendUser
    {
        return $this->security->getUser();
    }

    private function getButton(array $row, string|null $href, string $label, string $title, string|null $icon, string $attributes): string
    {
        return '<a href="'.Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }
}
