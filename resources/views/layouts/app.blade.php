<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('onboarding.brand_name'))</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { background: white; border-radius: 12px; max-width: 500px; width: 100%; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .logo h1 { text-align: center; color: #667eea; margin-bottom: 20px; }
        .btn { width: 100%; padding: 12px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; display: block; text-align: center; text-decoration: none; font-weight: bold; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
