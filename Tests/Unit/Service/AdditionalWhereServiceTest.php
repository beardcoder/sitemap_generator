<?php
namespace Markussom\SitemapGenerator\Tests\Unit\Controller;

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
 *
 */
class AdditionalWhereServiceTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    /**
     * @var \Markussom\SitemapGenerator\Service\AdditionalWhereService
     */
    protected $service;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->service = new \Markussom\SitemapGenerator\Service\AdditionalWhereService();
    }

    /**
     * @test
     */
    public function emptyWhereStringTest()
    {
        $this->assertEquals('', $this->service->getWhereString(''));
    }

    /**
     * @test
     */
    public function getWhereStringTest()
    {
        $this->assertEquals(' AND pid!=0', $this->service->getWhereString('pid!=0'));
    }
}
