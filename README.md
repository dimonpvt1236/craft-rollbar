# Craft 3 Rollbar Plugin

> Report Craft errors and exceptions to Rollbar.

## Content-Security-Policy

`config/content-security-policy.php`

```php
<?php

use craft\helpers\UrlHelper;

/**
 * Reference:
 *
 *   *                    Wildcard, allows any URL except data: blob: filesystem: schemes
 *   'none'               Prevents loading resources from any source
 *   'self'               Allows loading resources from the same origin (same scheme, host and port)
 *   data:                Allows loading resources via the data scheme (eg Base64 encoded images)
 *   https:               Allows loading resources only over HTTPS on any domain
 *   domain.example.com   Allows loading resources from the specified domain name
 *   *.example.com        Allows loading resources from any subdomain under example.com
 *   https://cdn.com      Allows loading resources only over HTTPS matching the given domain
 *   'unsafe-inline'      Allows use of inline source elements such as style attribute, onclick, or
 *                        script tag bodies (depends on the context of the source it is applied to)
 *   'unsafe-eval'        Allows unsafe dynamic code evaluation such as JavaScript eval()
 *
 */

return [
    '*' => [
        'reportOnly' => false,
        'policies' => [

// The default-src is the default policy for loading content such as JavaScript, Images,
            // CSS, Font's, AJAX requests, Frames, HTML5 Media.
            'default-src' => [
                'https:',
            ],

            // Defines valid sources for web wokers and nested browsing contexts loaded using elements
            // such as <frame> and <iframe>
            'child-src' => [
                'https:',
                "'self'",
            ],

            // Applies to XMLHttpRequest (AJAX), WebSocket or EventSource. If not allowed the browser
            // emulates a 400 HTTP status code.
            'connect-src' => [
                'https:',
                "'self'",
            ],

            // Defines valid sources of fonts.
            'font-src' => [
                'https:',
                'data:'
            ],

            // Defines valid sources that can be used as a HTML <form> action.
            'form-action' => [
                "'self'",
            ],

            // Defines valid sources for embedding the resource using <frame> <iframe> <object> <embed>
            // <applet>. Setting this directive to 'none' should be roughly equivalent to
            // X-Frame-Options: DENY
            'frame-ancestors' => [
                "'none'",
            ],

            // Defines valid sources of images.
            'img-src' => [
                'https:',
                'data:',
                'blob:',
            ],

            // Defines valid sources of audio and video, eg HTML5 <audio>, <video> elements.
            'media-src' => [

            ],

            // Defines valid sources of plugins, eg <object>, <embed> or <applet>.
            'object-src' => [
                'https:',
            ],

            // Defines valid MIME types for plugins invoked via <object> and <embed>. To load an
            // <applet> you must specify application/x-java-applet.
            'plugin-types' => [
                // 'application/pdf'
            ],

            // Defines valid sources of JavaScript.
            'script-src' => [
                'https:',
                'data:', // needed for disqus
                "'unsafe-inline'",
                "'unsafe-eval'", // needed for disqus
            ],

            // Defines valid sources of stylesheets.
            'style-src' => [
                'https:',
                "'self'",
                "'unsafe-inline'",
            ],

            // Instructs the browser to POST a reports of policy failures to this URI. You can also
            // append -Report-Only to the HTTP header name to instruct the browser to only send reports
            // (does not block anything).
            'report-uri' => [
               UrlHelper::actionUrl('rollbar/csp/report'),
            ],
        ],
    ],
    '.local' => [
        'reportOnly' => true,
    ],
];
```