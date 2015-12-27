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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class SitemapController
 * @package Markussom\SitemapGenerator\Controller
 */
class SitemapController extends ActionController
{

    /**
     * SitemapRepository
     * @var \Markussom\SitemapGenerator\Domain\Repository\SitemapRepository
     * @inject
     */
    protected $sitemapRepo = null;

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
     * List action
     *
     * @return void
     */
    public function listAction()
    {
        $urlEntries = $this->sitemapRepo->findAllEntries();
        $this->view->assign('urlEntries', $urlEntries);
    }

    /**
     * Render Google News Sitemap
     *
     * @return void
     */
    public function googleNewsListAction()
    {
        $urlEntries = $this->sitemapRepo->findAllGoogleNewsEntries();
        if (is_array($urlEntries)) {
            $this->view->assign('urlEntries', $urlEntries);
        } else {
            $this->view->assign('urlEntries', []);
        }
    }
}
