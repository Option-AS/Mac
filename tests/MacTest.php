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
        $a = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('12:34:56:78:9A:BC', $a->asColon());
    }

    public function testAsDot()
    {
        $a = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('1234.5678.9ABC', $a->asDot());
    }

    public function testAsIEE802()
    {
        $a = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('12-34-56-78-9A-BC', $a->asIEE802());
    }

    public function testAsDash()
    {
        $a = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('12-34-56-78-9A-BC', $a->asDash());
    }

    public function testAsString()
    {
        $a = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('12:34:56:78:9A:BC', (string)$a);
    }

    public function testAsInteger()
    {
        $a = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals(20015998343868, $a->asInteger());
    }

    public function testAsLowercase()
    {
        $a = Mac::fromHex('12:34:56:78:9a:bc');
        $this->assertEquals('123456789abc', $a->asLowercase());
    }

    public function testAsBytes()
    {
        $a = Mac::fromHex('41:42:43:44:45:46');
        $this->assertEquals('ABCDEF', $a->asBytes());
    }

    public function testFromInteger()
    {
        $a = Mac::fromInteger(20015998343868);
        $this->assertEquals('12:34:56:78:9A:BC', (string)$a);
    }

    public function testFromIntegerLowNumber0()
    {
        $a = Mac::fromInteger(0);
        $this->assertEquals('00:00:00:00:00:00', (string)$a);
    }

    public function testFromIntegerLowNumber1()
    {
        $a = Mac::fromInteger(1);
        $this->assertEquals('00:00:00:00:00:01', (string)$a);
    }

    public function testFromIntegerLowNumber256()
    {
        $a = Mac::fromInteger(256);
        $this->assertEquals('00:00:00:00:01:00', (string)$a);
    }

    public function testFromIntegerTooSmall()
    {
        $this->expectException(OutOfBoundsException::class);
        $a = Mac::fromInteger(-1);
    }

    public function testFromIntegerTooBig()
    {
        $this->expectException(OutOfBoundsException::class);
        $a = Mac::fromInteger(2 ** 48);
    }
    public function testFromIntegerMin()
    {
        $a = Mac::fromInteger(0);
        $this->assertEquals('00:00:00:00:00:00', (string)$a);
    }

    public function testFromIntegerMax()
    {
        $a = Mac::fromInteger(2 ** 48 - 1);
        $this->assertEquals('FF:FF:FF:FF:FF:FF', (string)$a);
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
    //     $a = Mac::fromHex('0:1:2:3:4:6');
    //     $this->assertEquals('00:01:02:03:04:06', (string)$a);
    // }

    public function testfromBytes()
    {
        $a = Mac::fromBytes(chr(0x12) . chr(0x34) . chr(0x56) . chr(0x78) . chr(0x9A) . chr(0xBC));
        $this->assertEquals('12:34:56:78:9A:BC', (string)$a);
    }

    public function testbinstrrev()
    {
        $a = Mac::binstrrev("EA");
        $this->assertEquals("57", $a);
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
        $a = Mac::fromHex('12:34:56:78:9A:BC');

        $b = $a->reverseBitOrder();
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
}

//EOF
