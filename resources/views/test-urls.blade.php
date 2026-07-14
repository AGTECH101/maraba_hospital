<!DOCTYPE html>
<html>
<head>
    <title>Asset URL Test</title>
</head>
<body>
    <h2>Generated Asset URLs</h2>
    
    <div style="background: #f0f0f0; padding: 20px; font-family: monospace;">
        <p><strong>CSS Bootstrap:</strong> {{ asset('css/bootstrap.min.css') }}</p>
        <p><strong>CSS Style:</strong> {{ asset('css/style.css') }}</p>
        <p><strong>Lib Animate:</strong> {{ asset('lib/animate/animate.min.css') }}</p>
        <p><strong>JS WOW:</strong> {{ asset('lib/wow/wow.min.js') }}</p>
        <p><strong>JS Main:</strong> {{ asset('js/main.js') }}</p>
    </div>
    
    <h2>Environment Info</h2>
    <div style="background: #f0f0f0; padding: 20px; font-family: monospace;">
        <p><strong>APP_URL:</strong> {{ env('APP_URL') }}</p>
        <p><strong>ASSET_URL:</strong> {{ env('ASSET_URL') }}</p>
        <p><strong>Current URL:</strong> {{ url('/') }}</p>
    </div>
    
    <h2>Direct File Existence Test</h2>
    <div style="background: #f0f0f0; padding: 20px; font-family: monospace;">
        @php
            $files = [
                'css/bootstrap.min.css' => public_path('css/bootstrap.min.css'),
                'css/style.css' => public_path('css/style.css'),
                'lib/animate/animate.min.css' => public_path('lib/animate/animate.min.css'),
                'js/main.js' => public_path('js/main.js'),
            ];
        @endphp
        
        @foreach($files as $name => $path)
            <p>
                <strong>{{ $name }}:</strong>
                @if(file_exists($path))
                    <span style="color: green;">✓ EXISTS ({{ filesize($path) }} bytes)</span>
                @else
                    <span style="color: red;">✗ MISSING</span>
                @endif
            </p>
        @endforeach
    </div>
</body>
</html>
