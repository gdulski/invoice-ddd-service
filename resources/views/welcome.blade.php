<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Invoice DDD Service</title>
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                text-align: center;
                background: white;
                padding: 3rem;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                max-width: 600px;
                margin: 2rem;
            }
            h1 {
                color: #333;
                margin-bottom: 1rem;
                font-size: 2.5rem;
            }
            .subtitle {
                color: #666;
                font-size: 1.2rem;
                margin-bottom: 2rem;
            }
            .status {
                background: #e8f5e8;
                color: #2d5a2d;
                padding: 1rem;
                border-radius: 10px;
                margin: 1rem 0;
                font-weight: bold;
            }
            .links {
                margin-top: 2rem;
            }
            .links a {
                display: inline-block;
                margin: 0.5rem;
                padding: 0.8rem 1.5rem;
                background: #667eea;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                transition: background 0.3s;
            }
            .links a:hover {
                background: #5a6fd8;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🏢 Invoice DDD Service</h1>
            <p class="subtitle">Domain-Driven Design Laravel Application</p>
            
            <div class="status">
                ✅ Service is running successfully!
            </div>
            
            <p>Welcome to your new Laravel application with DDD architecture. This service is designed to handle invoice-related business logic following Domain-Driven Design principles.</p>
            
            <div class="links">
                <a href="/api/health">🏥 Health Check</a>
                <a href="https://laravel.com/docs" target="_blank">📚 Laravel Docs</a>
            </div>
        </div>
    </body>
</html>
