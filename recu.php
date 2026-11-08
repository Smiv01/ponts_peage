<?php
$in_paiement = true;
require_once '../connexion.php';

if (empty($_GET['id'])) {
    header('Location: choix_vehicule.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, v.type_vehicule, v.icone, m.nom as moyen, m.icone as moyen_icone
    FROM paiements p
    JOIN vehicules v ON p.vehicule_id = v.id
    JOIN moyens_paiement m ON p.moyen_paiement_id = m.id
    WHERE p.id = ?
");
$stmt->execute([(int)$_GET['id']]);
$p = $stmt->fetch();

if (!$p) {
    header('Location: choix_vehicule.php');
    exit;
}

require_once '../includes/header.php';
?>

<div class="container">
    <!-- Étapes -->
    <div class="steps">
        <div class="step done"><div class="step-num">✓</div><div class="step-label">Véhicule</div></div>
        <div class="step-line"></div>
        <div class="step done"><div class="step-num">✓</div><div class="step-label">Paiement</div></div>
        <div class="step-line"></div>
        <div class="step active"><div class="step-num">3</div><div class="step-label">Reçu</div></div>
    </div>

    <!-- REÇU -->
    <div class="receipt">
        <div class="receipt-logo">🌉 Ponts à Péage — Côte d'Ivoire</div>

        <div style="margin:20px 0;">
            <div style="font-size:3.5rem;">✅</div>
            <div class="receipt-title">Paiement confirmé !</div>
            <span class="badge-success">● Passage autorisé</span>
            <div class="receipt-ref">Réf : <strong><?= $p['reference'] ?></strong></div>
        </div>

        <hr class="receipt-divider">

        <div class="receipt-row">
            <span class="rl">Véhicule</span>
            <span class="rv"><?= $p['icone'] ?> <?= htmlspecialchars($p['type_vehicule']) ?></span>
        </div>
        <div class="receipt-row">
            <span class="rl">Moyen de paiement</span>
            <span class="rv"><?= $p['moyen_icone'] ?> <?= htmlspecialchars($p['moyen']) ?></span>
        </div>
        <div class="receipt-row">
            <span class="rl">Agent</span>
            <span class="rv"><?= htmlspecialchars($p['agent']) ?></span>
        </div>
        <div class="receipt-row">
            <span class="rl">Date & Heure</span>
            <span class="rv"><?= date('d/m/Y à H:i:s', strtotime($p['date_paiement'])) ?></span>
        </div>

        <hr class="receipt-divider">

        <div class="receipt-total">
            <span class="rt-label">💰 Montant encaissé</span>
            <span class="rt-value"><?= number_format($p['montant'],0,',',' ') ?> FCFA</span>
        </div>

        <hr class="receipt-divider">

        <p style="font-size:0.78rem;color:var(--gray);margin-bottom:20px;">
            Ce reçu confirme le paiement du droit de passage.<br>
            Conservez cette référence pour tout litige.
        </p>

        <!-- Actions -->
        <div style="display:flex;flex-direction:column;gap:10px;">
            <button onclick="window.print()" class="btn btn-secondary btn-block">
                🖨️ Imprimer le reçu
            </button>
            <a href="choix_vehicule.php" class="btn btn-primary btn-block">
                ➕ Nouveau passage
            </a>
            <a href="../index.php" class="btn btn-secondary btn-block">
                🏠 Retour à l'accueil
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    nav, footer, .steps, .btn { display: none !important; }
    .receipt { box-shadow: none; border: 1px solid #ccc; }
    body { background: white; }
}
</style>

<?php require_once '../includes/footer.php'; ?>