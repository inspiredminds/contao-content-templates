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

use Contao\CoreBundle\Picker\PickerBuilderInterface;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\StringUtil;
use InspiredMinds\ContaoContentTemplates\Controller\CreateContentTemplateFromPageController;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Callback(table="tl_content_template", target="config.onload")
 */
class AdjustContentTemplateGlobalOperationsListener
{
    private $pickerBuilder;
    private $translator;
    private $router;

    public function __construct(PickerBuilderInterface $pickerBuilder, TranslatorInterface $translator, RouterInterface $router)
    {
        $this->pickerBuilder = $pickerBuilder;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function __invoke(DataContainer $dc): void
    {
        $op = &$GLOBALS['TL_DCA']['tl_content_template']['list']['global_operations']['new_from_page'];

        $applyUrl = $this->router->generate(CreateContentTemplateFromPageController::class);

        if (!isset($op['attributes'])) {
            $op['attributes'] = '';
        }

        $op['attributes'] .= ' data-apply="'.$applyUrl.'"';
        $op['attributes'] .= ' data-title="'.$this->translator->trans('Choose a page').'"';

        $op['button_callback'] = function (?string $href, string $label, string $title, string $class, string $attributes): string {
            $href = $this->pickerBuilder->getUrl('dc.tl_page', [
                'fieldType' => 'radio',
            ]);

            return '<a href="'.$href.'" class="'.$class.'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.$label.'</a> ';
        };

        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaocontenttemplates/content-templates-modal.js|async';
    }
}
