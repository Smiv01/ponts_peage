<?php
$in_paiement = true;
require_once '../connexion.php';

// Vérification
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['vehicule_id'])) {
    header('Location: choix_vehicule.php');
    exit;
}

$vehicule_id = (int)$_POST['vehicule_id'];
$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = ?");
$stmt->execute([$vehicule_id]);
$vehicule = $stmt->fetch();

if (!$vehicule) {
    header('Location: choix_vehicule.php');
    exit;
}

$moyens = $pdo->query("SELECT * FROM moyens_paiement")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container">
    <!-- Étapes -->
    <div class="steps">
        <div class="step done">
            <div class="step-num">✓</div>
            <div class="step-label">Véhicule</div>
        </div>
        <div class="step-line"></div>
        <div class="step active">
            <div class="step-num">2</div>
            <div class="step-label">Paiement</div>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-label">Reçu</div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">💳 Choisir le moyen de paiement</div>
        <div class="card-subtitle">Sélectionnez comment le passager règle son passage</div>

        <!-- Résumé véhicule -->
        <div class="summary-box" style="margin-bottom:24px;">
            <div class="summary-row">
                <span class="label">Véhicule</span>
                <span class="value"><?= $vehicule['icone'] ?> <?= htmlspecialchars($vehicule['type_vehicule']) ?></span>
            </div>
            <div class="summary-row total">
                <span class="label">Montant</span>
                <span class="value"><?= number_format($vehicule['tarif'],0,',',' ') ?> FCFA</span>
            </div>
        </div>

        <form method="POST" action="traitement.php">
            <input type="hidden" name="vehicule_id" value="<?= $vehicule_id ?>">

            <div class="payment-grid">
                <?php
                $icons_desc = [
                    'Espèces'       => ['💵', 'Paiement immédiat en cash'],
                    'Wave'          => ['🔵', 'Transfert via Wave CI'],
                    'Moov Money'    => ['🟡', 'Transfert via Moov Money'],
                    'Orange Money'  => ['🟠', 'Transfert via Orange Money'],
                    'Carte bancaire'=> ['💳', 'Visa / Mastercard'],
                ];
                foreach($moyens as $m):
                    $info = $icons_desc[$m['nom']] ?? ['💰', ''];
                ?>
                <label class="payment-option" onclick="selectPayment(this)">
                    <input type="radio" name="moyen_paiement_id" value="<?= $m['id'] ?>" required>
                    <div class="pay-icon"><?= $info[0] ?></div>
                    <div>
                        <div class="pay-name"><?= htmlspecialchars($m['nom']) ?></div>
                        <div class="pay-desc"><?= $info[1] ?></div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
                <a href="choix_vehicule.php" class="btn btn-secondary">← Retour</a>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                    ✅ Confirmer le paiement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectPayment(el) {
    document.querySelectorAll('.payment-option').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
}
</script>

<?php require_once '../includes/footer.php'; ?>