<?php
session_start();

if (!isset($_SESSION['finished']) || !$_SESSION['finished']) {
    header("Location: index.php");
    exit;
}

$won = $_SESSION['won'] ?? false;
$secret = $_SESSION['secret'] ?? '';
$attempts = $_SESSION['attempts'] ?? [];
$totalAttempts = count($attempts);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Resultado - Mastermind Numérico</title>
</head>
<body>

<div class="container">
    <h2>Resultado de la Partida</h2>

    <div class="result">
        <?php if ($won): ?>
            <h3 style="color: green;">¡Felicidades, has ganado!</h3>
            <p>Has acertado el número secreto: <strong><?= htmlspecialchars($secret) ?></strong></p>
            <p>Total de intentos usados: <?= $totalAttempts ?></p>
        <?php else: ?>
            <h3 style="color: red;">¡Has perdido!</h3>
            <p>Te has quedado sin intentos. El número secreto era: <strong><?= htmlspecialchars($secret) ?></strong></p>
        <?php endif; ?>

        <p>
            <a href="index.php?reset=1">
                <button type="button">Jugar otra vez</button>
            </a>
        </p>
    </div>

    <?php if (!empty($attempts)): ?>
        <h3>Historial de intentos</h3>
        <table border="1">
            <tr>
                <th>#</th>
                <th>Intento</th>
                <th>Exactos</th>
                <th>Parciales</th>
            </tr>
            <?php foreach ($attempts as $i =>$a): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($a['guess']) ?></td>
                    <td><?= $a['exact'] ?></td>
                    <td><?= $a['partial'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>