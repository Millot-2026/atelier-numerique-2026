<!-- SECTION EXPÉRIMENTALE (V2) -->
<style>
    .projects-grid-v2 {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .card-v2 {
        background-color: var(--card-bg);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
        box-sizing: border-box;
    }

    .card-v2:hover {
        transform: translateY(-4px);
        border-color: rgba(56, 189, 248, 0.4);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.4);
    }

    /* En-tête : Titre seul au-dessus de l'image */
    .card-v2-top {
        margin-bottom: 12px;
    }

    .card-v2-title {
        color: var(--text-main);
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        word-break: break-all;
    }

    /* Média : Image de la card */
    .card-v2-media {
        position: relative;
        width: 100%;
        height: 140px;
        background-color: #0d1b2a;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .card-v2-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .card-v2-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Statut placé sous l'image */
    .card-v2-status {
        margin-bottom: 10px;
    }

    .card-v2-technos {
        margin-bottom: 20px;
    }

    /* Boutons d'action en colonne (pleine largeur V1) */
    .card-v2-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .btn-v2-primary {
        display: inline-block;
        text-align: center;
        background-color: var(--accent);
        color: var(--bg-color);
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background-color 0.2s ease;
    }

    .btn-v2-primary:hover {
        background-color: var(--accent-hover);
    }

    .btn-v2-ghost {
        background: rgba(56, 189, 248, 0.05);
        border: 1px solid rgba(56, 189, 248, 0.3);
        color: var(--accent);
        text-align: center;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        box-sizing: border-box;
    }

    .btn-v2-ghost:hover {
        background-color: var(--accent);
        color: var(--bg-color);
        border-color: var(--accent);
    }
</style>

<div id="section-v2-container">
    <h2 style="color: var(--accent); font-size: 1.4rem; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px;">
        🧪 Lanceur Projets (V2 - Cartes Enrichies)
    </h2>

    <div class="projects-grid-v2">
        <?php foreach ($projects as $p): ?>
            <?php 
                $linkHref = '/' . rawurlencode($p['name']) . '/';
                $jsonDetailsAttr = htmlspecialchars(json_encode($p['details']), ENT_QUOTES, 'UTF-8');
                
                $assetsDir = __DIR__ . '/../dashboard-designer/assets/img/';
                $defaultImage = 'photo-640x480.png';
                $imgName = $defaultImage;

                if (isset($p['details']['niveau1']['image']) && !empty($p['details']['niveau1']['image'])) {
                    $candidate = basename($p['details']['niveau1']['image']);
                    if (file_exists($assetsDir . $candidate) && !is_dir($assetsDir . $candidate)) {
                        $imgName = $candidate;
                    }
                }

                $imgSrc = 'dashboard-designer/assets/img/' . $imgName;
            ?>
            <div class="card-v2 card"
                 data-title="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>"
                 data-summary="<?php echo htmlspecialchars($p['description'], ENT_QUOTES, 'UTF-8'); ?>"
                 data-img="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>"
                 data-details='<?php echo $jsonDetailsAttr; ?>'>
                
                <!-- 1. Titre seul au-dessus de l'image -->
                <div class="card-v2-top">
                    <h3 class="card-v2-title"><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                </div>

                <!-- 2. Image -->
                <div class="card-v2-media">
                    <img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <!-- 3. Corps : Statut + Badges technos sous l'image -->
                <div class="card-v2-body">
                    <div>
                        <div class="card-v2-status">
                            <span class="<?php echo $p['badgeClass']; ?>" 
                                  data-project="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                  data-status="<?php echo $p['statusKey']; ?>"
                                  <?php echo !$p['isWP'] ? 'onclick="cycleStatus(this)" title="Cliquez pour changer le statut"' : ''; ?>>
                                <?php echo $p['badgeLabel']; ?>
                            </span>
                        </div>

                        <?php if (isset($p['details']['niveau1']['technos']) && !empty($p['details']['niveau1']['technos'])): ?>
                            <div class="card-v2-technos">
                                <?php foreach ($p['details']['niveau1']['technos'] as $tech): ?>
                                    <span class="badge-tech"><?php echo htmlspecialchars($tech, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Actions verticalisées -->
                    <div class="card-v2-actions">
                        <a class="btn-v2-primary" href="<?php echo $linkHref; ?>" target="_blank">
                            Lancer le site
                        </a>
                        <button class="btn-v2-ghost" onclick="openOverlay(this)" title="En savoir plus">
                            <i class="fas fa-eye"></i> En savoir plus...
                        </button>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div>