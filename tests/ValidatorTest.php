<?php
namespace OddsPHP\Tests;

use OddsPHP\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {

    public function testIsFractionalValid(): void {
        $this->assertTrue(Validator::isFractional('11/20'));
        $this->assertTrue(Validator::isFractional('2/1'));
        $this->assertTrue(Validator::isFractional('555/19999'));
        $this->assertTrue(Validator::isFractional('1/1'));
    }

    public function testIsFractionalInvalid(): void {
        $this->assertFalse(Validator::isFractional(''));
        $this->assertFalse(Validator::isFractional('0/5'));
        $this->assertFalse(Validator::isFractional('5/0'));
        $this->assertFalse(Validator::isFractional('abc'));
        $this->assertFalse(Validator::isFractional('1.5/2'));
        $this->assertFalse(Validator::isFractional('/'));
        $this->assertFalse(Validator::isFractional('100'));
        $this->assertFalse(Validator::isFractional('2//1'));
    }

    public function testIsDecimalValid(): void {
        $this->assertTrue(Validator::isDecimal(1.55));
        $this->assertTrue(Validator::isDecimal(2.00));
        $this->assertTrue(Validator::isDecimal(1.01));
    }

    public function testIsDecimalInvalid(): void {
        $this->assertFalse(Validator::isDecimal(1.00));
        $this->assertFalse(Validator::isDecimal(0.50));
        $this->assertFalse(Validator::isDecimal(-255));
        $this->assertFalse(Validator::isDecimal(-1.5));
    }
}
