<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ACM_Utils {

    public static function format_metric( $value, $format, $decimals = 0 ) {
        if ( empty( $value ) && $value !== '0' ) return $value;

        switch ( $format ) {
            case 'number': return number_format_i18n( (float) $value, $decimals );
            case 'money': return '$ ' . number_format_i18n( (float) $value, $decimals );
            case 'percent': return number_format_i18n( (float) $value, $decimals ) . '%';
            case 'compact': return self::format_compact_number( (float) $value, $decimals );
            case 'money_compact': return '$ ' . self::format_compact_number( (float) $value, $decimals );
            case 'weight': return self::format_weight( (float) $value, $decimals );
            case 'date': 
                $timestamp = strtotime( $value );
                return $timestamp ? date_i18n( get_option( 'date_format' ), $timestamp ) : $value;
            case 'raw':
            default: return $value;
        }
    }

    private static function format_compact_number( $n, $decimals ) {
        if ( $n < 1000 ) return number_format_i18n( $n, $decimals );
        $suffix = [ '', 'k', 'M', 'B', 'T' ];
        $power  = floor( log( $n, 1000 ) );
        if ( $power >= count( $suffix ) ) $power = count( $suffix ) - 1;
        return number_format_i18n( round( $n / pow( 1000, $power ), $decimals ), $decimals ) . $suffix[ $power ];
    }

    private static function format_weight( $g, $decimals ) {
        $suffixes = [ 'g', 'kg', 't' ];
        if ( $g <= 0 ) return '0 g';
        $power = floor( log( $g, 1000 ) );
        if ( $power >= count( $suffixes ) ) $power = count( $suffixes ) - 1;
        if ( $power < 0 ) $power = 0;
        $number = $g / pow( 1000, $power );
        return number_format_i18n( round( $number, $decimals ), $decimals ) . ' ' . $suffixes[ $power ];
    }
}