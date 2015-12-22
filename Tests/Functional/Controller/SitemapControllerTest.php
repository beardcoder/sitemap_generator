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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Service\SqlSchemaMigrationService;

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
    protected $testExtensionsToLoad = ['typo3conf/ext/sitemap_generator'];

    public function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/pages.xml');
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
}
