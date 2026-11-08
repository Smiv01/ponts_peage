<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ponts à Péage — Côte d'Ivoire</title>
    <link rel="stylesheet" href="<?= str_contains($current, 'paiement') || isset($in_paiement) ? '../' : '' ?>css/style.css">
</head>
<body>

<nav>
    <a class="logo" href="<?= isset($in_paiement) ? '../' : '' ?>index.php">
        🌉 <span>Ponts</span>Péage
    </a>
    <ul>
        <li><a href="<?= isset($in_paiement) ? '../' : '' ?>index.php" 
               class="<?= $current === 'index.php' ? 'active' : '' ?>">Accueil</a></li>
        <li><a href="<?= isset($in_paiement) ? '' : 'paiement/' ?>choix_vehicule.php"
               class="<?= in_array($current, ['choix_vehicule.php','choix_paiement.php','recu.php']) ? 'active' : '' ?>">Nouveau Paiement</a></li>
        <li><a href="<?= isset($in_paiement) ? '../' : '' ?>historique.php"
               class="<?= $current === 'historique.php' ? 'active' : '' ?>">Historique</a></li>
    </ul>
</nav>