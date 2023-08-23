<?php

declare(strict_types=1);

namespace Option\Mac;

use PHPUnit\Framework\TestCase;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Mac::class)]
final class MacTest extends TestCase
{
    public function testEncode()
    {
        $sut = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('12:34:56:78:9A:BC', $sut->asColon());
    }

    public function testAsDot()
    {
        $sut = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('1234.5678.9ABC', $sut->asDot());
    }

    public function testAsIEE802()
    {
        $sut = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('12-34-56-78-9A-BC', $sut->asIEE802());
    }

    public function testAsDash()
    {
        $sut = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('12-34-56-78-9A-BC', $sut->asDash());
    }

    public function testAsString()
    {
        $sut = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('12:34:56:78:9A:BC', (string)$sut);
    }

    public function testAsInteger()
    {
        $sut = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals(20015998343868, $sut->asInteger());
    }

    public function testAsLowercase()
    {
        $sut = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('123456789abc', $sut->asLowercase());
    }

    public function testAsUppercase()
    {
        $sut = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('123456789ABC', $sut->asUppercase());
    }

    public function testAsBytes()
    {
        $sut = Mac::fromHex('41:42:43:44:45:46');
        $this->assertEquals('ABCDEF', $sut->asBytes());
    }

    public function testFromInteger()
    {
        $sut = Mac::fromInteger(20015998343868);
        $this->assertEquals('12:34:56:78:9A:BC', (string)$sut);
    }

    public function testFromIntegerLowNumber0()
    {
        $sut = Mac::fromInteger(0);
        $this->assertEquals('00:00:00:00:00:00', (string)$sut);
    }

    public function testFromIntegerLowNumber1()
    {
        $sut = Mac::fromInteger(1);
        $this->assertEquals('00:00:00:00:00:01', (string)$sut);
    }

    public function testFromIntegerLowNumber256()
    {
        $sut = Mac::fromInteger(256);
        $this->assertEquals('00:00:00:00:01:00', (string)$sut);
    }

    public function testFromIntegerTooSmall()
    {
        $this->expectException(OutOfBoundsException::class);
        $sut = Mac::fromInteger(-1);
    }

    public function testFromIntegerTooBig()
    {
        $this->expectException(OutOfBoundsException::class);
        $sut = Mac::fromInteger(2 ** 48);
    }
    public function testFromIntegerMin()
    {
        $sut = Mac::fromInteger(0);
        $this->assertEquals('00:00:00:00:00:00', (string)$sut);
    }

    public function testFromIntegerMax()
    {
        $sut = Mac::fromInteger(2 ** 48 - 1);
        $this->assertEquals('FF:FF:FF:FF:FF:FF', (string)$sut);
    }

    public function testFromHexWithInvalidHex()
    {
        $this->expectException(Exception::class);
        Mac::fromHex('not a valid mac');
    }

    public function testFromHexOrNullWithInvalidHex()
    {
        $this->assertNull(Mac::fromHexOrNull('not a valid mac'));
    }

    public function testFromHexOrNullWithNonString()
    {
        $this->assertNull(Mac::fromHexOrNull(null));
    }

    //Not a feature yet
    // public function testThatItToleratesMissingLeadingZero()
    // {
    //     $sut = Mac::fromHex('0:1:2:3:4:6');
    //     $this->assertEquals('00:01:02:03:04:06', (string)$sut);
    // }

    public function testfromBytes()
    {
        $sut = Mac::fromBytes(chr(0x12) . chr(0x34) . chr(0x56) . chr(0x78) . chr(0x9A) . chr(0xBC));
        $this->assertEquals('12:34:56:78:9A:BC', (string)$sut);
    }

    public function testbinstrrev()
    {
        $sut = Mac::binstrrev("EA");
        $this->assertEquals("57", $sut);
    }

    public function testfromBytesTooLong()
    {
        $this->expectException(Exception::class);
        Mac::fromBytes("1234567");
    }

    public function testfromBytesTooShort()
    {
        $this->expectException(Exception::class);
        Mac::fromBytes("12345");
    }

    public function testReverseBitOrder()
    {
        $sut = Mac::fromHex('12:34:56:78:9A:BC');

        $b = $sut->reverseBitOrder();
        $this->assertEquals('48:2C:6A:1E:59:3D', (string)$b);

        //And back again
        $c = $b->reverseBitOrder();
        $this->assertEquals('12:34:56:78:9A:BC', (string)$c);
    }

    public function testOctets()
    {
        $sut = Mac::fromHex('12:34:56:78:9A:BC');
        $o = $sut->octets();
        $this->assertCount(6, $o);
        $this->assertEquals('12', $o[0]);
        $this->assertEquals('34', $o[1]);
        $this->assertEquals('56', $o[2]);
        $this->assertEquals('78', $o[3]);
        $this->assertEquals('9A', $o[4]);
        $this->assertEquals('BC', $o[5]);
    }

    public function testULbit()
    {
        $sut = Mac::fromHex('00:00:00:00:00:00');
        $this->assertFalse($sut->ULbit());

        $sut = Mac::fromHex('02:00:00:00:00:00');
        $this->assertTrue($sut->ULbit());
    }

    public function testIsUniversal()
    {
        $sut = Mac::fromHex('00:00:00:00:00:00');
        $this->assertTrue($sut->isUniversal());

        $sut = Mac::fromHex('02:00:00:00:00:00');
        $this->assertFalse($sut->isUniversal());
    }

    public function testIsLocal()
    {
        $sut = Mac::fromHex('00:00:00:00:00:00');
        $this->assertFalse($sut->isLocal());

        $sut = Mac::fromHex('02:00:00:00:00:00');
        $this->assertTrue($sut->isLocal());
    }

    public function testIGbit()
    {
        $sut = Mac::fromHex('00:00:00:00:00:00');
        $this->assertFalse($sut->IGbit());

        $sut = Mac::fromHex('01:00:00:00:00:00');
        $this->assertTrue($sut->IGbit());
    }

    public function testIsUnicast()
    {
        $sut = Mac::fromHex('00:00:00:00:00:00');
        $this->assertTrue($sut->isUnicast());

        $sut = Mac::fromHex('01:00:00:00:00:00');
        $this->assertFalse($sut->isUnicast());
    }

    public function testIsMulticast()
    {
        $sut = Mac::fromHex('00:00:00:00:00:00');
        $this->assertFalse($sut->isMulticast());

        $sut = Mac::fromHex('01:00:00:00:00:00');
        $this->assertTrue($sut->isMulticast());
    }

    public function testVendor()
    {
        $sut = Mac::fromHex('12:34:56:78:9A:BC');
        $this->assertEquals('12:34:56:00:00:00', $sut->vendor());
    }
}

//EOF
