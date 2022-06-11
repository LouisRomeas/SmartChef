<?php

namespace App\Twig;

use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Twig\Extension\AbstractExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
  public function __construct(private TranslationExtension $translationExtension, private IntlExtension $intlExtension)
  {
    
  }

    public function getFilters()
    {
        return [
            new TwigFilter('t', [$this->translationExtension, 'trans']),
            new TwigFilter('format_score', [$this, 'formatScore'])
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
}