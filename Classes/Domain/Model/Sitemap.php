<?php
namespace Markussom\SitemapGenerator\Domain\Model;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Sitemap
 */
class Sitemap
{
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Markussom\SitemapGenerator\Domain\Model\UrlEntry>
     */
    protected $urlEntries = null;

    /**
     * Sitemap constructor.
     */
    public function __construct()
    {
        $this->urlEntries = GeneralUtility::makeInstance(ObjectStorage::class);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Markussom\SitemapGenerator\Domain\Model\UrlEntry>
     */
    public function getUrlEntries(): ObjectStorage
    {
        return $this->urlEntries;
    }

    /**
     * @param ObjectStorage $urlEntries
     */
    public function setUrlEntries(ObjectStorage $urlEntries): void
    {
        $this->urlEntries = $urlEntries;
    }

    /**
     * @param UrlEntry $urlEntry
     */
    public function addUrlEntry(UrlEntry $urlEntry): void
    {
        $this->urlEntries->attach($urlEntry);
    }

    /**
     * @return bool
     */
    public function isFilled(): bool
    {
        return 1 <= $this->urlEntries->count();
    }
}
