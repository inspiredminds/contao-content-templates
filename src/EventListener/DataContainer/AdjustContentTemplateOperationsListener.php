<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoContentTemplates\EventListener\DataContainer;

use Contao\CoreBundle\Picker\PickerConfig;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Callback(table="tl_content_template", target="config.onload")
 */
class AdjustContentTemplateOperationsListener
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function __invoke(DataContainer $dc): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request->query->has('picker')) {
            return;
        }

        $pickerConfig = PickerConfig::urlDecode($request->query->get('picker'));

        if (!str_starts_with((string) $pickerConfig->getExtra('source'), 'tl_page.')) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_content_template']['config']['closed'] = true;
    }
}
