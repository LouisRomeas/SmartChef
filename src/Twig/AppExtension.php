<?php

namespace App\Twig;

use DateTime;
use Twig\TwigFilter;
use Twig\Environment;
use DateTimeInterface;
use Twig\TwigFunction;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extension\AbstractExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private TranslationExtension $translationExtension,
        private IntlExtension $intlExtension,
    ) {}

    public function getFilters()
    {
        return [
            new TwigFilter('t', [$this->translationExtension, 'trans']),
            new TwigFilter('format_big_number', [$this, 'formatBigNumber']),
            new TwigFilter('format_date_auto', [$this, 'formatDate'], [ 'needs_environment' => true ]),
            new TwigFilter('format_duration', [$this, 'formatDuration'], [ 'needs_environment' => true ]),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_env', [$this, 'getEnvironmentVariable']),
        ];
    }

    // Custom filters & functions

    /**
     * Format a number to a specific set of rules (similar to Reddit's way of shortening big scores)
     * - Below 1000, just output the number
     * - After 1000, the number is divided by 1000 until it's smaller than 1000
     * - Then, it is rounded to an integer if smaller than 100, else to 1 decimal
     * - Then, a suffix is applied depending on the amount of times it had to be divided
     * 
     * Examples : [ 1248 => "1.2k", 23647859 => "23.6k", 325615 => "325k" ]
     */
    public function formatBigNumber(int $score) {
        $suffixes = ['', 'k', 'M', 'B'];
        $suffixPointer = 0;
        while ($score >= 1000 && $suffixPointer < count($suffixes)) {
            $score /= 1000;
            $suffixPointer++;
        }

        return $this->intlExtension->formatNumber($score, [
            'fraction_digit' => ( ($score >= 100 || $suffixPointer == 0) ? 0 : 1)
        ]) . $suffixes[$suffixPointer];
    }

    /**
     * Format a DateTime depending on how close it was from now
     * - If the DateTime was today, it outputs the time
     * - If it was before today, it outputs the date
     */
    public function formatDate(
        Environment $environment,
        DateTimeInterface $datetime,
        $timezone = null

    ) {
        $format = ( $datetime < (new DateTime('today')) ) ? 'd/m/Y' : 'H:i';

        $datetimeString = twig_date_format_filter($environment, $datetime, $format, $timezone);

        return $datetimeString;
    }

    /**
     * Format a duration depending on its length
     * - If the duration is less than an hour long, show minutes
     * - If the duration is more than an hour long, show hours & minutes
     * - Then, in all cases, remove leading zeroes
     */
    public function formatDuration(
        Environment $environment,
        DateTimeInterface $duration
    ) {
        $smallerThanAnHour = ( $duration->format('G') == '0' );
        $format = $smallerThanAnHour ? "i\m\i\\n" : "H\hi\m";


        $datetimeString = twig_date_format_filter($environment, $duration, $format);

        return preg_replace("/^0+([0-9])/", '$1', $datetimeString);
    }

    /**
     * Retrieves an environment variable for use in Twig
     * (used in order to get admin mail)
     */
    public function getEnvironmentVariable($varname)
    {
        return $_ENV[$varname];
    }
}