<?php
/*
 * @package   OddsPHP/Odds
 * @author    @jsgm (Github)
 * @license   MIT
 * @since     08-02-2020
 * @updated   10-04-2026
 *
 */

namespace OddsPHP;

enum OddsErrors: string {
    case NoOddSet = 'No odd has been set yet.';

    case InvalidDecimal = 'Value must be greater than 1.00 (e.g. 1.55).';
    case InvalidFractional = 'Expected format: numerator/denominator (e.g. 2/1).';
    case InvalidHongKong = 'Value must be greater than 0 (e.g. 0.55).';
	case InvalidImpliedProbability = 'Implied probability must be greater than 0 and less than 100 (e.g. 55.5).';
	case InvalidIndonesian = 'Value must be 1.00 or greater, or -1.00 or less (e.g. 1.50 or -2.00).';
	case InvalidMalay = 'Value must be between 0.01 and 1.00, or between -1.00 and -0.01 (e.g. 0.75 or -0.50).';
    case InvalidMoneyline = 'Value must be 100 or greater, or -100 or less (e.g. 150 or -200).';

    case InvalidDecimalPlaces = 'Decimal places must be 0 or greater.';
}

class Odds{
   	private ?float $decimal = null;
   	private int $decimalPlaces = 2;
   	private RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero;
	
	public function setAmerican(int $value): static {
		return $this->setMoneyline($value);
	}

	public function getAmerican(): int {
		return $this->getMoneyline();
	}

    public function setDecimal(float $value): static{
        if(!Validator::isDecimal($value)){
        	throw new \InvalidArgumentException(OddsErrors::InvalidDecimal->value);
		}

		$this->decimal = $value;
		return $this;
    }

	public function getDecimal(): float{
		$this->requireBaseOdd();
		return round($this->decimal, $this->getDecimalPlaces(), $this->getRoundingMode());
	}


    public function setFractional(string $value): static{
        if(!Validator::isFractional($value)){
        	throw new \InvalidArgumentException(OddsErrors::InvalidFractional->value);
		}

    	[$numerator, $denominator] = explode('/', $value);
    	$this->decimal = (int)$numerator / (int)$denominator + 1;
		return $this;
    }

	public function getFractional(): string{
		$this->requireBaseOdd();

		[$numerator, $denominator] = $this->reduceFraction((int)(($this->decimal - 1) * 100), 100);
    	return "{$numerator}/{$denominator}";
	}

    public function setHongKong(float $value): static{
        if(!Validator::isHongKong($value)){
        	throw new \InvalidArgumentException(OddsErrors::InvalidHongKong->value);
		}

		$this->decimal = $value + 1;
		return $this;
    }

	public function getHongKong(): float {
		$this->requireBaseOdd();
		return round($this->decimal - 1, $this->getDecimalPlaces(), $this->getRoundingMode());
	}

	public function setImpliedProbability(float $value): static{
		if (!Validator::isImpliedProbability($value)) {
			throw new \InvalidArgumentException(OddsErrors::InvalidImpliedProbability->value);
		}

        $this->decimal = 100 / $value;
		return $this;
	}

	public function getImpliedProbability(): float{
        $this->requireBaseOdd();
		
		return round(1 / $this->decimal * 100, $this->getDecimalPlaces(), $this->getRoundingMode());	
	}

	public function getIndonesian(): float {
		$this->requireBaseOdd();

		if ($this->decimal >= 2.00) {
			return round($this->decimal - 1, $this->decimalPlaces, $this->roundingMode);
		}

		return round(-1 / ($this->decimal - 1), $this->decimalPlaces, $this->roundingMode);
	}

	public function setMalay(float $value): static {
		if (!Validator::isMalay($value)) {
			throw new \InvalidArgumentException(OddsErrors::InvalidMalay->value);
		}

		$this->decimal = $value > 0 ? $value + 1 : 1 - (1 / $value);
		return $this;
	}
	
	public function getMalay(): float {
		$this->requireBaseOdd();

		if ($this->decimal <= 2.00) {
			return round($this->decimal - 1, $this->decimalPlaces, $this->roundingMode);
		}

		return round(-1 / ($this->decimal - 1), $this->decimalPlaces, $this->roundingMode);
	}


    public function setMoneyline(int $value): static{
        if(!Validator::isMoneyline($value)){
        	throw new \InvalidArgumentException(OddsErrors::InvalidMoneyline->value);
		}

        $this->decimal = (abs($value) / 100) + 1;
		return $this;
	}

	public function getMoneyline(): int{
        $this->requireBaseOdd();
		
		if ($this->decimal >= 2.00) {
			return (int) round(($this->decimal - 1) * 100);
		}

		return (int) round((-100) / ($this->decimal - 1));
	}

	public function getRoundingMode(): RoundingMode{
		return $this->roundingMode;
	}

	public function setRoundingMode(RoundingMode $mode): void {
		$this->roundingMode = $mode;
	}

	public function setDecimalPlaces(int $places=0): void{
       	if ($places < 0) {
    		throw new \InvalidArgumentException(OddsErrors::InvalidDecimalPlaces->value);
		}

    	$this->decimalPlaces = $places;
	}

   	public function getDecimalPlaces(): int{
		return $this->decimalPlaces;
   	}

	private function reduceFraction(int $a, int $b): array {
		$gcd = $this->gcd($a, $b);
		return [$a / $gcd, $b / $gcd];
	}

	private function requireBaseOdd(): void{
		if($this->decimal === null){
			throw new \BadMethodCallException(OddsErrors::NoOddSet->value);
		}
	}

	private function gcd(int $a, int $b): int {
		return ($a % $b) ? $this->gcd($b, $a % $b) : $b;
	}
}
