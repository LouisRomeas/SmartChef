<?php

namespace App\Twig;

use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
  public function __construct(private TranslationExtension $translationExtension)
  {
    
  }

    public function getFilters()
    {
        return [
            new TwigFilter('t', [$this->translationExtension, 'trans']),
        ];
    }
}