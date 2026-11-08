<?php
require_once '../connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['vehicule_id']) || empty($_POST['moyen_paiement_id'])) {
    header('Location: choix_vehicule.php');
    exit;
}

$vehicule_id       = (int)$_POST['vehicule_id'];
$moyen_paiement_id = (int)$_POST['moyen_paiement_id'];

// Récupérer le véhicule et son tarif
$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = ?");
$stmt->execute([$vehicule_id]);
$vehicule = $stmt->fetch();

if (!$vehicule) {
    header('Location: choix_vehicule.php');
    exit;
}

// Générer une référence unique
$reference = 'PP-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . date('His');

// Enregistrer le paiement
$insert = $pdo->prepare("
    INSERT INTO paiements (vehicule_id, moyen_paiement_id, montant, reference, agent)
    VALUES (?, ?, ?, ?, ?)
");
$insert->execute([
    $vehicule_id,
    $moyen_paiement_id,
    $vehicule['tarif'],
    $reference,
    'Agent Péage'
]);

$paiement_id = $pdo->lastInsertId();

// Redirection vers le reçu
header("Location: recu.php?id=$paiement_id");
exit;
?>