<!DOCTYPE html>
<html>
<head>
    <title>Asset Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .path { background: #f0f0f0; padding: 12px; margin: 15px 0; border-left: 4px solid #0066cc; border-radius: 4px; }
        .label { font-weight: bold; color: #0066cc; margin-bottom: 5px; }
        .value { word-break: break-all; }
        hr { margin: 30px 0; border: 1px solid #ddd; }
        .test-element { padding: 15px; margin: 15px 0; border-radius: 4px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Asset URLs Debug</h2>
        
        <div class="path">
            <div class="label">APP_URL:</div>
            <div class="value">{{ env('APP_URL') }}</div>
        </div>
        
        <div class="path">
            <div class="label">ASSET_URL:</div>
            <div class="value">{{ env('ASSET_URL') }}</div>
        </div>
        
        <div class="path">
            <div class="label">asset('css/bootstrap.min.css'):</div>
            <div class="value">{{ asset('css/bootstrap.min.css') }}</div>
        </div>
        
        <div class="path">
            <div class="label">asset('css/style.css'):</div>
            <div class="value">{{ asset('css/style.css') }}</div>
        </div>
        
        <div class="path">
            <div class="label">asset('lib/animate/animate.min.css'):</div>
            <div class="value">{{ asset('lib/animate/animate.min.css') }}</div>
        </div>
        
        <hr>
        
        <h3>Test CSS Loading</h3>
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        
        <div class="test-element btn btn-primary">
            Test Button (Should be styled if CSS loads correctly)
        </div>
        
        <div class="test-element" style="background: #007bff; color: white;">
            This is a test element with inline styles
        </div>
    </div>
</body>
</html>
