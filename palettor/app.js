document.addEventListener('DOMContentLoaded', () => {
    const primaryInput = document.getElementById('primaryOklch');
    const secondaryInput = document.getElementById('secondaryOklch');
    const presetsGrid = document.getElementById('presetsGrid');
    const savePaletteBtn = document.getElementById('savePaletteBtn');
    const logoInput = document.getElementById('logoInput');
    const paletteTitle = document.getElementById('paletteTitle');
    const logoPreviewImg = document.getElementById('logoPreviewImg');
    const colorAnalysisContainer = document.getElementById('colorAnalysisContainer');
    const logoAnalysisContainer = document.getElementById('logoAnalysisContainer');
    const savedPalettesList = document.getElementById('savedPalettesList');
    const tagInput = document.getElementById('tagInput');
    const activeTagsContainer = document.getElementById('activeTagsContainer');

    let currentPaletteColors = ['oklch(0.6 0.15 250)', 'oklch(0.8 0.1 150)'];
    let currentPaletteKeywords = [];
    let currentEditIndex = null;
    let allPresetsData = [];
    let isCreatingMood = false;
    let activeMoodTitle = null;

    function updatePalettePreview(primary, secondary) {
        document.documentElement.style.setProperty('--dynamic-primary', primary);
        document.documentElement.style.setProperty('--dynamic-secondary', secondary);

        currentPaletteColors[0] = primary;
        currentPaletteColors[1] = secondary;
        updateSwatches();
        triggerAutoSave();
    }

    function updateSwatches() {
        if (!colorAnalysisContainer) return;

        colorAnalysisContainer.innerHTML = '';

        currentPaletteColors.forEach((col, index) => {
            const swatch = document.createElement('div');
            swatch.className = 'swatch-item';
            swatch.style.backgroundColor = col;

            const removeColor = () => {
                if (currentPaletteColors.length <= 2) {
                    alert("Une palette doit conserver au moins une couleur primaire et secondaire.");
                    return;
                }
                currentPaletteColors.splice(index, 1);
                updateSwatches();
                triggerAutoSave();
            };

            swatch.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                removeColor();
            });

            let pressTimer = null;
            swatch.addEventListener('touchstart', () => {
                pressTimer = setTimeout(() => {
                    removeColor();
                }, 500);
            });
            swatch.addEventListener('touchend', () => clearTimeout(pressTimer));
            swatch.addEventListener('touchmove', () => clearTimeout(pressTimer));

            colorAnalysisContainer.appendChild(swatch);
        });

        const addBtn = document.createElement('button');
        addBtn.className = 'add-color-btn';
        addBtn.textContent = '+';
        addBtn.title = "Ajouter une couleur harmonieuse";

        addBtn.addEventListener('click', () => {
            const lastColor = currentPaletteColors[currentPaletteColors.length - 1] || 'oklch(0.6 0.15 200)';
            const match = lastColor.match(/oklch\([\d.]+\s+[\d.]+\s+([\d.]+)\)/);
            const currentHue = match ? parseFloat(match[1]) : 200;
            const nextHue = (currentHue + 40) % 360;
            const newColor = `oklch(0.7 0.15 ${nextHue})`;

            currentPaletteColors.push(newColor);
            updateSwatches();
            triggerAutoSave();
        });

        colorAnalysisContainer.appendChild(addBtn);

        const hint = document.createElement('div');
        hint.className = 'color-hint';
        hint.textContent = 'Astuce : Clic droit (PC) ou appui long (Mobile) sur une couleur pour la supprimer.';
        colorAnalysisContainer.appendChild(hint);
    }

    function renderTags() {
        if (!activeTagsContainer) return;
        activeTagsContainer.innerHTML = '';

        currentPaletteKeywords.forEach((tag, index) => {
            const badge = document.createElement('div');
            badge.className = 'tag-badge';
            badge.textContent = tag;

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', () => {
                currentPaletteKeywords.splice(index, 1);
                renderTags();
                filterPresetsByTags();
                triggerAutoSave();
            });

            badge.appendChild(removeBtn);
            activeTagsContainer.appendChild(badge);
        });
    }

    function filterPresetsByTags() {
        if (!presetsGrid) return;
        presetsGrid.innerHTML = '';

        allPresetsData.forEach(palette => {
            const chip = document.createElement('button');
            chip.className = 'preset-chip';
            chip.textContent = palette.title;

            if (activeMoodTitle === palette.title) {
                chip.classList.add('active-mood');
            }

            chip.addEventListener('click', (e) => {
                if (e.ctrlKey) {
                    e.preventDefault();
                    let customPresets = JSON.parse(localStorage.getItem('palettor_custom_presets') || '[]');
                    const indexInCustom = customPresets.findIndex(p => p.title === palette.title);

                    if (indexInCustom !== -1) {
                        customPresets.splice(indexInCustom, 1);
                        localStorage.setItem('palettor_custom_presets', JSON.stringify(customPresets));
                        if (activeMoodTitle === palette.title) activeMoodTitle = null;
                        loadPresets();
                    }
                    return;
                }

                if (activeMoodTitle === palette.title) {
                    activeMoodTitle = null;
                    currentPaletteKeywords = [];
                } else {
                    activeMoodTitle = palette.title;
                    currentPaletteKeywords = palette.keywords ? palette.keywords.filter(k => k.toLowerCase() !== palette.title.toLowerCase()) : [];
                }

                renderTags();

                primaryInput.value = palette.primary;
                secondaryInput.value = palette.secondary;
                currentEditIndex = null;
                currentPaletteColors = palette.colors ? [...palette.colors] : [palette.primary, palette.secondary];

                updatePalettePreview(palette.primary, palette.secondary);
                filterPresetsByTags();
            });

            presetsGrid.appendChild(chip);
        });

        if (!isCreatingMood) {
            const addBtn = document.createElement('button');
            addBtn.className = 'preset-add-btn';
            addBtn.textContent = '+';
            addBtn.title = "Ajouter un mood";
            addBtn.addEventListener('click', () => {
                isCreatingMood = true;
                filterPresetsByTags();
            });
            presetsGrid.appendChild(addBtn);
        } else {
            const container = document.createElement('div');
            container.className = 'preset-inline-form';
            container.style.display = 'inline-flex';
            container.style.gap = '6px';
            container.style.alignItems = 'center';

            const inputTitle = document.createElement('input');
            inputTitle.type = 'text';
            inputTitle.className = 'preset-input-inline';
            inputTitle.placeholder = 'Nom du mood...';

            const inputKeywords = document.createElement('input');
            inputKeywords.type = 'text';
            inputKeywords.className = 'preset-input-inline';
            inputKeywords.placeholder = 'Mots-clés...';

            const handleValidation = () => {
                const newTitle = inputTitle.value.trim();
                if (newTitle !== '') {
                    const rawKeywords = inputKeywords.value.trim();
                    const parsedKeywords = rawKeywords ? rawKeywords.split(/[\s,]+/).map(k => k.toLowerCase()).filter(k => k.length > 0) : [];

                    const newPreset = {
                        title: newTitle,
                        primary: primaryInput.value,
                        secondary: secondaryInput.value,
                        colors: [...currentPaletteColors],
                        keywords: parsedKeywords
                    };

                    const customPresets = JSON.parse(localStorage.getItem('palettor_custom_presets') || '[]');
                    customPresets.push(newPreset);
                    localStorage.setItem('palettor_custom_presets', JSON.stringify(customPresets));

                    activeMoodTitle = newTitle;
                    currentPaletteKeywords = [...parsedKeywords];
                    renderTags();
                    loadPresets();
                }
                isCreatingMood = false;
                filterPresetsByTags();
            };

            inputTitle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); handleValidation(); }
                else if (e.key === 'Escape') { isCreatingMood = false; filterPresetsByTags(); }
            });

            inputKeywords.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); handleValidation(); }
                else if (e.key === 'Escape') { isCreatingMood = false; filterPresetsByTags(); }
            });

            container.appendChild(inputTitle);
            container.appendChild(inputKeywords);
            presetsGrid.appendChild(container);
            inputTitle.focus();
        }
    }

    function triggerAutoSave() {
        if (currentEditIndex === null) return;
        const savedData = localStorage.getItem('palettor_saved_list');
        const palettes = savedData ? JSON.parse(savedData) : [];

        if (palettes[currentEditIndex]) {
            palettes[currentEditIndex] = {
                title: paletteTitle.value,
                colors: [...currentPaletteColors],
                keywords: [...currentPaletteKeywords],
                primary: primaryInput.value,
                secondary: secondaryInput.value
            };
            localStorage.setItem('palettor_saved_list', JSON.stringify(palettes));
            loadSavedPalettes();
        }
    }

    function loadSavedPalettes() {
        if (!savedPalettesList) return;
        savedPalettesList.innerHTML = '';

        const savedData = localStorage.getItem('palettor_saved_list');
        const palettes = savedData ? JSON.parse(savedData) : [];

        if (palettes.length > 0) {
            palettes.forEach((palette, index) => {
                const card = document.createElement('div');
                card.className = 'saved-item-card';
                if (currentEditIndex === index) {
                    card.style.borderColor = 'var(--accent-color)';
                }

                card.addEventListener('click', (e) => {
                    if (e.target.classList.contains('delete-palette-btn')) return;

                    currentEditIndex = index;
                    paletteTitle.value = palette.title || `Palette ${index + 1}`;
                    currentPaletteKeywords = palette.keywords ? [...palette.keywords] : [];
                    renderTags();

                    if (palette.colors && palette.colors.length >= 2) {
                        currentPaletteColors = [...palette.colors];
                        primaryInput.value = palette.colors[0];
                        secondaryInput.value = palette.colors[1];
                        updatePalettePreview(palette.colors[0], palette.colors[1]);
                    }
                    loadSavedPalettes();
                });

                const headerEl = document.createElement('div');
                headerEl.className = 'saved-item-header';

                const titleEl = document.createElement('span');
                titleEl.className = 'saved-item-title';
                titleEl.textContent = palette.title || `Palette ${index + 1}`;
                headerEl.appendChild(titleEl);

                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'delete-palette-btn';
                deleteBtn.innerHTML = '&times;';

                deleteBtn.addEventListener('click', () => {
                    palettes.splice(index, 1);
                    if (currentEditIndex === index) currentEditIndex = null;
                    else if (currentEditIndex > index) currentEditIndex--;

                    localStorage.setItem('palettor_saved_list', JSON.stringify(palettes));
                    loadSavedPalettes();
                });
                headerEl.appendChild(deleteBtn);
                card.appendChild(headerEl);

                const safeColorsRow = document.createElement('div');
                safeColorsRow.className = 'saved-item-colors';

                (palette.colors || []).forEach(col => {
                    const miniSwatch = document.createElement('div');
                    miniSwatch.className = 'saved-mini-swatch';
                    miniSwatch.style.backgroundColor = col;
                    safeColorsRow.appendChild(miniSwatch);
                });

                card.appendChild(safeColorsRow);
                savedPalettesList.appendChild(card);
            });
        } else {
            savedPalettesList.innerHTML = '<span style="font-size:0.85rem; color:#777;">Aucune sauvegarde pour le moment.</span>';
        }
    }

    function loadPresets() {
        const customPresets = JSON.parse(localStorage.getItem('palettor_custom_presets') || '[]');
        allPresetsData = [...customPresets];
        filterPresetsByTags();
    }

    // ============================================================
    // ANALYSE CANVAS — Extraction chromatique perceptuelle v2
    // Stratégie : RGB→HSL, score hybride (saturation × rareté),
    // déduplication par distance angulaire de teinte (ΔH HSL).
    // ============================================================

    /**
     * Convertit un triplet RGB [0-255] en HSL.
     * @returns {{ h: number, s: number, l: number }}
     *   h ∈ [0, 360), s ∈ [0, 1], l ∈ [0, 1]
     */
    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        const delta = max - min;
        let h = 0, s = 0;
        const l = (max + min) / 2;

        if (delta > 0) {
            s = delta / (1 - Math.abs(2 * l - 1));
            switch (max) {
                case r: h = ((g - b) / delta + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / delta + 2) / 6; break;
                case b: h = ((r - g) / delta + 4) / 6; break;
            }
        }
        return { h: h * 360, s, l };
    }

    /**
     * Distance angulaire minimale entre deux teintes (cercle chromatique).
     * @returns {number} ∈ [0, 180]
     */
    function hueDistance(h1, h2) {
        const diff = Math.abs(h1 - h2) % 360;
        return diff > 180 ? 360 - diff : diff;
    }

    /**
     * Quantifie une valeur sur une grille de pas `step`.
     * Réduit le bruit de l'anti-aliasing sans perdre la distinction chromatique.
     */
    function quantize(v, step) {
        return Math.min(255, Math.round(v / step) * step);
    }

    /**
     * Extrait les couleurs identitaires d'une image via Canvas.
     * Retourne un tableau de codes HEX triés par pertinence chromatique.
     *
     * Filtres appliqués :
     *  - Pixels transparents       (alpha < 128)
     *  - Fond blanc / clair        (luminosité HSL > 92%)
     *  - Noir profond / sombre     (luminosité HSL < 5%)
     *  - Gris / neutres            (saturation HSL < 12%)
     *  - Anti-aliasing             (delta RGB max-min < 18)
     *
     * Score hybride par bin quantifié :
     *  score += saturation_HSL² × boost_rareté
     *  → Une couleur rare mais très saturée rivalise avec une couleur dominante.
     *
     * Déduplication perceptuelle :
     *  Deux couleurs sont « trop proches » si ΔH < 28° ET ΔL < 0.15.
     *  Cela préserve bleu clair vs bleu foncé, et rouge vs orange.
     *
     * @param {Uint8ClampedArray} imgData  Données brutes RGBA du canvas
     * @param {number} maxColors           Nombre maximum de couleurs à retourner
     * @returns {string[]}                 Tableau de codes HEX (#rrggbb)
     */
    function extractDistinctColors(imgData, maxColors = 6) {
        // --- Étape 1 : Accumulation par bins quantifiés ---
        // Clé = hex quantifié (pas de 20px → ~13 bins/canal)
        // Valeur = { score, count, hsl }
        const binsMap = new Map();
        const QUANT_STEP = 20;

        for (let i = 0; i < imgData.length; i += 4) {
            const r = imgData[i];
            const g = imgData[i + 1];
            const b = imgData[i + 2];
            const a = imgData[i + 3];

            // Filtre transparence
            if (a < 128) continue;

            // Pré-filtre rapide : gris/neutre via diff RGB
            const maxC = Math.max(r, g, b);
            const minC = Math.min(r, g, b);
            if (maxC - minC < 18) continue; // anti-aliasing & gris

            // Conversion HSL pour filtres perceptuels
            const hsl = rgbToHsl(r, g, b);

            // Filtre fond blanc/clair
            if (hsl.l > 0.92) continue;
            // Filtre noir profond
            if (hsl.l < 0.05) continue;
            // Filtre neutres / gris (saturation insuffisante)
            if (hsl.s < 0.12) continue;

            // Quantification → clé du bin
            const qR = quantize(r, QUANT_STEP);
            const qG = quantize(g, QUANT_STEP);
            const qB = quantize(b, QUANT_STEP);
            const key = `#${[qR, qG, qB].map(x => x.toString(16).padStart(2, '0')).join('')}`;

            const existing = binsMap.get(key);
            if (existing) {
                existing.count++;
                // Score cumulatif : saturation² (favorise les couleurs chromiques)
                existing.score += hsl.s * hsl.s;
            } else {
                binsMap.set(key, { score: hsl.s * hsl.s, count: 1, hsl });
            }
        }

        if (binsMap.size === 0) return [];

        // --- Étape 2 : Calcul du score hybride (saturation × rareté) ---
        // Le boost de rareté = 1 / log(count + 1) normalisé
        // → Une couleur présente sur 5 pixels mais très saturée peut surpasser
        //   une couleur terne présente sur 500 pixels.
        const maxCount = Math.max(...Array.from(binsMap.values()).map(v => v.count));
        const scoredEntries = Array.from(binsMap.entries()).map(([hex, data]) => {
            const rarityBoost = 1 + (1 - Math.log(data.count + 1) / Math.log(maxCount + 1));
            const finalScore = data.score * rarityBoost;
            return { hex, finalScore, hsl: data.hsl };
        });

        // Tri décroissant par score final
        scoredEntries.sort((a, b) => b.finalScore - a.finalScore);

        // --- Étape 3 : Déduplication perceptuelle par teinte HSL ---
        // On garde une couleur seulement si elle est « différente » de toutes
        // celles déjà sélectionnées, selon un double critère :
        //   ΔH (distance angulaire de teinte) ET ΔL (distance de luminosité)
        const HUE_THRESHOLD = 28;   // degrés — seuil de distinction de teinte
        const LUM_THRESHOLD = 0.15; // seuil de distinction de luminosité

        const selected = [];

        for (const entry of scoredEntries) {
            const { hex, hsl } = entry;
            let isDistinct = true;

            for (const sel of selected) {
                const dH = hueDistance(hsl.h, sel.hsl.h);
                const dL = Math.abs(hsl.l - sel.hsl.l);
                // Deux couleurs sont « trop proches » si teinte ET luminosité proches
                if (dH < HUE_THRESHOLD && dL < LUM_THRESHOLD) {
                    isDistinct = false;
                    break;
                }
            }

            if (isDistinct) {
                selected.push(entry);
                if (selected.length >= maxColors) break;
            }
        }

        return selected.map(e => e.hex);
    }

    // --- GESTION DE L'INPUT LOGO ---
    if (logoInput) {
        logoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                let name = file.name.replace(/\.[^/.]+$/, "");
                name = name.replace(/[-_]/g, ' ');
                paletteTitle.value = name.charAt(0).toUpperCase() + name.slice(1);

                const reader = new FileReader();
                reader.onload = function (event) {
                    const img = new Image();
                    img.onload = function () {
                        // Résolution plus élevée pour mieux capturer les liserés fins
                        const CANVAS_SIZE = 120;
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = CANVAS_SIZE;
                        canvas.height = Math.round(CANVAS_SIZE * (img.height / img.width));
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;

                        // Extraction chromatique perceptuelle v2
                        const distinctHex = extractDistinctColors(imgData, 6);

                        // --- Affichage dans le bloc d'analyse dédié ---
                        if (logoAnalysisContainer) {
                            logoAnalysisContainer.innerHTML = '';
                            if (distinctHex.length === 0) {
                                logoAnalysisContainer.innerHTML = '<span style="font-size:0.8rem; color:#e53e3e;">Aucune couleur distincte détectée.</span>';
                            } else {
                                distinctHex.forEach((hex, idx) => {
                                    const swatch = document.createElement('div');
                                    swatch.className = 'swatch-item';
                                    swatch.style.cssText = `width: 30px; height: 30px; border-radius: 4px; background-color: ${hex}; cursor: pointer; border: 2px solid rgba(255,255,255,0.3);`;
                                    swatch.title = `Couleur ${idx + 1}: ${hex} — Cliquer pour ajouter à la palette`;

                                    swatch.addEventListener('click', () => {
                                        if (!currentPaletteColors.includes(hex)) {
                                            currentPaletteColors.push(hex);
                                            updateSwatches();
                                            triggerAutoSave();
                                        }
                                    });

                                    logoAnalysisContainer.appendChild(swatch);
                                });
                            }
                        }

                        // Injecter les deux premières couleurs comme primaire / secondaire
                        if (distinctHex.length > 0) {
                            primaryInput.value = distinctHex[0];
                            secondaryInput.value = distinctHex[1] || distinctHex[0];
                            updatePalettePreview(primaryInput.value, secondaryInput.value);
                        }
                    };

                    img.src = event.target.result;
                    logoPreviewImg.src = event.target.result;
                    logoPreviewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (tagInput) {
        tagInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && tagInput.value.trim() !== '') {
                e.preventDefault();
                const val = tagInput.value.trim().toLowerCase();
                if (!currentPaletteKeywords.includes(val)) {
                    currentPaletteKeywords.push(val);
                    renderTags();
                    filterPresetsByTags();
                    triggerAutoSave();
                }
                tagInput.value = '';
            }
        });
    }

    if (primaryInput && secondaryInput) {
        primaryInput.addEventListener('input', () => updatePalettePreview(primaryInput.value, secondaryInput.value));
        secondaryInput.addEventListener('input', () => updatePalettePreview(primaryInput.value, secondaryInput.value));
    }

    if (paletteTitle) {
        paletteTitle.value = "Nom du Projet";
        paletteTitle.addEventListener('input', () => {
            triggerAutoSave();
        });
    }

    if (savePaletteBtn) {
        savePaletteBtn.addEventListener('click', () => {
            const currentPalette = {
                title: paletteTitle.value,
                colors: [...currentPaletteColors],
                keywords: [...currentPaletteKeywords],
                primary: primaryInput.value,
                secondary: secondaryInput.value
            };

            const savedData = localStorage.getItem('palettor_saved_list');
            const palettes = savedData ? JSON.parse(savedData) : [];

            if (currentEditIndex !== null && palettes[currentEditIndex]) {
                palettes[currentEditIndex] = currentPalette;
            } else {
                palettes.push(currentPalette);
                currentEditIndex = palettes.length - 1;
            }

            localStorage.setItem('palettor_saved_list', JSON.stringify(palettes));
            loadSavedPalettes();

            savePaletteBtn.textContent = 'Sauvegardé !';
            setTimeout(() => { savePaletteBtn.textContent = 'Sauvegarder'; }, 1500);
        });
    }

    loadPresets();
    updatePalettePreview(primaryInput.value, secondaryInput.value);
    loadSavedPalettes();
});