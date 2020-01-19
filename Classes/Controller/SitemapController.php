<?php
namespace Markussom\SitemapGenerator\Controller;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use Markussom\SitemapGenerator\Domain\Repository\SitemapRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class SitemapController
 */
class SitemapController extends ActionController
{

    /**
     * SitemapRepository
     * @var SitemapRepository
     */
    protected $sitemapRepository = null;

    /**
     * Initializes the current action
     * Set xml format for FLUID
     */
    protected function initializeAction(): void
    {
        $this->request->setFormat('xml');
    }

    /**
     * List action for sitemap.xml
     */
    public function listAction(): void
    {
        $sitemap = $this->sitemapRepository->generateSitemap();
        $this->view->assign('sitemap', $sitemap);
    }

    /**
     * Render Google News Sitemap
     */
    public function googleNewsListAction(): void
    {
        $urlEntries = $this->sitemapRepository->findAllGoogleNewsEntries();
        $this->view->assign('urlEntries', $urlEntries);
    }

    /**
     * Inject sitemap repository
     *
     * @param SitemapRepository $sitemapRepo
     */
    public function injectSitemapRepo(SitemapRepository $sitemapRepo): void
    {
        $this->sitemapRepository = $sitemapRepo;
    }
}
