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
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use InspiredMinds\ContaoContentTemplates\Controller\ApplyContentTemplateController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Callback(table="tl_page", target="config.onload")
 */
class AdjustPageOperationsListener
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly PickerBuilderInterface $pickerBuilder,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function __invoke(DataContainer $dc): void
    {
        if (null === Input::get('apply_content_template')) {
            return;
        }

        // Show "apply" button
        $GLOBALS['TL_DCA']['tl_page']['list']['operations'] = [
            'apply_content_template' => [
                'icon' => 'pasteinto.svg',
                'button_callback' => function (array $row, string|null $href, string $label, string $title, string $icon, string $attributes): string {
                    if ('regular' !== $row['type']) {
                        return '';
                    }

                    $applyUrl = $this->router->generate(ApplyContentTemplateController::class, ['pageId' => $row['id']]);

                    $href = $this->pickerBuilder->getUrl('dc.tl_content_template', [
                        'fieldType' => 'radio',
                        'source' => 'tl_page.'.$row['id'],
                    ]);

                    return '<a class="content-templates-modal" href="'.$href.'" title="'.StringUtil::specialchars($title).'"'.$attributes.' data-apply="'.$applyUrl.'" data-title="'.$this->translator->trans('Choose a content template').'">'.Image::getHtml($icon, $label).'</a> ';
                },
            ],
        ];

        // Remove "new" button
        $GLOBALS['TL_DCA']['tl_page']['config']['closed'] = true;

        // Show "cancel" button
        $GLOBALS['TL_DCA']['tl_page']['list']['global_operations'] = [
            'cancel_apply_content_template' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['cancelBT'],
                'icon' => 'clipboard.svg',
                'button_callback' => function (string|null $href, string $label, string $title, string $class, string $attributes): string {
                    $href = $this->router->generate('contao_backend', [
                        'do' => 'page',
                        'ref' => $this->requestStack->getCurrentRequest()->attributes->get('_contao_referer_id'),
                    ]);

                    return '<a href="'.$href.'" class="'.$class.'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.$label.'</a> ';
                },
            ],
        ];

        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaocontenttemplates/content-templates-modal.js|async';
    }
}
