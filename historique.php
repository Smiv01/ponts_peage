<?php
require_once 'connexion.php';
require_once 'includes/header.php';

// Filtres
$filtre_moyen = $_GET['moyen'] ?? '';
$filtre_date  = $_GET['date']  ?? '';

$where = "WHERE 1=1";
$params = [];

if ($filtre_moyen) {
    $where .= " AND m.id = ?";
    $params[] = $filtre_moyen;
}
if ($filtre_date) {
    $where .= " AND DATE(p.date_paiement) = ?";
    $params[] = $filtre_date;
}

$stmt = $pdo->prepare("
    SELECT p.*, v.type_vehicule, v.icone, m.nom as moyen
    FROM paiements p
    JOIN vehicules v ON p.vehicule_id = v.id
    JOIN moyens_paiement m ON p.moyen_paiement_id = m.id
    $where
    ORDER BY p.date_paiement DESC
");
$stmt->execute($params);
$paiements = $stmt->fetchAll();

// Total filtré
$stmt2 = $pdo->prepare("
    SELECT COALESCE(SUM(p.montant),0) as total, COUNT(*) as nb
    FROM paiements p
    JOIN moyens_paiement m ON p.moyen_paiement_id = m.id
    $where
");
$stmt2->execute($params);
$totaux = $stmt2->fetch();

$moyens = $pdo->query("SELECT * FROM moyens_paiement")->fetchAll();
?>

<div class="container">
    <div class="card-title" style="margin-bottom:20px;font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:700;">
        📊 Historique des paiements
    </div>

    <!-- Filtres -->
    <div class="card" style="padding:20px;">
        <form method="GET" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label style="font-size:0.85rem;color:var(--gray);display:block;margin-bottom:4px;">Moyen de paiement</label>
                <select name="moyen" style="padding:8px 14px;border:2px solid var(--border);border-radius:8px;font-family:'DM Sans',sans-serif;">
                    <option value="">Tous</option>
                    <?php foreach($moyens as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $filtre_moyen == $m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size:0.85rem;color:var(--gray);display:block;margin-bottom:4px;">Date</label>
                <input type="date" name="date" value="<?= htmlspecialchars($filtre_date) ?>"
                       style="padding:8px 14px;border:2px solid var(--border);border-radius:8px;font-family:'DM Sans',sans-serif;">
            </div>
            <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
            <a href="historique.php" class="btn btn-secondary">✖ Réinitialiser</a>
        </form>
    </div>

    <!-- Totaux -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
        <div class="card" style="text-align:center;padding:20px;">
            <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--accent)"><?= $totaux['nb'] ?></div>
            <div style="color:var(--gray);font-size:0.9rem;">Passages trouvés</div>
        </div>
        <div class="card" style="text-align:center;padding:20px;">
            <div style="font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:var(--accent)"><?= number_format($totaux['total'],0,',',' ') ?> FCFA</div>
            <div style="color:var(--gray);font-size:0.9rem;">Total encaissé</div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card" style="padding:0;overflow:hidden;">
        <?php
        $tags = ['Espèces'=>'especes','Wave'=>'wave','Moov Money'=>'moov','Orange Money'=>'orange','Carte bancaire'=>'carte'];
        ?>
        <?php if(empty($paiements)): ?>
            <p style="color:var(--gray);text-align:center;padding:40px;">Aucun paiement trouvé.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Référence</th>
                    <th>Véhicule</th>
                    <th>Montant</th>
                    <th>Moyen</th>
                    <th>Agent</th>
                    <th>Date & Heure</th>
                    <th>Reçu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($paiements as $i => $p): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><code style="font-size:0.8rem;"><?= $p['reference'] ?></code></td>
                    <td><?= $p['icone'] ?> <?= htmlspecialchars($p['type_vehicule']) ?></td>
                    <td><strong><?= number_format($p['montant'],0,',',' ') ?> FCFA</strong></td>
                    <td><span class="tag tag-<?= $tags[$p['moyen']] ?? 'especes' ?>"><?= $p['moyen'] ?></span></td>
                    <td><?= htmlspecialchars($p['agent']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($p['date_paiement'])) ?></td>
                    <td>
                        <a href="paiement/recu.php?id=<?= $p['id'] ?>" class="btn btn-secondary" style="padding:5px 12px;font-size:0.8rem;">
                            🧾 Voir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>