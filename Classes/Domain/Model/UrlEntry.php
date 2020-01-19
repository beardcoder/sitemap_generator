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

/**
 * Class UrlEntry
 */
class UrlEntry
{

    /**
     * The database field to exclude the entry from sitemap
     * @var string
     */
    const EXCLUDE_FROM_SITEMAP = 'exclude_from_sitemap';

    /**
     * Location For example: http://www.example.com/site1
     *
     * @var string
     */
    protected $loc = '';

    /**
     * Last modification date of entry
     *
     * @var string
     */
    protected $lastmod = '';

    /**
     * Change frequency
     * always
     * hourly
     * daily
     * weekly
     * monthly
     * yearly
     * never
     *
     * @var string
     */
    protected $changefreq = '';

    /**
     * Priority
     *
     * @var float
     */
    protected $priority = 0.5;

    /**
     * Get Loc
     *
     * @return string
     */
    public function getLoc(): string
    {
        return $this->loc;
    }

    /**
     * Set Loc
     *
     * @param string $loc
     */
    public function setLoc(string $loc): void
    {
        $this->loc = $loc;
    }

    /**
     * Get Lastmod
     *
     * @return string
     */
    public function getLastmod(): string
    {
        return $this->lastmod;
    }

    /**
     * Set Lastmod
     *
     * @param string $lastmod
     */
    public function setLastmod(string $lastmod): void
    {
        $this->lastmod = $lastmod;
    }

    /**
     * Get Changefreq
     *
     * @return string
     */
    public function getChangefreq(): string
    {
        return $this->changefreq;
    }

    /**
     * Set Changefreq
     *
     * @param string $changefreq
     */
    public function setChangefreq(string $changefreq): void
    {
        $this->changefreq = $changefreq;
    }

    /**
     * Get Priority
     *
     * @return float
     */
    public function getPriority(): float
    {
        return $this->priority;
    }

    /**
     * Set Priority
     *
     * @param float $priority
     */
    public function setPriority(float $priority): void
    {
        $this->priority = $priority;
    }
}
