<?php

namespace TradeAppOne\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;

class I18nRequestHandler
{

    private $app;
    private $config;

    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = $app->config;
    }

    public function handle($request, Closure $next)
    {
        $locale             = $request->header('Content-Language');
        $supportedLanguages = $this->config->get('app.supported_languages');

        if ($locale == null) {
            $locale = $this->config->get('app.locale');
        }

        if (array_key_exists($locale, $supportedLanguages) == false) {
            return abort(403, trans('messages.language_not_supported'));
        }

        $this->app->setLocale($locale);
        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        return $response;
    }
}
