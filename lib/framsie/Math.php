<?php
/**
 * This class provides quick access to common algorithms
 * @package Framsie
 * @subpackage FramsieMath
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieMath {

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method divides $iDivisor by 100 then multiplies that result by $iMultiplicative
	 * @package Framsie
	 * @subpackage FramsieMath
	 * @access public
	 * @static
	 * @param float $iDivisor
	 * @param float $iMultiplicative
	 * @return float
	 */
	public static function DivideByOneHundredThemMultiply($iDivisor, $iMultiplicative) {
		// Check the divisor
		if ($iDivisor === 0) {
			// Cannot divide zero so return
			return 0;
		}
		// Returnthe calculated value
		return (($iDivisor / 100) * $iMultiplicative);
	}

	/**
	 * This method divides 100 by $iDividend then multiplies that result by $iMultiplicative
	 * @package Framsie
	 * @subpackage FramsieMath
	 * @access public
	 * @static
	 * @param float $iDividend
	 * @param float $iMultiplicative
	 * @return float
	 */
	public static function OneHundredDividedByThenMultipliedBy($iDividend, $iMultiplicative) {
		// Check the dividend
		if ($iDividend === 0) {
			// Cannot divide by zero, so return
			return 0;
		}
		// Return the calculated value
		return ((100 / $iDividend) * $iMultiplicative);
	}
}