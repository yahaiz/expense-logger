<?php
/**
 * Persian/Jalali Date Converter
 * Converts Gregorian dates to Persian/Jalali dates
 */

class JalaliDate {
    /**
     * Convert Gregorian date to Jalali
     */
    public static function toJalali($g_y, $g_m, $g_d) {
        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + floor(($gy + 3) / 4) - floor(($gy + 99) / 100) + floor(($gy + 399) / 400);

        for ($i = 0; $i < $gm; ++$i)
            $g_day_no += self::g_days_in_month($i, $gy + 1600);
        $g_day_no += $gd;

        $j_day_no = $g_day_no - 79;

        $j_np = floor($j_day_no / 12053);
        $j_day_no = $j_day_no % 12053;

        $jy = 979 + 33 * $j_np + 4 * floor($j_day_no / 1461);

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += floor(($j_day_no - 1) / 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        $j_all_days = $j_day_no + 1;

        for ($i = 0; $i < 11 && $j_day_no >= self::j_days_in_month($i, $jy); ++$i)
            $j_day_no -= self::j_days_in_month($i, $jy);
        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return array($jy, $jm, $jd);
    }

    /**
     * Get days in Gregorian month
     */
    private static function g_days_in_month($month, $year) {
        $days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        if ($month == 1 && (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0)))
            return 29;
        return $days[$month];
    }

    /**
     * Get days in Jalali month
     */
    private static function j_days_in_month($month, $year) {
        if ($month < 6)
            return 31;
        if ($month < 11)
            return 30;
        if (self::isLeapJalaliYear($year))
            return 30;
        return 29;
    }

    /**
     * Check if Jalali year is leap
     */
    private static function isLeapJalaliYear($year) {
        $a = 0.025;
        $b = 266;
        if ($year > 0) {
            $c = $year - 474;
        } else {
            $c = 473;
        }
        $mod = fmod($c, 2820) + 474;
        if (fmod($mod + 38 + floor($a * $mod) - $b, 33) < 1)
            return true;
        return false;
    }

    /**
     * Format Jalali date
     * Formats: 'Y-m-d', 'Y/m/d', 'd F Y', 'F Y', 'd M Y', 'M d'
     */
    public static function format($format, $timestamp = null) {
        if ($timestamp === null) {
            $timestamp = time();
        }

        list($jy, $jm, $jd) = self::toJalali(
            (int)date('Y', $timestamp),
            (int)date('m', $timestamp),
            (int)date('d', $timestamp)
        );

        $monthNames = [
            1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
            'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        ];

        $monthNamesShort = [
            1 => 'فرو', 'ارد', 'خرد', 'تیر', 'مرد', 'شهر',
            'مهر', 'آبان', 'آذر', 'دی', 'بهم', 'اسف'
        ];

        $replacements = [
            'Y' => $jy,
            'm' => str_pad($jm, 2, '0', STR_PAD_LEFT),
            'd' => str_pad($jd, 2, '0', STR_PAD_LEFT),
            'F' => $monthNames[$jm],
            'M' => $monthNamesShort[$jm],
            'j' => $jd,
            'n' => $jm,
        ];

        $result = $format;
        foreach ($replacements as $key => $value) {
            $result = str_replace($key, $value, $result);
        }

        return $result;
    }

    /**
     * Convert Jalali to Gregorian
     */
    public static function toGregorian($j_y, $j_m, $j_d) {
        $jy = $j_y - 979;
        $jm = $j_m - 1;
        $jd = $j_d - 1;

        $j_day_no = 365 * $jy + floor($jy / 33) * 8 + floor(($jy % 33 + 3) / 4);
        for ($i = 0; $i < $jm; ++$i)
            $j_day_no += self::j_days_in_month($i, $j_y);

        $j_day_no += $jd;

        $g_day_no = $j_day_no + 79;

        $gy = 1600 + 400 * floor($g_day_no / 146097);
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100 * floor($g_day_no / 36524);
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
                $g_day_no++;
            $leap = false;
        }

        $gy += 4 * floor($g_day_no / 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += floor($g_day_no / 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= self::g_days_in_month($i, $gy) && $i < 11; $i++)
            $g_day_no -= self::g_days_in_month($i, $gy);
        $gm = $i + 1;
        $gd = $g_day_no + 1;

        return array($gy, $gm, $gd);
    }
}

/**
 * Helper function to format Jalali date
 */
function jdate($format, $timestamp = null) {
    return JalaliDate::format($format, $timestamp);
}
