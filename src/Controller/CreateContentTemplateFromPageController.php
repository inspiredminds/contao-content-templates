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
 * @Route("/contao/_create_content_template/{pageId}",
 *   name=CreateContentTemplateFromPageController::class,
 *   defaults={"_scope": "backend"}
 * )
 */
class CreateContentTemplateFromPageController
{
    private $router;
    private $manager;

    public function __construct(RouterInterface $router, ContentTemplateManager $manager)
    {
        $this->router = $router;
        $this->manager = $manager;
    }

    public function __invoke(Request $request, int $pageId = null): Response
    {
        if (null !== $pageId) {
            $this->manager->createContentTemplateFromPage($pageId);
        }

        $redirect = $this->router->generate('contao_backend', [
            'do' => 'content_templates',
            'ref' => $request->attributes->get('_contao_referer_id'),
        ], RouterInterface::ABSOLUTE_PATH);

        return new RedirectResponse($redirect);
    }
}
