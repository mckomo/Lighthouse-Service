<?php namespace Lighthouse\Services\Torrents\Validation\Utils;


class ValidationHelper {

    /**
     * @param string $hashCandidate
     * @return bool
     */
    static function isHash($hashCandidate)
    {
        return preg_match('/^[a-fA-F\d]{40}$/', $hashCandidate);
    }

    /**
     * @param string $string
     * @param int $bound
     * @return bool
     */
    public static function isLongerThan($string, $bound = 0)
    {
        return !is_null($string) && strlen($string) > $bound;
    }

    /**
     * @param integer $size
     * @return bool
     */
    public static function isPositiveInteger($number)
    {
        return is_integer($number) && $number > 0;
    }

    /**
     * @param integer $number
     * @return bool
     */
    public static function isNegativeInteger($number)
    {
        return is_integer($number) && $number < 0;
    }

    /**
     * @param string $url
     * @return bool
     */
    public static function isUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param string $date
     * @return bool
     */
    public static function isIso8601Utc($date)
    {
        return preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}Z$/', $date);
    }

    /**
     * @param float $number
     * @param float $lb
     * @param float $rb
     * @return bool
     */
    public static function isInRange($number, $lb, $rb)
    {
//        var_dump($number, $lb, $rb);
        return $number >= $lb && $number <= $rb;
    }

}