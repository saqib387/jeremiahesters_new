<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HtmlMinifier extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!$this->canRewriteResponse($response)) {
            return $response;
        }

        $actionName = $request->route()->getActionMethod();
        if (in_array($actionName, [/*Skipped routes*/]) || config('minify.config.enable_html_min') == false) {
            return $response;
        }

        $buffer = $response->getContent();
        if (strpos($buffer, '<pre>') !== false) {
            $replace = [
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\r/"                      => '',
                "/>\n</"                    => '><',
                "/>\s+\n</"                 => '><',
                "/>\n\s+</"                 => '><',
            ];
        } else {
            $replace = [
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\n([\S])/"                => '$1',
                "/\r/"                      => '',
                "/\n/"                      => '',
                "/\t/"                      => '',
                '/ +/'                      => ' ',
            ];
        }

        $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
        $response->setContent($buffer);

        return $response;
    }

    private function canRewriteResponse($response): bool
    {
        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            return false;
        }

        if (!method_exists($response, 'getContent') || !method_exists($response, 'setContent')) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type');

        return !$contentType || stripos($contentType, 'text/html') !== false;
    }
}
