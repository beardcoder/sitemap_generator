<?php
namespace Markussom\SitemapGenerator\Tests\Unit\Service;

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

class LimitServiceTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    /**
     * @test
     */
    public function canReturnsTheLimitStatementForDatabaseConnection()
    {
        $actual = \Markussom\SitemapGenerator\Service\LimitService::getLimitString('100');
        $this->assertTrue(is_int($actual));
        $this->assertSame(100, $actual);

        $actual = \Markussom\SitemapGenerator\Service\LimitService::getLimitString('100, 200');
        $this->assertSame(100 . ',' . 200, $actual);
    }
}
