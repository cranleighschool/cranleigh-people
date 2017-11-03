<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 03/11/2017
 * Time: 11:10
 */

namespace CranleighSchool\CranleighPeople;


class Helper {

	public static function santitizePhoneHref( $number ) {

		if ( substr( $number, 0, 1 ) == "+" ) {
			return $number;
		} else {
			$str = str_replace( "01483", "+441483", $number );
			$str = str_replace( " ", "", $str );

			return $str;
		}

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
