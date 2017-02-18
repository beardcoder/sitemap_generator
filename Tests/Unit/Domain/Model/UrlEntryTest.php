<?php

namespace Markussom\SitemapGenerator\Tests\Unit\Domain\Model;

use Markussom\SitemapGenerator\Domain\Model\UrlEntry;

/**
 * Class UrlEntry
 */
class UrlEntryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(UrlEntry::class, ['dummy']);
    }

    /**
     * @test
     */
    public function hasLoc()
    {
        $this->assertSame('', $this->subject->_get('loc'));
    }

    /**
     * @test
     */
    public function canStoreAndReturnLoc()
    {
        $str = 'http://localhost/';
        $this->subject->_callRef('setLoc', $str);

        $this->assertEquals($str, $this->subject->_call('getLoc'));
    }

    /**
     * @test
     */
    public function hasLastmod()
    {
        $this->assertSame('', $this->subject->_get('lastmod'));
    }

    /**
     * @test
     */
    public function canStoreAndReturnLastmod()
    {
        $str = '2009-12-10';
        $this->subject->_callRef('setLastmod', $str);

        $this->assertEquals($str, $this->subject->_call('getLastmod'));
    }

    /**
     * @test
     */
    public function hasChangefreq()
    {
        $this->assertSame('', $this->subject->_get('changefreq'));
    }

    /**
     * @test
     */
    public function canStoreAndReturnChangefreq()
    {
        $str = 'monthly';
        $this->subject->_callRef('setChangefreq', $str);

        $this->assertEquals($str, $this->subject->_call('getChangefreq'));
    }

    /**
     * @test
     */
    public function hasPriority()
    {
        $this->assertSame(0.5, $this->subject->_get('priority'));
    }

    /**
     * @test
     */
    public function canStoreAndReturnPriority()
    {
        $float = 0.8;
        $this->subject->_callRef('setPriority', $float);

        $this->assertEquals($float, $this->subject->_call('getPriority'));
    }
}
