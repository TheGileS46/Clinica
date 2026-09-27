<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body { margin: 0; padding: 4rem 1.5rem; background: #f4f7fb; color: #172033; font: 16px/1.6 system-ui, sans-serif; }
        main { max-width: 700px; margin: auto; padding: 2rem; background: #fff; border: 1px solid #dce3ee; border-radius: 16px; }
        h1 { margin-top: 0; }
        code { padding: .15rem .4rem; background: #eef2f8; border-radius: 5px; }
    </style>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8') ?></p>
        <p>Entorno: <code><?= htmlspecialchars($data['environment'], ENT_QUOTES, 'UTF-8') ?></code></p>
        <p>También puedes probar <code>/health</code>.</p>
    </main>
</body>
</html>
