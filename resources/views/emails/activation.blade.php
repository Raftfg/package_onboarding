<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Confirmez votre adresse email</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #00286f; color: #fff !important; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bonjour,</h2>
        <p>Merci de vous être inscrit sur <strong>{{ $organizationName }}</strong>.</p>
        <p>Pour activer votre compte et configurer votre mot de passe, veuillez cliquer sur le bouton ci-dessous :</p>
        <p><a href="{{ $activationUrl }}" class="button">Activer mon compte</a></p>
        <p>Ce lien expirera dans {{ $expiresInDays }} jours.</p>
        <p>Si vous n'avez pas demandé cette activation, vous pouvez ignorer cet email.</p>
        <hr>
        <p><small>L'équipe {{ config('onboarding.brand_name', 'Raftfg') }}</small></p>
    </div>
</body>
</html>
