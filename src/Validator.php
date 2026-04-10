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

class Validator{
	public static function isDecimal(float $value): bool {
        return $value > 1.0;
	}

	public static function isFractional(string $value): bool {
		return (bool) preg_match('/^[1-9]\d*\/[1-9]\d*$/', $value);
	}
	
	public static function isHongKong(float $value): bool {
		return $value > 0.0;
	}

	public static function isImpliedProbability(float $value): bool {
		return $value > 0.0 && $value < 100.0;
	}

	public static function isMalay(float $value): bool {
		return ($value > 0.0 && $value <= 1.0) || ($value < 0.0 && $value >= -1.0);
	}

	public static function isMoneyline(int $value): bool {
		return $value >= 100 || $value <= -100;
	}
}
