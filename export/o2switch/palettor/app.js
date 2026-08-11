document.addEventListener('DOMContentLoaded', () => {
    const primaryInput = document.getElementById('primaryOklch');
    const secondaryInput = document.getElementById('secondaryOklch');
    const presetsGrid = document.getElementById('presetsGrid');
    const savePaletteBtn = document.getElementById('savePaletteBtn');
    const logoInput = document.getElementById('logoInput');
    const paletteTitle = document.getElementById('paletteTitle');
    const logoPreviewImg = document.getElementById('logoPreviewImg');
    const colorAnalysisContainer = document.getElementById('colorAnalysisContainer');
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
        document.documentElement.style.setProperty('--dynamic-muted', 'oklch(0.95 0.02 250)');

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
            const lastColor = currentPaletteColors[currentPaletteColors.length - 1];
            const match = lastColor.match(/oklch\([\d.]+\s+[\d.]+\s+([\d.]+)\)/);
            const currentHue = match ? parseFloat(match[1]) : 200;
            const nextHue = (currentHue + 40) % 360;
            const newColor = `oklch(0.7 0.12 ${nextHue})`;

            currentPaletteColors.push(newColor);
            updateSwatches();
            triggerAutoSave();
        });

        colorAnalysisContainer.appendChild(addBtn);

        const hint = document.createElement('div');
        hint.className = 'color-hint';
        hint.textContent = 'Astuce : Clic droit (PC) ou appui long (Mobile) sur une couleur pour la supprimer. Ctrl + clic sur un mood pour le supprimer.';
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

        const targetList = allPresetsData;

        targetList.forEach(palette => {
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
                    // Charge uniquement les vrais mots-clés associés, en filtrant strictement pour ne pas inclure le titre du mood
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
            addBtn.setAttribute('aria-label', "Ajouter un mood");
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
            inputKeywords.placeholder = 'Mots-clés (ex: voiture, velo)...';

            const handleValidation = () => {
                const newTitle = inputTitle.value.trim();
                if (newTitle !== '') {
                    const rawKeywords = inputKeywords.value.trim();
                    // Récupère uniquement les mots-clés saisis, sans y inclure de force le titre du mood
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
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleValidation();
                } else if (e.key === 'Escape') {
                    isCreatingMood = false;
                    filterPresetsByTags();
                }
            });

            inputKeywords.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleValidation();
                } else if (e.key === 'Escape') {
                    isCreatingMood = false;
                    filterPresetsByTags();
                }
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
                deleteBtn.title = "Supprimer cette palette";

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

    if (logoInput) {
        logoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                let name = file.name.replace(/\.[^/.]+$/, "");
                name = name.replace(/[-_]/g, ' ');
                paletteTitle.value = name.charAt(0).toUpperCase() + name.slice(1);

                const reader = new FileReader();
                reader.onload = function (event) {
                    logoPreviewImg.src = event.target.result;
                    logoPreviewImg.style.display = 'block';
                }
                reader.readAsDataURL(file);

                const newPrimary = `oklch(0.58 0.22 35)`;
                const newSecondary = `oklch(0.82 0.15 85)`;

                primaryInput.value = newPrimary;
                secondaryInput.value = newSecondary;
                currentEditIndex = null;
                currentPaletteColors = [newPrimary, newSecondary, `oklch(0.70 0.18 120)`];
                currentPaletteKeywords = ["logo", "annonceur"];
                renderTags();
                filterPresetsByTags();
                updatePalettePreview(newPrimary, newSecondary);
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
                keywords: [...currentPaletteKeywords]
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