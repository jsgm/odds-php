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
    case InvalidHongKong = 'Value must be greater than 0 (e.g. 0.55).';
    case InvalidFractional = 'Expected format: numerator/denominator (e.g. 2/1).';
    case InvalidMoneyline = 'Value must be 100 or greater, or -100 or less (e.g. 150 or -200).';
    case InvalidDecimalPlaces = 'Decimal places must be 0 or greater.';
}

class Odds{
   private ?float $decimal = null;
   private int $decimalPlaces = 2;

	public function setDecimalPlaces(int $places=0): void{
       	if ($places < 0) {
    		throw new \InvalidArgumentException(OddsErrors::InvalidDecimalPlaces->value);
		}

    	$this->decimalPlaces = $places;
	}

   	public function getDecimalPlaces(): int{
		return $this->decimalPlaces;
   	}

    public function setDecimal(float $value): static{
        if(!Validator::isDecimal($value)){
        	throw new \InvalidArgumentException(OddsErrors::InvalidDecimal->value);
		}

		$this->decimal = $value;
		return $this;
    }

    public function setHongKong(float $value): static{
        if(!Validator::isHongKong($value)){
        	throw new \InvalidArgumentException(OddsErrors::InvalidHongKong->value);
		}

		$this->decimal = $value + 1;
		return $this;
    }

    public function setFractional(string $value): static{
        if(!Validator::isFractional($value)){
        	throw new \InvalidArgumentException(OddsErrors::InvalidFractional->value);
		}

        $this->decimal = $this->fractionalToDecimal($value);
		return $this;
    }

    public function setMoneyline($odd=NULL): static{
        if(!Validator::isMoneyline($odd)){
        	throw new \InvalidArgumentException(OddsErrors::InvalidMoneyline->value);
		}

        $this->decimal = $this->moneylineToDecimal($odd);
		return $this;
	}

	public function setImpliedProbability($odd): static{
        $this->decimal=$this->moneylineToDecimal($odd);
		return $this;
	}

	public function getHongKong(): float {
		$this->requireBaseOdd();
		return round($this->decimal - 1, $this->getDecimalPlaces());
	}

	public function getDecimal(): float{
		$this->requireBaseOdd();
		return $this->decimal;
	}

	public function getMoneyline(){
        $this->requireBaseOdd();
		return (float)round($this->decimalToMoneyline($this->decimal));
	}
	
	public function getFractional(): string{
		$this->requireBaseOdd(); 
		return $this->decimalToFraction($this->decimal);
	}

	public function getImpliedProbability(){
        $this->requireBaseOdd();
		return $this->decimalToImpliedProbability($this->decimal);
	}

	private function decimalToImpliedProbability($decimal){
		if(Validator::isDecimal($value)){
			return round(1/(float)$decimal*100, $this->getDecimalPlaces());	
		}
		return false;
	}

	private function decimalToMoneyline(float $decimal): float {
		if ($decimal >= 2.00) {
			return ($decimal - 1) * 100;
		}

		return (-100) / ($decimal - 1);
	}
	
	private function decimalToFraction(float $value){
		if(!Validator::isDecimal($value)){
			return false;
		}

		$dec = number_format($value, $this->getDecimalPlaces());
        $reduced = $this->reduceFraction(round(($dec-1)*100), round(100));
        return $reduced[0]."/".$reduced[1];
	}
	
	private function fractionalToDecimal(string $fractional): float{
		$fraction = explode("/", $fractional);
		return $fraction[0]/$fraction[1]+1.00;
	}

	private function moneylineToDecimal($moneyline){
		if(Validator::isMoneyline($moneyline)){
			if($moneyline>0){
				return $moneyline/100+1;
			}else{
				return abs($moneyline)/100+1;
			}
		}
		return false;
	}

    private function hasDecimalPart(float $value): bool{
        return floor($value) != $value;
    }

	private function reduceFraction(int $a, int $b): array {
		$gcd = $this->gcd($a, $b);
		return [$a / $gcd, $b / $gcd];
	}

	private function gcd(int $a, int $b): int {
		return ($a % $b) ? $this->gcd($b, $a % $b) : $b;
	}

	private function parse_float($value=0.0){
		return floatval(preg_replace('/\.(?=.*\.)/', '', str_replace(",", ".", $value)));
	}

	private function requireBaseOdd(): void{
		if($this->decimal === null){
			throw new \BadMethodCallException(OddsErrors::NoOddSet->value);
		}
	}
	
	public function __toString(): string {
	    //return number_format($this->decimal, $this->decimalPlaces);
	}
}
