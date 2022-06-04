<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleRoutingListener
{
  public function onKernelRequest(RequestEvent $event)
  {
      $request = $event->getRequest();
  
      // some logic to determine the $locale
      $locale = $request->getPreferredLanguage(['fr', 'en']);
      $request->setLocale($locale);
  }
}