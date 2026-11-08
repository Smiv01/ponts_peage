<?php
$in_paiement = true;
require_once '../connexion.php';
require_once '../includes/header.php';

$vehicules = $pdo->query("SELECT * FROM vehicules ORDER BY tarif ASC")->fetchAll();
$preselect = isset($_GET['vehicule_id']) ? (int)$_GET['vehicule_id'] : null;
?>

<div class="container">
    <!-- Étapes -->
    <div class="steps">
        <div class="step active">
            <div class="step-num">1</div>
            <div class="step-label">Véhicule</div>
        </div>
        <div class="step-line"></div>
        <div class="step">
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
        <div class="card-title">🚗 Choisir le type de véhicule</div>
        <div class="card-subtitle">Sélectionnez le véhicule qui se présente au péage</div>

        <form method="POST" action="choix_paiement.php">
            <div class="vehicle-grid">
                <?php foreach($vehicules as $v): ?>
                <label class="vehicle-card <?= $preselect == $v['id'] ? 'selected' : '' ?>" 
                       onclick="selectVehicle(this)">
                    <input type="radio" name="vehicule_id" value="<?= $v['id'] ?>"
                           <?= $preselect == $v['id'] ? 'checked' : '' ?> required>
                    <div class="icon"><?= $v['icone'] ?></div>
                    <div class="name"><?= htmlspecialchars($v['type_vehicule']) ?></div>
                    <div class="price"><?= number_format($v['tarif'],0,',',' ') ?> FCFA</div>
                </label>
                <?php endforeach; ?>
            </div>

            <div id="selected-info" style="display:none;" class="summary-box">
                <div class="summary-row">
                    <span class="label">Véhicule sélectionné</span>
                    <span class="value" id="sel-name">—</span>
                </div>
                <div class="summary-row total">
                    <span class="label">Montant à encaisser</span>
                    <span class="value" id="sel-price">—</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Continuer → Choisir le paiement
            </button>
        </form>
    </div>
</div>

<script>
const vehicules = <?= json_encode(array_column($vehicules, null, 'id')) ?>;

function selectVehicle(el) {
    document.querySelectorAll('.vehicle-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    const id = el.querySelector('input').value;
    const v = vehicules[id];
    if (v) {
        document.getElementById('sel-name').textContent = v.icone + ' ' + v.type_vehicule;
        document.getElementById('sel-price').textContent = parseInt(v.tarif).toLocaleString('fr') + ' FCFA';
        document.getElementById('selected-info').style.display = 'block';
    }
}

// Afficher si présélectionné
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[type=radio]:checked');
    if (checked) selectVehicle(checked.closest('.vehicle-card'));
});
</script>

<?php require_once '../includes/footer.php'; ?>