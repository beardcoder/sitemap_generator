<?php
namespace Markussom\SitemapGenerator\Tests\Functional\Controller;

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

use TYPO3\CMS\Core\Tests\FunctionalTestCase;

/**
 * Class SitemapControllerTest
 *
 * @package Markussom\SitemapGenerator\Tests\Unit
 */
class SitemapControllerTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Extensions/news_fixture',
        'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Extensions/events_fixture',
        'typo3conf/ext/sitemap_generator',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/pages.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/news.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/events.xml');
    }

    /**
     * @test
     */
    public function pages()
    {
        $this->setUpFrontendRootPage(
            1,
            [
                'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Frontend/PagesRenderer.ts',
            ]
        );
        $response = $this->getFrontendResponse(
            1,
            0,
            0,
            0,
            true,
            0
        );
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Fixtures/OutputXml/pages.xml', $response->getContent());
    }

    /**
     * @test
     */
    public function emptyPages()
    {
        $this->setUpFrontendRootPage(
            1,
            [
                'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Frontend/EmptyPagesRenderer.ts',
            ]
        );
        $response = $this->getFrontendResponse(
            1,
            0,
            0,
            0,
            true,
            0
        );
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Fixtures/OutputXml/emptyPages.xml', $response->getContent());
    }

    /**
     * @test
     */
    public function plugins()
    {
        $this->setUpFrontendRootPage(
            1,
            [
                'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Frontend/PluginRenderer.ts',
            ]
        );
        $response = $this->getFrontendResponse(
            1,
            0,
            0,
            0,
            true,
            0
        );
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Fixtures/OutputXml/plugin.xml', $response->getContent());
    }

    /**
     * @test
     */
    public function emptyNews()
    {
        $this->setUpFrontendRootPage(
            1,
            [
                'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Frontend/EmptyNewsRenderer.ts',
            ]
        );
        $response = $this->getFrontendResponse(
            1,
            0,
            0,
            0,
            true,
            0
        );
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Fixtures/OutputXml/emptyNews.xml', $response->getContent());
    }

    /**
     * @test
     */
    public function googleNewsSitemap()
    {
        $this->setUpFrontendRootPage(
            1,
            [
                'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Frontend/GoogleNewsSitemap.ts',
            ]
        );
        $response = $this->getFrontendResponse(
            1,
            0,
            0,
            0,
            true,
            0
        );
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../Fixtures/OutputXml/googleNewsSitemap.xml',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function emptyGoogleNewsSitemap()
    {
        $this->setUpFrontendRootPage(
            1,
            [
                'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Frontend/EmptyGoogleNewsSitemap.ts',
            ]
        );
        $response = $this->getFrontendResponse(
            1,
            0,
            0,
            0,
            true,
            0
        );
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../Fixtures/OutputXml/emptyGoogleNewsSitemap.xml',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function validateXml()
    {
        $this->setUpFrontendRootPage(
            1,
            [
                'typo3conf/ext/sitemap_generator/Tests/Functional/Fixtures/Frontend/ValidateRenderer.ts',
            ]
        );
        $response = $this->getFrontendResponse(
            1,
            0,
            0,
            0,
            true,
            0
        );
        $xml = new \DOMDocument();
        $xml->loadXML($response->getContent());
        $result = $xml->schemaValidate(__DIR__ . '/../Fixtures/sitemap.xsd');

        $this->assertEquals(
            $result,
            true
        );
    }
}
