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
 * Class UrlEntryNews
 */
class GoogleNewsUrlEntry extends UrlEntry
{
    /**
     * Name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Language
     *
     * @var string
     */
    protected $language = 'en';

    /**
     * Access
     *
     * @var string
     */
    protected $access = '';

    /**
     * Genres
     *
     * @var string
     */
    protected $genres = '';

    /**
     * Publication date
     *
     * @var string
     */
    protected $publicationDate = '';

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Keywords
     *
     * @var string
     */
    protected $keywords = '';

    /**
     * Stock tickers
     *
     * @var string
     */
    protected $stockTickers = '';

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get Language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set Language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get Access
     *
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Set Access
     *
     * @param string $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * Get Genres
     *
     * @return string
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set Genres
     *
     * @param string $genres
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
    }

    /**
     * Get PublicationDate
     *
     * @return string
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * Set PublicationDate
     *
     * @param string $publicationDate
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get Keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set Keywords
     *
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get StockTickers
     *
     * @return string
     */
    public function getStockTickers()
    {
        return $this->stockTickers;
    }

    /**
     * Set StockTickers
     *
     * @param string $stockTickers
     */
    public function setStockTickers($stockTickers)
    {
        $this->stockTickers = $stockTickers;
    }
}
