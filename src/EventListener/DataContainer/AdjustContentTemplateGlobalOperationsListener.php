<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoContentTemplates\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Picker\PickerBuilderInterface;
use Contao\CoreBundle\String\HtmlAttributes;
use Contao\DataContainer;
use InspiredMinds\ContaoContentTemplates\Controller\CreateContentTemplateFromPageController;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCallback('tl_content_template', 'config.onload')]
class AdjustContentTemplateGlobalOperationsListener
{
    public function __construct(
        private readonly PickerBuilderInterface $pickerBuilder,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
    ) {
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

        $op['button_callback'] = function (string|null $href, string $label, string $title, string $class, string $attributes): string {
            $href = $this->pickerBuilder->getUrl('dc.tl_page', [
                'fieldType' => 'radio',
            ]);

            $attributes = (new HtmlAttributes($attributes))
                ->set('type', 'button')
                ->set('data-href', $href)
                ->addClass($class)
            ;

            return \sprintf('<button%s>%s</button>', (string) $attributes, $label);
        };

        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaocontenttemplates/content-templates-modal.js|async';
    }
}
