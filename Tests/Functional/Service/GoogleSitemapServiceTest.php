<?php
namespace Markussom\SitemapGenerator\Service;

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
use TYPO3\CMS\Core\Tests\FunctionalTestCase;

/**
 * Class GoogleSitemapServiceTest
 */
class GoogleSitemapServiceTest extends \Nimut\TestingFramework\TestCase\FunctionalTestCase
{

    /**
     * @var GoogleSitemapService
     */
    protected $googleSitemapService;

    public function setUp()
    {
        parent::setUp();
        $this->googleSitemapService = new GoogleSitemapService('http://www.localhost.dev/sitemap.xml');
    }

    /**
     * @test
     */
    public function requestTest()
    {
        $this->assertEquals($this->googleSitemapService->sendRequest(), 200, 'Service not available');
    }
}
