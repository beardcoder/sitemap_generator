<?php
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

namespace Markussom\SitemapGenerator\Domain\Model;

/**
 * Class UrlEntry
 * @package Markussom\SitemapGenerator\Domain\Model
 */
class UrlEntry
{
    /**
     * @var string
     */
    protected $loc = '';

    /**
     * @var string
     */
    protected $lastmod = '';

    /**
     * Change frequenz
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
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * Set Loc
     *
     * @param string $loc
     *
     * @return void
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
    }

    /**
     * Get Lastmod
     *
     * @return string
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * Set Lastmod
     *
     * @param string $lastmod
     *
     * @return void
     */
    public function setLastmod($lastmod)
    {
        $this->lastmod = $lastmod;
    }

    /**
     * Get Changefreq
     *
     * @return string
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * Set Changefreq
     *
     * @param string $changefreq
     *
     * @return void
     */
    public function setChangefreq($changefreq)
    {
        $this->changefreq = $changefreq;
    }

    /**
     * Get Priority
     *
     * @return float
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set Priority
     *
     * @param float $priority
     *
     * @return void
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}
