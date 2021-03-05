<?php
/**
 * String helper functions.
 */

/**
 * String helper class.
 */
class RWMB_Helpers_String
{
    /**
     * Convert text to Title_Case.
     *
     * @param  string $text Input text.
     * @return string
     */
    public static function title_case($text)
    {
        $text = str_replace(['-', '_'], ' ', $text);
        $text = ucwords($text);
        $text = str_replace(' ', '_', $text);

        return $text;
    }
}
