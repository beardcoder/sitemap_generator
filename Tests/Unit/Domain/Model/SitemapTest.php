<?php

namespace Markussom\SitemapGenerator\Tests\Unit\Domain\Model;

use Markussom\SitemapGenerator\Domain\Model\Sitemap;
use Markussom\SitemapGenerator\Domain\Model\UrlEntry;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * test class sitemap
 */
class SitemapTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(Sitemap::class, ['dummy']);
    }

    /**
     * @test
     */
    public function hasEntries()
    {
        $this->assertInstanceOf(ObjectStorage::class, $this->subject->_get('urlEntries'));
    }

    /**
     * @test
     */
    public function canGetUrlEntries()
    {
        $this->assertInstanceOf(ObjectStorage::class, $this->subject->_call('getUrlEntries'));
    }

    /**
     * @test
     */
    public function canSetUrlEntries()
    {
        $storage = new ObjectStorage();
        $storage->attach(new UrlEntry());
        $storage->attach(new UrlEntry());
        $this->assertSame(2, $storage->count());

        $this->subject->_callRef('setUrlEntries', $storage);

        $this->assertEquals($storage, $this->subject->_call('getUrlEntries'));
    }

    /**
     * @test
     */
    public function canAddSingleEntry()
    {
        $entry = new UrlEntry();
        $this->subject->_callRef('addUrlEntry', $entry);

        $this->assertTrue($this->subject->_call('getUrlEntries')->contains($entry));
    }

    /**
     * @test
     */
    public function isFilledTest()
    {
        $this->assertFalse($this->subject->_call('isFilled'));

        $entry = new UrlEntry();
        $this->subject->_callRef('addUrlEntry', $entry);

        $this->assertTrue($this->subject->_call('isFilled'));
    }
}
