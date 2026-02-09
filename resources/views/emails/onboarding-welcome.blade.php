<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bienvenue !</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #10b981; color: #fff !important; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bienvenue {{ $adminName }} !</h2>
        <p>Votre espace <strong>{{ $subdomain }}</strong> a été créé avec succès.</p>
        <p>Vous pouvez y accéder dès maintenant en cliquant ci-dessous :</p>
        <p><a href="{{ $url }}" class="button">Accéder à mon espace</a></p>
        <p>Vos identifiants :<br>
        Email : {{ $adminEmail }}</p>
        <hr>
        <p><small>L'équipe {{ config('onboarding.brand_name', 'Raftfg') }}</small></p>
    </div>
</body>
</html>
