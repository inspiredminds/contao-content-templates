<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoContentTemplates;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\PageModel;
use InspiredMinds\ContaoContentTemplates\Model\ContentTemplateArticleModel;
use InspiredMinds\ContaoContentTemplates\Model\ContentTemplateModel;

class ContentTemplateManager
{
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function applyContentTemplate(int $pageId, int $templateId): void
    {
        $this->framework->initialize();

        $page = PageModel::findByPk($pageId);
        $template = ContentTemplateModel::findByPk($templateId);

        if (null === $page || null === $template) {
            throw new \RuntimeException('Invalid page or template.');
        }

        $templateArticles = ContentTemplateArticleModel::findByPid($templateId);

        if (null === $templateArticles) {
            return;
        }

        foreach ($templateArticles as $templateArticle) {
            $targetArticle = ArticleModel::findOneBy(['pid = ?', 'content_template_source = ?'], [$pageId, $templateArticle->id]);

            if (null === $targetArticle) {
                $targetArticle = ArticleModel::findOneBy(['alias = ?', 'pid = ?', 'inColumn = ?'], [$templateArticle->alias, $pageId, $templateArticle->inColumn]);

                if (null !== $targetArticle) {
                    $targetArticle->content_template_source = $templateArticle->id;
                    $targetArticle->save();
                }
            }

            if (null === $targetArticle) {
                $targetArticle = new ArticleModel();
                $row = $templateArticle->row();
                unset($row[ArticleModel::getPk()]);
                $targetArticle->setRow($row);
                $targetArticle->pid = $pageId;
                $targetArticle->save();
            }

            // TODO: update settings

            $templateElements = ContentModel::findBy(['pid = ?', 'ptable = ?'], [$templateArticle->id, $templateArticle->getTable()], ['order' => 'sorting ASC']);

            if (null === $templateElements) {
                continue;
            }

            $mappedElements = [];

            foreach ($templateElements as $templateElement) {
                $targetElement = ContentModel::findOneBy(
                    ['pid = ?', 'ptable = ?', 'content_template_source = ?'], 
                    [$targetArticle->id, $targetArticle->getTable(), $templateElement->id]
                );

                if (null === $targetElement) {
                    $targetCandidates = ContentModel::findBy(
                        ['pid = ?', 'ptable = ?', 'type = ?'],
                        [$targetArticle->id, $targetArticle->getTable(), $templateElement->type],
                        ['order' => 'sorting ASC']
                    );

                    foreach ($targetCandidates ?? [] as $targetCandidate) {
                        if (\in_array((int) $targetCandidate->id, $mappedElements, true)) {
                            continue;
                        }

                        $targetElement = $targetCandidate;
                        $targetElement->content_template_source = $templateElement->id;
                        $targetElement->save();
                        break;
                    }
                }

                if (null === $targetElement) {
                    $targetElement = new ContentModel();
                    $row = $templateElement->row();
                    unset($row[$targetElement->getPk()]);
                    $targetElement->setRow($row);
                    $targetElement->pid = $targetArticle->id;
                    $targetElement->ptable = $targetArticle->getTable();
                    $targetElement->save();
                }

                $mappedElements[] = (int) $targetElement->id;

                // TODO: update settings
            }
        }
    }
}
