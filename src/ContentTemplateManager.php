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
    private $mappedElements = [];

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
            $targetArticle = $this->getTargetArticle($pageId, $templateArticle);

            // TODO: update settings

            $this->updateContentElements($templateArticle, $targetArticle);
        }
    }

    private function getTargetArticle(int $pageId, ContentTemplateArticleModel $templateArticle): ArticleModel
    {
        // Search by source ID
        $targetArticle = ArticleModel::findOneBy(
            ['pid = ?', 'content_template_source = ?'], 
            [$pageId, $templateArticle->id]
        );

        // Search by alias and column
        if (null === $targetArticle) {
            $targetArticle = ArticleModel::findOneBy(
                ['alias = ?', 'pid = ?', 'inColumn = ?'], 
                [$templateArticle->alias, $pageId, $templateArticle->inColumn]
            );

            if (null !== $targetArticle) {
                $targetArticle->content_template_source = $templateArticle->id;
                $targetArticle->save();
            }
        }

        // Create new article
        if (null === $targetArticle) {
            $targetArticle = new ArticleModel();
            $row = $templateArticle->row();
            unset($row[ArticleModel::getPk()]);
            $targetArticle->setRow($row);
            $targetArticle->pid = $pageId;
            $targetArticle->content_template_source = $templateArticle->id;
            $targetArticle->save();
        }

        return $targetArticle;
    }

    private function updateContentElements(ContentTemplateArticleModel $templateArticle, ArticleModel $targetArticle): void
    {
        // Get the content elements of the template
        $templateElements = ContentModel::findBy(
            ['pid = ?', 'ptable = ?'], 
            [$templateArticle->id, $templateArticle->getTable()], 
            ['order' => 'sorting ASC']
        );

        if (null === $templateElements) {
            return;
        }

        $this->mappedElements = [];

        foreach ($templateElements as $templateElement) {
            $targetElement = $this->getTargetElement($targetArticle, $templateElement);

            // TODO: update settings
        }
    }

    private function getTargetElement(ArticleModel $targetArticle, ContentModel $templateElement): ContentModel
    {
        // Search by source ID
        $targetElement = ContentModel::findOneBy(
            ['pid = ?', 'ptable = ?', 'content_template_source = ?'], 
            [$targetArticle->id, $targetArticle->getTable(), $templateElement->id]
        );

        // Search by type
        if (null === $targetElement) {
            $targetCandidates = ContentModel::findBy(
                ['pid = ?', 'ptable = ?', 'type = ?'],
                [$targetArticle->id, $targetArticle->getTable(), $templateElement->type],
                ['order' => 'sorting ASC']
            );

            foreach ($targetCandidates ?? [] as $targetCandidate) {
                if (\in_array((int) $targetCandidate->id, $this->mappedElements, true)) {
                    continue;
                }

                $targetElement = $targetCandidate;
                $targetElement->content_template_source = $templateElement->id;
                $targetElement->save();
                break;
            }
        }

        // Create new content element
        if (null === $targetElement) {
            $targetElement = new ContentModel();
            $row = $templateElement->row();
            unset($row[$targetElement->getPk()]);
            $targetElement->setRow($row);
            $targetElement->pid = $targetArticle->id;
            $targetElement->ptable = $targetArticle->getTable();
            $targetElement->content_template_source = $templateElement->id;
            $targetElement->save();
        }

        $this->mappedElements[] = (int) $targetElement->id;

        return $targetElement;
    }
}
