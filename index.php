<?php
session_start();

/*
  Mastermind numérico
  - Genera un número secreto de 3 cifras distintas.
  - El jugador tiene 10 intentos.
*/

$NUM_DIGITS = 3;
$MAX_ATTEMPTS = 10;

// Inicializar o reiniciar el juego
if (!isset($_SESSION['secret']) || isset($_GET['reset'])) {
    $digits = range(0, 9);
    shuffle($digits);
    $_SESSION['secret'] = implode('', array_slice($digits, 0, $NUM_DIGITS));
    $_SESSION['attempts'] = [];
    $_SESSION['finished'] = false;
    $_SESSION['won'] = false;
}

if (!empty($_SESSION['finished'])) {
    header("Location: resultado.php");
    exit;
}

$secret = $_SESSION['secret'];
$attempts = $_SESSION['attempts'];
$message = "";

// Procesar intento si el juego no ha terminado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guess = preg_replace('/\D/', '', $_POST['guess'] ?? '');

    if (strlen($guess) != $NUM_DIGITS) {
        $message = "Debes escribir exactamente $NUM_DIGITS cifras.";
    } elseif (strlen(count_chars($guess, 3)) != $NUM_DIGITS) {
        $message = "Las cifras no deben repetirse.";
    } else {
        $exact = 0;
        $partial = 0;
        for ($i = 0; $i < $NUM_DIGITS; $i++) {
            if ($guess[$i] === $secret[$i]) {
                $exact++;
            } elseif (strpos($secret, $guess[$i]) !== false) {
                $partial++;
            }
        }

        $_SESSION['attempts'][] = [
                'guess' => $guess,
                'exact' => $exact,
                'partial' => $partial
        ];

        // Verificar si ganó o perdió
        if ($exact == $NUM_DIGITS) {
            $_SESSION['finished'] = true;
            $_SESSION['won'] = true;
            header("Location: resultado.php");
            exit;
        } elseif (count($_SESSION['attempts']) >= $MAX_ATTEMPTS) {
            $_SESSION['finished'] = true;
            $_SESSION['won'] = false;
            header("Location: resultado.php");
            exit;
        }

        header("Location: index.php");
        exit;
    }
}

$remaining = $MAX_ATTEMPTS - count($_SESSION['attempts']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mastermind Numérico</title>
</head>
<body>

<div class="container">
    <h2>Mastermind Numérico</h2>
    <p>Adivina el número de <?= $NUM_DIGITS ?> cifras distintas.<br>
        Tienes <?= $MAX_ATTEMPTS ?> intentos.</p>

    <form method="post">
        <input type="text" name="guess" maxlength="<?= $NUM_DIGITS ?>" required autofocus placeholder="Ej: 123">
        <button type="submit">Intentar</button>
    </form>

    <?php if ($message): ?>
        <p class="message" style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <p>Intentos restantes: <?= $remaining ?></p>

    <?php if (!empty($attempts)): ?>
        <h3>Intentos anteriores</h3>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>#</th>
                <th>Intento</th>
                <th>Exactos</th>
                <th>Parciales</th>
            </tr>
            <?php foreach ($attempts as $i => $a): ?>
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