<?php

namespace Markussom\SitemapGenerator\Tests\Unit;


/*
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

use Markussom\SitemapGenerator\Domain\Model\UrlEntry;
use Markussom\SitemapGenerator\Domain\Repository\SitemapRepository;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Class SitemapControllerTest
 * @package Markussom\SitemapGenerator\Tests\Unit
 */
class SitemapRepositoryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var SitemapRepository
     */
    public $sitemapRepo = null;

    /**
     * @var SitemapRepository
     */
    protected $subject = null;

    /**
     * @var null
     */
    protected $uriBuilder = null;

    /**
     * Set up framework
     *
     * @return void
     */
    public function setUp()
    {
        $uriBuilder = $this->getAccessibleMock(UriBuilder::class, ['build']);

        $this->subject = $this->getAccessibleMock(SitemapRepository::class, ['dummy'], [], '', false);
        $this->inject($this->subject, 'uriBuilder', $uriBuilder);
    }


    /**
     * Tear down framework
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->fixture, $this->newsRepository);
    }

    /**
     * @test
     */
    public function testEntriesFromPagesWithAttributes()
    {
        $pages = [
            [
                'uid' => 1,
                'pid ' => 0,
                'tstamp' => 1450463917,
                'doktype' => 1,
            ],
            [
                'uid' => 2,
                'pid ' => 1,
                'tstamp' => 1450744802,
                'sitemap_priority' => 8,
                'doktype' => 1,
            ]
        ];
        $resultOne = new UrlEntry();
        $resultOne->setLastmod('2015-12-18');

        $resultTwo = new UrlEntry();
        $resultTwo->setLastmod('2015-12-22');
        $resultTwo->setPriority(0.8);

        $result = $this->subject->getEntriesFromPages($pages);
        $this->assertEquals([$resultOne, $resultTwo], $result);
    }
}
