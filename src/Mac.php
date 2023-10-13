<?php

declare(strict_types=1);

/**
 * Object for handling MAC adresses
 */

namespace Option\Mac;

use OutOfBoundsException;
use TypeError;

class Mac
{
    /**
     * Store the mac address internally as string of twelwe uppercase hex digits
     * representing a six byte MAC. Example: "1234567890AB"
     */
    private function __construct(private string $mac)
    {
        $mac = strtoupper($mac);

        //Strip anything but hexdigits
        $mac = preg_replace("/[^0-9A-F]/", '', $mac);

        if (strlen($mac) != 12) {
            throw new Exception("Must be exactly 12 hex digits");
        }

        $this->mac = $mac;
    }

    /**
     * Factory attempts to figure out .... stuff
     */
    public static function factory(mixed $candidate): self
    {
        if ($candidate instanceof self) {
            return $candidate;
        }
        if (is_int($candidate)) {
            return self::fromInteger($candidate);
        }
        if (is_string($candidate)) {
            return new static($candidate);
        }
        throw new Exception("Could not figure out how to convert this " . gettype($candidate) . " to mac address.");
    }

    /**
     * Factory from a string with a mac address with hex digits. Any delimiter.
     * Unpredictable results if string contains anyting but a mac address
     */
    public static function fromHex(string $candidate): self
    {
        return new static($candidate);
    }

    /**
     * Same as fromHex, just return null if extraction fails
     */
    public static function fromHexOrNull(mixed $candidate): null|self
    {
        try {
            return new static($candidate);
        } catch (TypeError | Exception) {
            return null;
        }
    }

    /**
     * Factory from a single (0 - 2^28) integer
     */
    public static function fromInteger(int $candidate): self
    {
        self::throwIfSystemCantHandleBigEnoughIntegers();
        if (0 > $candidate) {
            throw new OutOfBoundsException("Cannot be negative. Got {$candidate}");
        }
        if ($candidate > 2 ** 48 - 1) {
            throw new OutOfBoundsException("Cannot be larger than 2^48");
        }
        return new static(str_pad(dechex($candidate), 12, "0", STR_PAD_LEFT));
    }

    /**
     * Factory from a string of exactly six bytes
     */
    public static function fromBytes(string $bytes): self
    {
        if (strlen($bytes) != 6) {
            throw new Exception("Was not exactly 6 bytes");
        }

        return self::fromHex(bin2hex($bytes));
    }

    /**
     * Sometimes, the byte order of each octet is reversed.
     * Returns a new instance where the byte ordrer is corrected.
     * Running the returned value back in again returns the original.
     */
    public function reverseBitOrder(): self
    {
        return self::fromHex(implode(array_map("self::binstrrev", $this->octets())));
    }

    /**
     * Reverse the bit order in some hex digits
     * EA (11101010) gives
     * 57 (01010111)
     */
    public static function binstrrev(string $hex): string
    {
        return self::b2h(strrev(self::h2b($hex)));
    }

    private static function h2b(string $hex): string
    {
        return str_pad(base_convert($hex, 16, 2), strlen($hex) * 4, "0", STR_PAD_LEFT);
    }

    private static function b2h(string $bin): string
    {
        return str_pad(base_convert($bin, 2, 16), strlen($bin) / 4, "0", STR_PAD_LEFT);
    }


    /**
     * Return the mac like 01:23:45:67:89:AB
     */
    public function asColon(): string
    {
        return self::insertEvery($this->mac, 2, ':');
    }

    /**
     * Return the mac like 0123.4567.89AB
     */
    public function asDot(): string
    {
        return self::insertEvery($this->mac, 4, '.');
    }

    /**
     * Return the mac like 01-23-45-67-89-AB
     */
    public function asIEE802(): string
    {
        return self::insertEvery($this->mac, 2, '-');
    }

    /**
     * Alias of asIEE802
     */
    public function asDash(): string
    {
        return $this->asIEE802();
    }

    /**
     * Returns the mac as a large integer (0 - 2^48)
     */
    public function asInteger(): int
    {
        self::throwIfSystemCantHandleBigEnoughIntegers();
        return hexdec($this->mac);
    }

    /**
     * Lowercase hex digits with no delimiters
     */
    public function asLowercase(): string
    {
        return strtolower($this->mac);
    }

    /**
     * Uppercase hex digits with no delimiters
     */
    public function asUppercase(): string
    {
        return $this->mac;
    }

    /**
     * As hex octets
     */
    public function octets(): array
    {
        return str_split($this->mac, 2);
    }

    /**
     * Is the I/G bit set?
     * (Individual/Group)
     */

    public function IGbit(): bool
    {
        return (bool)(hexdec($this->octets()[0]) & 1);
    }

    /**
     * Does the I/G bit indicate this is a Unicast address?
     */
    public function isUnicast(): bool
    {
        return !$this->IGbit();
    }

    /**
     * Does the I/G bit indicate this is a Multicast address?
     */
    public function isMulticast(): bool
    {
        return $this->IGbit();
    }

    /**
     * Is the U/L bit set?
     */
    public function ULbit(): bool
    {
        return (bool)(hexdec($this->octets()[0]) & 2);
    }

    /**
     * Does the U/L bit indicate this is a Universal address?
     */
    public function isUniversal(): bool
    {
        return !$this->ULbit();
    }

    /**
     * Does the U/L bit indicate this is a Local address?
     */
    public function isLocal(): bool
    {
        return $this->ULbit();
    }

    /**
     * As a six byte string
     */
    public function asBytes(): string
    {
        return implode('', array_map('hex2bin', $this->octets()));
    }

    /**
     * Return a copy of this where the NIC hex digits is set to zeros.
     * Useful to where only the OUI is needed.
     */
    public function vendor(): self
    {
        return new self(substr($this->mac, 0, 6) . "000000");
    }

    /**
     * Somewhat like chunk_split
     */
    private static function insertEvery($string, $size = 1, $separator = ' ')
    {
        return implode($separator, str_split($string, $size));
    }

    private static function throwIfSystemCantHandleBigEnoughIntegers(): void
    {
        if (8 > PHP_INT_SIZE) {
            throw new Exception("Your system does not handle big enough integers (must be 64 bit system)");
        }
    }

    public function __toString(): string
    {
        return $this->asColon();
    }
}

//EOF
