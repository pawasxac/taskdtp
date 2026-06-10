<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script>
            window.addEventListener('error', function(e) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/api/log-error', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify({
                    type: 'error',
                    message: e.message,
                    source: e.filename,
                    line: e.lineno,
                    col: e.colno,
                    error: e.error ? e.error.stack : null
                }));
            });
            window.addEventListener('unhandledrejection', function(e) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/api/log-error', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify({
                    type: 'unhandledrejection',
                    message: 'Unhandled Promise Rejection: ' + e.reason,
                    error: e.reason ? e.reason.stack : null
                }));
            });
</script>
        <title inertia>{{ config('app.name', 'NGOPI') }}</title>

        <!-- Fonts & Styling -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
        <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700,900&display=swap" rel="stylesheet">

        @viteReactRefresh
        @vite(['resources/js/app.jsx', 'resources/css/app.css'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-alabaster text-espresso">
        @inertia
    </body>
</html>
