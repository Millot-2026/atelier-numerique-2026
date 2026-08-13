<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palettor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="palettor-wrapper">
        <header class="palettor-header">
            <input type="text" id="paletteTitle" class="palette-title-input" value="Nom du Projet" placeholder="Nom de la charte...">
            <div class="palettor-actions">
                <button id="savePaletteBtn" class="btn-primary">Sauvegarder</button>
            </div>
        </header>

        <div class="palettor-content">
            <div class="palette-editor">
                <div class="input-group">
                    <label for="logoInput">Logo annonceur (Importer) :</label>
                    <input type="file" id="logoInput" accept="image/*">
                </div>

                <div class="colors-row">
                    <div class="color-picker-wrapper">
                        <label>Primaire (OKLCH)</label>
                        <input type="text" id="primaryOklch" value="oklch(0.6 0.2 30)">
                    </div>
                    <div class="color-picker-wrapper">
                        <label>Secondaire (OKLCH)</label>
                        <input type="text" id="secondaryOklch" value="oklch(0.9 0.1 30)">
                    </div>
                </div>

                <!-- SECTION DÉDIÉE À L'ANALYSE DU LOGO -->
                <div class="logo-analysis-block" style="margin-top: 20px; padding: 12px; background: rgba(0,0,0,0.2); border-radius: 6px; border: 1px solid rgba(255,255,255,0.05);">
                    <h4 style="font-size: 0.85rem; margin-bottom: 8px; color: var(--text-muted, #aaa);">Couleurs détectées dans le logo :</h4>
                    <div id="logoAnalysisContainer" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <span style="font-size: 0.8rem; color: #777;">Importe un logo pour lancer l'analyse...</span>
                    </div>
                </div>
            </div>

            <!-- Zone de preview -->
            <div class="palette-preview" id="palettePreview">
                <div class="brand-card-sample">
                    <img id="logoPreviewImg" src="" alt="Logo" class="sample-logo" style="display:none;">
                </div>
            </div>
        </div>

        <!-- Section dédiée aux Ambiances, Recommandations & Mots-clés -->
        <div class="ambiance-section">
            <h3>Ambiance & Recommandations</h3>
            <p class="ambiance-subtitle">Suggestions d'univers graphiques et déclinaisons sémantiques</p>
            
            <!-- Grille qui accueille les boutons noirs ET le bouton carré noir dynamique -->
            <div id="presetsGrid" class="presets-grid-extended"></div>

            <!-- Champ de saisie classique des tags -->
            <div class="input-group" style="margin-top: 15px;">
                <input type="text" id="tagInput" placeholder="Ajouter un tag (ex: luxe, tech...) et Entrée">
                <div id="activeTagsContainer" class="tags-container"></div>
            </div>
        </div>

        <!-- Bloc d'analyse des couleurs -->
        <div id="colorAnalysisContainer" class="color-analysis-section">
            <div class="color-hint">Astuce : Clic droit (PC) ou appui long (Mobile) sur une couleur pour la supprimer.</div>
        </div>

        <!-- Bloc des palettes sauvegardées -->
        <div class="saved-palettes-section">
            <h3>Palettes sauvegardées</h3>
            <div id="savedPalettesList" class="saved-palettes-grid"></div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>