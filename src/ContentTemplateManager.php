<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoContentTemplates;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model;
use Contao\PageModel;
use Contao\Versions;
use InspiredMinds\ContaoContentTemplates\Model\ContentTemplateArticleModel;
use InspiredMinds\ContaoContentTemplates\Model\ContentTemplateModel;

class ContentTemplateManager
{
    private $mappedElements = [];

    public function __construct(
        private readonly ContaoFramework $framework,
        private array $copyProperties,
        private readonly bool $deleteEmptyArticles,
    ) {
    }

    public function applyContentTemplate(int $pageId, int $templateId): void
    {
        $this->framework->initialize();

        $page = PageModel::findById($pageId);
        $template = ContentTemplateModel::findById($templateId);

        if (null === $page || null === $template) {
            throw new \RuntimeException('Invalid page or template.');
        }

        $templateArticles = ContentTemplateArticleModel::findByPid($templateId);

        if (null === $templateArticles) {
            return;
        }

        // Delete any empty target articles
        if ($this->deleteEmptyArticles) {
            $targetArticles = ArticleModel::findBy(['pid = ?'], [$pageId]);

            foreach ($targetArticles as $targetArticle) {
                if (null === ContentModel::findBy(['pid = ?', 'ptable = ?'], [$targetArticle->id, $targetArticle->getTable()])) {
                    $targetArticle->delete();
                }
            }
        }

        foreach ($templateArticles as $templateArticle) {
            $targetArticle = $this->getTargetArticle($pageId, $templateArticle, (bool) $template->disable_mapping);

            // Update the properties of the target article
            $this->updateProperties($templateArticle, $targetArticle);

            // Update the content elements of the article
            $this->updateContentElements($templateArticle, $targetArticle);
        }
    }

    public function createContentTemplateFromPage(int $pageId): void
    {
        $this->framework->initialize();

        $page = PageModel::findById($pageId);

        if (null === $page) {
            throw new \RuntimeException('Invalid page.');
        }

        $template = new ContentTemplateModel();
        $template->name = $page->title;
        $template->tstamp = time();
        $template->save();

        foreach (ArticleModel::findByPid($pageId) ?? [] as $pageArticle) {
            $pageArticleData = $pageArticle->row();
            unset($pageArticleData['id']);

            $templateArticle = new ContentTemplateArticleModel();
            $templateArticle->setRow($pageArticleData);
            $templateArticle->pid = $template->id;
            $templateArticle->save();

            $pageElements = ContentModel::findBy(['pid = ?', 'ptable = ?'], [(int) $pageArticle->id, $pageArticle->getTable()]);

            /** @var ContentModel $pageElement */
            foreach ($pageElements ?? [] as $pageElement) {
                $templateElement = clone $pageElement;
                $templateElement->ptable = $templateArticle->getTable();
                $templateElement->pid = (int) $templateArticle->id;
                $templateElement->save();
            }
        }
    }

    private function getTargetArticle(int $pageId, ContentTemplateArticleModel $templateArticle, bool $disableMapping = false): ArticleModel
    {
        $targetArticle = null;

        if (!$disableMapping) {
            // Search by source ID
            $targetArticle = ArticleModel::findOneBy(
                ['pid = ?', 'content_template_source = ?'],
                [$pageId, $templateArticle->id],
            );

            // Search by alias and column
            if (null === $targetArticle) {
                $targetArticle = ArticleModel::findOneBy(
                    ["alias != ''", 'alias = ?', 'pid = ?', 'inColumn = ?'],
                    [$templateArticle->alias, $pageId, $templateArticle->inColumn],
                );

                if (null !== $targetArticle) {
                    $targetArticle->content_template_source = $templateArticle->id;
                    $targetArticle->save();
                }
            }
        }

        // Create new article
        if (null === $targetArticle) {
            $targetArticle = new ArticleModel();
            $row = $templateArticle->row();
            unset($row[ArticleModel::getPk()]);
            $targetArticle->setRow($row);
            $targetArticle->tstamp = time();
            $targetArticle->pid = $pageId;
            $targetArticle->content_template_source = $templateArticle->id;

            // Adjust sorting
            $maxSort = 0;

            foreach (ArticleModel::findByPid($pageId) ?? [] as $otherArticle) {
                if ((int) $otherArticle->sorting > $maxSort) {
                    $maxSort = (int) $otherArticle->sorting;
                }
            }

            if ($maxSort > 0) {
                $targetArticle->sorting = $maxSort + 64;
            }

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
            ['order' => 'sorting ASC'],
        );

        if (null === $templateElements) {
            return;
        }

        // Reset the already mapped elements
        $this->mappedElements = [];

        foreach ($templateElements as $templateElement) {
            $targetElement = $this->getTargetElement($targetArticle, $templateElement);

            // Update the properties of the target element
            $this->updateProperties($templateElement, $targetElement);
        }
    }

    private function getTargetElement(ArticleModel $targetArticle, ContentModel $templateElement): ContentModel
    {
        // Search by source ID
        $targetElement = ContentModel::findOneBy(
            ['pid = ?', 'ptable = ?', 'content_template_source = ?'],
            [$targetArticle->id, $targetArticle->getTable(), $templateElement->id],
        );

        // Search by type
        if (null === $targetElement) {
            $targetCandidates = ContentModel::findBy(
                ['pid = ?', 'ptable = ?', 'type = ?'],
                [$targetArticle->id, $targetArticle->getTable(), $templateElement->type],
                ['order' => 'sorting ASC'],
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

    private function updateProperties(Model $source, Model $target): void
    {
        $table = $target->getTable();

        if (empty($this->copyProperties[$table])) {
            return;
        }

        Controller::loadDataContainer($table);

        $version = new Versions($target->getTable(), $target->id);
        $version->initialize();

        $createVersion = false;

        foreach ($this->copyProperties[$table] as $prop) {
            if (!empty($GLOBALS['TL_DCA'][$table]['fields'][$prop])) {
                $target->{$prop} = $source->{$prop};
            }
        }

        if ($target->isModified()) {
            $target->tstamp = time();
            $createVersion = true;
        }

        $target->save();

        if ($createVersion) {
            $version->create();
        }
    }
}
