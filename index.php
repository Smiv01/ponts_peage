<?php require_once 'connexion.php'; ?>
<?php require_once 'includes/header.php'; ?>

<?php
// Statistiques du jour
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COUNT(*) as nb, COALESCE(SUM(montant),0) as total FROM paiements WHERE DATE(date_paiement) = ?");
$stmt->execute([$today]);
$stats = $stmt->fetch();

$stmt2 = $pdo->query("SELECT COUNT(*) as total FROM paiements");
$total_all = $stmt2->fetch()['total'];
?>

<div class="container">
    <div class="hero">
        <h1>Système de Péage 🌉</h1>
        <p>Gérez les paiements de passage sur les ponts à péage rapidement et efficacement.</p>
        <a href="paiement/choix_vehicule.php" class="btn btn-primary">➕ Nouveau Paiement</a>
    </div>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:32px;">
        <div class="card" style="text-align:center;padding:24px;">
            <div style="font-size:2rem;margin-bottom:8px;">🚗</div>
            <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:var(--accent)"><?= $stats['nb'] ?></div>
            <div style="color:var(--gray);font-size:0.9rem;">Passages aujourd'hui</div>
        </div>
        <div class="card" style="text-align:center;padding:24px;">
            <div style="font-size:2rem;margin-bottom:8px;">💰</div>
            <div style="font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:var(--accent)"><?= number_format($stats['total'],0,',',' ') ?> F</div>
            <div style="color:var(--gray);font-size:0.9rem;">Recettes aujourd'hui</div>
        </div>
        <div class="card" style="text-align:center;padding:24px;">
            <div style="font-size:2rem;margin-bottom:8px;">📊</div>
            <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:var(--accent)"><?= $total_all ?></div>
            <div style="color:var(--gray);font-size:0.9rem;">Total paiements</div>
        </div>
    </div>

    <!-- Tarifs rapides -->
    <div class="card">
        <div class="card-title">📋 Grille tarifaire</div>
        <div class="card-subtitle">Tarifs en vigueur — Francs CFA (FCFA)</div>
        <?php $vehicules = $pdo->query("SELECT * FROM vehicules")->fetchAll(); ?>
        <table>
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Type</th>
                    <th>Tarif</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($vehicules as $v): ?>
                <tr>
                    <td><?= $v['icone'] ?></td>
                    <td><?= htmlspecialchars($v['type_vehicule']) ?></td>
                    <td><strong><?= number_format($v['tarif'],0,',',' ') ?> FCFA</strong></td>
                    <td>
                        <a href="paiement/choix_vehicule.php?vehicule_id=<?= $v['id'] ?>" class="btn btn-primary" style="padding:6px 14px;font-size:0.85rem;">
                            Encaisser
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Derniers paiements -->
    <div class="card">
        <div class="card-title">🕐 Derniers paiements</div>
        <?php
        $derniers = $pdo->query("
            SELECT p.*, v.type_vehicule, v.icone, m.nom as moyen
            FROM paiements p
            JOIN vehicules v ON p.vehicule_id = v.id
            JOIN moyens_paiement m ON p.moyen_paiement_id = m.id
            ORDER BY p.date_paiement DESC LIMIT 5
        ")->fetchAll();
        ?>
        <?php if(empty($derniers)): ?>
            <p style="color:var(--gray);text-align:center;padding:20px;">Aucun paiement enregistré pour le moment.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>Référence</th><th>Véhicule</th><th>Montant</th><th>Moyen</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php foreach($derniers as $p): ?>
                <tr>
                    <td><code><?= $p['reference'] ?></code></td>
                    <td><?= $p['icone'] ?> <?= htmlspecialchars($p['type_vehicule']) ?></td>
                    <td><strong><?= number_format($p['montant'],0,',',' ') ?> FCFA</strong></td>
                    <td>
                        <?php
                        $tags = ['Espèces'=>'especes','Wave'=>'wave','Moov Money'=>'moov','Orange Money'=>'orange','Carte bancaire'=>'carte'];
                        $cls = $tags[$p['moyen']] ?? 'especes';
                        ?>
                        <span class="tag tag-<?= $cls ?>"><?= $p['moyen'] ?></span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($p['date_paiement'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <div style="margin-top:16px;">
            <a href="historique.php" class="btn btn-secondary">Voir tout l'historique →</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>