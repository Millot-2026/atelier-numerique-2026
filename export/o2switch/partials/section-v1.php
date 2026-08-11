<?php
/**
 * Partiel : section-v1.php
 * Contient uniquement la grille de cartes V1 (sans wrapper HTML, sans <details>).
 * Doit être inclus dans un contexte où $projects est déjà défini.
 */
?>
<div class="grid">
    <?php if (empty($projects)): ?>
        <p class="empty">Aucun projet trouvé dans ce dossier.</p>
    <?php else: ?>
        <?php foreach ($projects as $p): ?>
            <?php
            $linkHref = '/' . rawurlencode($p['name']) . '/';
            $jsonDetailsAttr = htmlspecialchars(json_encode($p['details']), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="<?php echo $p['cardClass']; ?>" 
                 data-title="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>"
                 data-summary="<?php echo htmlspecialchars($p['description'], ENT_QUOTES, 'UTF-8'); ?>"
                 data-img="<?php echo htmlspecialchars($p['screenshot'], ENT_QUOTES, 'UTF-8'); ?>"
                 data-details='<?php echo $jsonDetailsAttr; ?>'>
                <div>
                    <div class="card-title"><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <span class="<?php echo $p['badgeClass']; ?>" 
                          data-project="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>"
                          data-status="<?php echo $p['statusKey']; ?>"
                          <?php echo !$p['isWP'] ? 'onclick="cycleStatus(this)" title="Cliquez pour changer le statut"' : ''; ?>>
                        <?php echo $p['badgeLabel']; ?>
                    </span>
                </div>
                <div class="card-actions">
                    <a class="card-link" href="<?php echo $linkHref; ?>" target="_blank">Lancer le site</a>
                    <button type="button" class="btn-info" onclick="openOverlay(this)"><i class="fas fa-eye"></i> En savoir plus...</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
