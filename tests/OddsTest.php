<?php
namespace OddsPHP\Tests;

use OddsPHP\Odds;
use PHPUnit\Framework\TestCase;

/*
print "1800.00 to decimal:\n".$odd->set('decimal', 1800.00)->get('decimal')."\n".PHP_EOL;

// Converting from decimal to fractional.
print "1.55 to fractional:\n".$odd->set('decimal', 1.55)->get('fractional')."\n".PHP_EOL;

// Convert a decimal to fractional
print "5.50 to fractional:\n".$odd->set('decimal', 5.50)->get('fractional')."\n".PHP_EOL;

// Reducing a fraction
print "Reduce 44/100:\n".$odd->set('fractional', '44/100')->reduce()."\n".PHP_EOL;

// Reducing a fraction
print "Calculate implied probability:\n".$odd->set('fractional', '44/100')->get('implied')."%\n".PHP_EOL;

// Converting fractional to decimal.
print "11/20 to decimal:\n".$odd->set('fractional', '11/20')->get('decimal')."\n".PHP_EOL;
print "22/40 to decimal:\n".$odd->set('fractional', '22/40')->get('decimal')."\n".PHP_EOL; // Fractions are automatically reduced.

// Converting moneyline to decimal.
print "-500 to decimal:\n".$odd->set('moneyline', '-500')->get('decimal')."\n".PHP_EOL;
print "125 to decimal:\n".$odd->set('moneyline', '125')->get('decimal')."\n".PHP_EOL;

// Fractional to moneyline
print "11/20 to moneyline: ".$odd->set('fractional', '11/20')->get('moneyline')."\n".PHP_EOL;
exit("Test completed!");
?>
*/

class OddsTest extends TestCase {
    public function testDecimal(): void {
        $this->assertEquals(1800.00, (new Odds())->setDecimal(1800.00)->getDecimal());
        $this->assertEquals(50.0, (new Odds())->setFractional("49/1")->getDecimal());
        $this->assertEquals(5.25, (new Odds())->setHongKong(4.25)->getDecimal());
        $this->assertEquals(2.75, (new Odds())->setMoneyline(175)->getDecimal());
    }

    public function testDecimalPlaces(): void {
        $odds = new Odds();

        // Default value is 2
        $this->assertEquals(2, $odds->getDecimalPlaces());

        // Set valid value
        $odds->setDecimalPlaces(4);
        $this->assertEquals(4, $odds->getDecimalPlaces());

        // Zero is valid
        $odds->setDecimalPlaces(0);
        $this->assertEquals(0, $odds->getDecimalPlaces());

        // Negative value throws exception
        $this->expectException(\InvalidArgumentException::class);
        $odds->setDecimalPlaces(-1);

        $odds->setDecimal(1.23456789);

        $odds->setDecimalPlaces(2);
        $this->assertEquals(1.23, $odds->getDecimal());

        $odds->setDecimalPlaces(4);
        $this->assertEquals(1.2346, $odds->getDecimal());

        $odds->setDecimalPlaces(0);
        $this->assertEquals(1, $odds->getDecimal());
        $this->assertEquals(1.0, $odds->getDecimal());
    }

    public function testHongKong(): void {
        $odds = new Odds();

        $this->assertEquals(0.55, $odds->setDecimal(1.55)->getHongKong());
        $this->assertEquals(0.75, $odds->setHongKong(0.75)->getHongKong());
        $this->assertEquals(0.55, $odds->setFractional('11/20')->getHongKong());
        $this->assertEquals(1.50, $odds->setMoneyline(150)->getHongKong());
        $this->assertEquals(0.25, $odds->setMoneyline(-400)->getHongKong());
        $this->assertEquals(0.25, $odds->setImpliedProbability(80.0)->getHongKong());
    }

    public function testReduceFractions(): void {
        $odds = new Odds();

        $this->assertEquals('1/2', $odds->setFractional('1/2')->getFractional());
        $this->assertEquals('11/20', $odds->setFractional('22/40')->getFractional());
        $this->assertEquals('1/1', $odds->setFractional('1/1')->getFractional());
        $this->assertEquals('7/2', $odds->setFractional('14/4')->getFractional());
        $this->assertEquals('2/1', $odds->setFractional('2/1')->getFractional());
    }
}
