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
use Markussom\SitemapGenerator\Domain\Model\Sitemap;
use Markussom\SitemapGenerator\Domain\Repository\SitemapRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->request->setFormat('xml');
    }

    /**
     * List action for sitemap.xml
     *
     * @return void
     */
    public function listAction()
    {
        $sitemap = $this->generateSitemap();
        $this->view->assign('sitemap', $sitemap);
    }

    /**
     * Render Google News Sitemap
     *
     * @return void
     */
    public function googleNewsListAction()
    {
        $urlEntries = $this->sitemapRepository->findAllGoogleNewsEntries();
        $this->view->assign('urlEntries', $urlEntries);
    }

    /**
     * Inject sitemap repository
     *
     * @param SitemapRepository $sitemapRepo
     */
    public function injectSitemapRepo(SitemapRepository $sitemapRepo)
    {
        $this->sitemapRepository = $sitemapRepo;
    }

    /**
     * Generate sitemap
     *
     * @return Sitemap|null
     */
    public function generateSitemap()
    {
        if ($this->sitemapRepository->findAllEntries()) {
            $entryStorage = GeneralUtility::makeInstance(ObjectStorage::class);
            $sitemap = GeneralUtility::makeInstance(Sitemap::class);
            $sitemap->setEntries($entryStorage);
            return $sitemap;
        }
        return null;
    }
}
