<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 03/11/2017
 * Time: 11:10
 */

namespace CranleighSchool\CranleighPeople;

use Exception;

class Helper {

	public static $phoneNumber;

	public static function santitizePhoneHref( string $number = null ) {
		if ($number === null) return false;

		if ( self::phoneStartsWith("+", $number) ) {
			return $number;
		} else {
			$number = preg_replace('/\D/', '', $number);
			return "+44".substr($number, 1);
		}

	}

	public static function sanitizePhoneDisplay( string $number = null ) {
		if ($number === null) return false;

		$number = preg_replace('/\D/', '', $number);

		self::$phoneNumber = $number;

		if ( self::phoneStartsWith(44) ) {
			$number = "0".substr($number,2);
			self::$phoneNumber = $number;

		}
		if ( !self::phoneStartsWith("0")) {
			$number = "0".$number;
			self::$phoneNumber = $number;

		}

		if (strlen($number)==11) {
			if ( self::phoneStartsWith( "01" ) || self::phoneStartsWith( "07" ) ) {
				return preg_replace( "/([0-9]{5})([0-9]{6})/", "$1 $2", $number );
			} elseif ( self::phoneStartsWith( "02" ) ) {
				// EG: 020 8811 8181
				return preg_replace( "/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1 $2 $3", $number );
			} elseif ( self::phoneStartsWith( "08" ) || self::phoneStartsWith( "09" ) ) {
				return preg_replace( "/([0-9]{4})([0-9]{3})([0-9]{4})/", "$1 $2 $3", $number );
			}
		} elseif (strlen($number) == 10) {
			if ( self::phoneStartsWith(["0500", "0800"])) {
				return preg_replace("/([0-9]{4})([0-9]{6})/", "$1 $2", $number);
			}
		} elseif (strlen($number)==8 && self::phoneStartsWith("0800")) {
			return preg_replace("/([0-9]{4})([0-9]{4})/", "$1 $2", $number);
		}

		return $number;
	}

	public static function phoneStartsWith($startsWith, $phoneNumber=null) {

		if ($phoneNumber===null) {
			$phoneNumber = self::$phoneNumber;
		}

		if (is_array($startsWith)) {
			$matchLength = strlen($startsWith[0]);
			foreach ($startsWith as $item) {
				if (strlen($item) !== $matchLength) {
					throw new Exception("String lengths to check against are differing lengths. Trying to match: ".print_r($startsWith, true));
					break;
				}
			}
		}

		if (is_array($startsWith)) {
			if (in_array(substr($phoneNumber, 0, $matchLength), $startsWith)) {
				return true;
			}
		} elseif (substr($phoneNumber, 0, strlen($startsWith)) == $startsWith) {
			return true;
		}

		return false;
	}

	public static function santitizePositions( $positions, $not = null ) {

		if ( is_array( $positions ) ):
			foreach ( $positions as $position ):
				if ( $position == $not ):
					continue;
				endif;
				break;;
			endforeach;

			return $position;
		endif;

		return false;

	}
}
