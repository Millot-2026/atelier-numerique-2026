document.addEventListener('DOMContentLoaded', () => {
    const cardsGrid = document.getElementById('cardsGrid');
    const codeBox = document.getElementById('codeBox');
    const tagInput = document.getElementById('tagInput');
    const applyBtn = document.getElementById('applyBtn');
    const searchInput = document.getElementById('searchInput');
    const copyBtn = document.getElementById('copyBtn');
    const exportBtn = document.getElementById('exportBtn');
    const resetBtn = document.getElementById('resetBtn');

    // Panneau coulissant (Drawer) Elements
    const openDrawerBtn = document.getElementById('openDrawerBtn');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');
    const filterDrawer = document.getElementById('filterDrawer');
    const drawerOverlay = document.getElementById('drawerOverlay');
    const clearFilterBtn = document.getElementById('clearFilterBtn');
    const applyDrawerBtn = document.getElementById('applyDrawerBtn');

    let activeTagFilter = '';

    // Éléments du mode responsive (onglets mobile)
    const mainContainer = document.getElementById('mainContainer');
    const tabShowGallery = document.getElementById('tabShowGallery');
    const tabShowEditor = document.getElementById('tabShowEditor');

    function switchMobileView(view) {
        if (!mainContainer) return;

        if (view === 'editor') {
            mainContainer.classList.remove('view-gallery');
            mainContainer.classList.add('view-editor');
            if (tabShowEditor) tabShowEditor.classList.add('active');
            if (tabShowGallery) tabShowGallery.classList.remove('active');
        } else {
            mainContainer.classList.remove('view-editor');
            mainContainer.classList.add('view-gallery');
            if (tabShowGallery) tabShowGallery.classList.add('active');
            if (tabShowEditor) tabShowEditor.classList.remove('active');
        }
    }

    if (tabShowGallery) {
        tabShowGallery.addEventListener('click', (e) => {
            e.preventDefault();
            switchMobileView('gallery');
        });
    }

    if (tabShowEditor) {
        tabShowEditor.addEventListener('click', (e) => {
            e.preventDefault();
            switchMobileView('editor');
        });
    }

    // Gestion de l'ouverture/fermeture du tiroir
    function toggleDrawer(open) {
        if (open) {
            if (filterDrawer) filterDrawer.classList.add('open');
            if (drawerOverlay) drawerOverlay.classList.add('open');
        } else {
            if (filterDrawer) filterDrawer.classList.remove('open');
            if (drawerOverlay) drawerOverlay.classList.remove('open');
        }
    }

    if (openDrawerBtn) openDrawerBtn.addEventListener('click', () => toggleDrawer(true));
    if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', () => toggleDrawer(false));
    if (drawerOverlay) drawerOverlay.addEventListener('click', () => toggleDrawer(false));
    if (applyDrawerBtn) applyDrawerBtn.addEventListener('click', () => toggleDrawer(false));

    let dynamicStyle = document.getElementById('dynamic-texture-style');
    if (!dynamicStyle) {
        dynamicStyle = document.createElement('style');
        dynamicStyle.id = 'dynamic-texture-style';
        document.head.appendChild(dynamicStyle);
    }

    async function loadTextures() {
        const stored = localStorage.getItem('texturor_textures');
        if (stored) {
            try {
                return JSON.parse(stored);
            } catch (e) {
                console.error("Erreur de lecture du localStorage", e);
            }
        }

        try {
            const response = await fetch('data/textures.json');
            if (!response.ok) throw new Error("Impossible de charger le fichier JSON");
            const data = await response.json();
            const textures = data.textures || [];
            localStorage.setItem('texturor_textures', JSON.stringify(textures));
            return textures;
        } catch (error) {
            console.error("Erreur de chargement JSON:", error);
            return [];
        }
    }

    function saveTexturesLocally(textures) {
        localStorage.setItem('texturor_textures', JSON.stringify(textures));
    }

    async function renderGallery() {
        const textures = await loadTextures();
        if (!cardsGrid) return;

        cardsGrid.innerHTML = '';
        let allCss = '';

        const textSearchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const tagFilterTerm = activeTagFilter.toLowerCase().trim();

        // Indique si un filtre par tag est actif sur le bouton du header
        if (openDrawerBtn) {
            if (activeTagFilter) {
                openDrawerBtn.classList.add('active');
            } else {
                openDrawerBtn.classList.remove('active');
            }
        }

        textures.forEach(tex => {
            allCss += '\n' + tex.scss + '\n';

            const tagString = (tex.tags || []).join(' ').toLowerCase();
            const titleString = tex.title.toLowerCase();

            const matchesText = !textSearchTerm || titleString.includes(textSearchTerm) || tagString.includes(textSearchTerm);
            const matchesTag = !tagFilterTerm || tagString.includes(tagFilterTerm);

            if (!matchesText || !matchesTag) {
                return;
            }

            const classMatch = tex.scss.match(/\.([a-zA-Z0-9_-]+)/);
            const className = classMatch ? classMatch[1] : 'texture-custom';

            const card = document.createElement('div');
            card.className = 'texture-card';
            card.dataset.id = tex.id;

            const tagsHtml = (tex.tags || []).map(t => `<span class="tag">${t.trim()}</span>`).join('');

            const isSelected = localStorage.getItem('texture_selected_' + tex.id) === 'true';

            card.innerHTML = `
                <div class="card-preview ${className}">
                    <div class="texture-indicator ${isSelected ? 'selected' : ''}" 
                         data-texture-id="${tex.id}" 
                         onclick="event.stopPropagation(); toggleTextureSelection(this, '${tex.id}', '${className}')">
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-title">${tex.title}</div>
                    <div class="card-footer-row">
                        <div class="card-tags">${tagsHtml}</div>
                        <button class="card-copy-btn" title="Copier le code SCSS">Copier</button>
                    </div>
                </div>
            `;

            card.addEventListener('click', (e) => {
                if (e.target.classList.contains('card-copy-btn') || e.target.classList.contains('texture-indicator')) return;
                codeBox.value = tex.scss;
                tagInput.value = (tex.tags || []).join(', ');

                if (window.innerWidth <= 900) {
                    switchMobileView('editor');
                }
            });

            const cardCopyBtn = card.querySelector('.card-copy-btn');
            cardCopyBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                navigator.clipboard.writeText(tex.scss).then(() => {
                    cardCopyBtn.textContent = 'Copié !';
                    setTimeout(() => { cardCopyBtn.textContent = 'Copier'; }, 1500);
                });
            });

            cardsGrid.appendChild(card);
        });

        dynamicStyle.textContent = allCss;
    }

    window.toggleTextureSelection = function (element, textureId, className) {
        const wasSelected = element.classList.contains('selected');

        document.querySelectorAll('.texture-indicator').forEach(ind => {
            ind.classList.remove('selected');
            const otherId = ind.getAttribute('data-texture-id');
            if (otherId) localStorage.setItem('texture_selected_' + otherId, 'false');
        });

        loadTextures().then(textures => {
            const tex = textures.find(t => t.id === textureId);
            const textureScss = tex ? tex.scss : '';

            if (!wasSelected) {
                element.classList.add('selected');
                localStorage.setItem('texture_selected_' + textureId, 'true');
                localStorage.setItem('centrale_active_texture_class', className);
                localStorage.setItem('centrale_active_texture_scss', textureScss);

                window.parent.postMessage({
                    type: 'updateCentralBackground',
                    className: className,
                    scss: textureScss,
                    selected: true
                }, '*');
            } else {
                localStorage.removeItem('centrale_active_texture_class');
                localStorage.removeItem('centrale_active_texture_scss');

                window.parent.postMessage({
                    type: 'updateCentralBackground',
                    className: className,
                    scss: '',
                    selected: false
                }, '*');
            }
        });
    };

    // Gestion de la sélection d'un tag dans le tiroir
    document.querySelectorAll('.drawer-tag-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const selectedTag = e.target.dataset.tag;

            if (activeTagFilter === selectedTag) {
                activeTagFilter = '';
                e.target.classList.remove('active');
            } else {
                document.querySelectorAll('.drawer-tag-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                activeTagFilter = selectedTag;
            }

            renderGallery();
        });
    });

    if (clearFilterBtn) {
        clearFilterBtn.addEventListener('click', () => {
            activeTagFilter = '';
            document.querySelectorAll('.drawer-tag-btn').forEach(b => b.classList.remove('active'));
            renderGallery();
        });
    }

    renderGallery();

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            renderGallery();
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', async () => {
            const scssContent = codeBox.value.trim();
            if (!scssContent) return;

            const classMatch = scssContent.match(/\.([a-zA-Z0-9_-]+)/);
            if (!classMatch) {
                alert("Erreur : ton code SCSS doit contenir une classe (ex: .texture-nom)");
                return;
            }

            const className = classMatch[1];
            const title = className.replace('texture-', '').replace(/-/g, ' ');
            const rawTags = tagInput.value.trim();
            const tags = rawTags ? rawTags.split(',').map(t => t.trim()) : [title, 'personnalisé'];

            let textures = await loadTextures();
            const existingIndex = textures.findIndex(t => t.scss.includes(`.${className}`));

            const newTexture = {
                id: className,
                title: title.charAt(0).toUpperCase() + title.slice(1),
                tags: tags,
                scss: scssContent
            };

            if (existingIndex >= 0) {
                textures[existingIndex] = newTexture;
            } else {
                textures.push(newTexture);
            }

            saveTexturesLocally(textures);
            renderGallery();

            if (window.innerWidth <= 900) {
                switchMobileView('gallery');
            }
        });
    }

    if (copyBtn && codeBox) {
        copyBtn.addEventListener('click', () => {
            codeBox.select();
            document.execCommand('copy');
            const originalText = copyBtn.textContent;
            copyBtn.textContent = 'Copié !';
            setTimeout(() => { copyBtn.textContent = originalText; }, 1500);
        });
    }

    if (exportBtn) {
        exportBtn.addEventListener('click', async () => {
            const textures = await loadTextures();
            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify({ textures }, null, 2));
            const downloadAnchor = document.createElement('a');
            downloadAnchor.setAttribute("href", dataStr);
            downloadAnchor.setAttribute("download", "textures.json");
            document.body.appendChild(downloadAnchor);
            downloadAnchor.click();
            downloadAnchor.remove();
        });
    }

    // Le bouton Réinitialiser vide UNIQUEMENT la zone d'édition
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (codeBox) codeBox.value = '';
            if (tagInput) tagInput.value = '';
        });
    }
});