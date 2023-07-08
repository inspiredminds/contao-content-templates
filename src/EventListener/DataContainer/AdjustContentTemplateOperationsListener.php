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

use Contao\CoreBundle\Picker\PickerConfig;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Callback(table="tl_content_template", target="config.onload")
 */
class AdjustContentTemplateOperationsListener
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(DataContainer $dc): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request->query->has('picker')) {
            return;
        }

        $pickerConfig = PickerConfig::urlDecode($request->query->get('picker'));

        if (!str_starts_with($pickerConfig->getExtra('source'), 'tl_page.')) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_content_template']['config']['closed'] = true;
    }
}
