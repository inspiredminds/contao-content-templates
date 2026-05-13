<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoContentTemplates\Controller;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use InspiredMinds\ContaoContentTemplates\ContentTemplateManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

#[AsController]
#[Route(path: '/contao/_apply_content_template/{pageId}/{templateId}', name: self::class, defaults: ['_scope' => 'backend'])]
class ApplyContentTemplateController
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ContentTemplateManager $manager,
        private readonly ContaoCsrfTokenManager $tokenManager,
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
                'rt' => $this->tokenManager->getDefaultTokenValue(),
            ],
            RouterInterface::ABSOLUTE_PATH,
        );

        return new RedirectResponse($redirect);
    }
}
