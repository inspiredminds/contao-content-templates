<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoContentTemplates\Controller;

use InspiredMinds\ContaoContentTemplates\ContentTemplateManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/contao/_apply_content_template/{pageId}/{templateId}",
 *   name=ApplyContentTemplateController::class,
 *   defaults={"_scope": "backend"}
 * )
 */
class ApplyContentTemplateController
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ContentTemplateManager $manager,
    ) {
    }

    public function __invoke(Request $request, int $pageId, int|null $templateId = null): Response
    {
        if (null !== $templateId) {
            $this->manager->applyContentTemplate($pageId, $templateId);
        }

        $redirect = $this->router->generate(
            'contao_backend',
            [
                'do' => 'article',
                'pn' => $pageId,
                'ref' => $request->attributes->get('_contao_referer_id'),
            ],
            RouterInterface::ABSOLUTE_PATH,
        );

        return new RedirectResponse($redirect);
    }
}
