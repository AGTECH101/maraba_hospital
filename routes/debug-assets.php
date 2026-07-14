<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-assets', function () {
    return <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <title>Asset Debug</title>
        <style>
            body { font-family: monospace; padding: 20px; }
            .path { background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 3px solid #0066cc; }
            .label { font-weight: bold; color: #0066cc; }
        </style>
    </head>
    <body>
        <h2>Asset URLs Debug</h2>
        <div class="path">
            <div class="label">APP_URL:</div>
            {{ env('APP_URL') }}
        </div>
        <div class="path">
            <div class="label">ASSET_URL:</div>
            {{ env('ASSET_URL') }}
        </div>
        <div class="path">
            <div class="label">asset('css/bootstrap.min.css'):</div>
            {{ asset('css/bootstrap.min.css') }}
        </div>
        <div class="path">
            <div class="label">asset('css/style.css'):</div>
            {{ asset('css/style.css') }}
        </div>
        <div class="path">
            <div class="label">asset('lib/animate/animate.min.css'):</div>
            {{ asset('lib/animate/animate.min.css') }}
        </div>
        <hr>
        <h3>Test Loading CSS</h3>
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <div class="btn btn-primary" style="padding: 10px; margin: 10px 0; background: #0066cc; color: white; display: inline-block; border-radius: 4px;">
            Test Button (Should be styled if CSS loads)
        </div>
    </body>
    </html>
    HTML;
});
