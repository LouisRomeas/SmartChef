<?php

namespace App\Twig;

use DateTime;
use Twig\TwigFilter;
use Twig\Environment;
use DateTimeInterface;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extension\AbstractExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private TranslationExtension $translationExtension,
        private IntlExtension $intlExtension,
    )
    {
        
    }

    public function getFilters()
    {
        return [
            new TwigFilter('t', [$this->translationExtension, 'trans']),
            new TwigFilter('format_big_number', [$this, 'formatBigNumber']),
            new TwigFilter('format_date_auto', [$this, 'formatDate'], [ 'needs_environment' => true ]),
            new TwigFilter('format_duration', [$this, 'formatDuration'], [ 'needs_environment' => true ]),
        ];
    }

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

    public function formatDate(
        Environment $environment,
        DateTimeInterface $datetime,
        $timezone = null

    ) {
        $format = ( $datetime < (new DateTime('today')) ) ? 'd/m/Y' : 'H:i';

        $datetimeString = twig_date_format_filter($environment, $datetime, $format, $timezone);

        return $datetimeString;
    }

    public function formatDuration(
        Environment $environment,
        DateTimeInterface $duration
    ) {
        $smallerThanAnHour = ( $duration->format('G') == '0' );
        $format = $smallerThanAnHour ? "i\m\i\\n" : "H\hi\m";


        $datetimeString = twig_date_format_filter($environment, $duration, $format);

        return preg_replace("/^0+/", '', $datetimeString);
    }
}