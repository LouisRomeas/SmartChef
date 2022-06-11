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
            new TwigFilter('format_score', [$this, 'formatScore']),
            new TwigFilter('format_date_auto', [$this, 'formatDate'], [ 'needs_environment' => true ]),
        ];
    }

    public function formatScore(int $score) {
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
}