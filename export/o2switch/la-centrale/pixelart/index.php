<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PixelArt Studio — Atelier Nomade</title>
    <style>
        :root {
            --bg-dark: #1a1a1a;
            --panel-bg: #222222;
            --border-color: #333333;
            --accent: #f39c12;
            --text-main: #f0f0f0;
            --text-muted: #888888;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; border-radius: 0 !important; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            width: 100%;
            overflow: hidden;
        }
        header {
            height: 50px;
            background: var(--panel-bg);
            border-bottom: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            padding: 0 10px 0 15px;
            flex-shrink: 0;
            gap: 12px;
            overflow: hidden;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        header h1 { font-size: 1rem; color: var(--accent); white-space: nowrap; }
        
        #projectName {
            width: 100% !important;
            height: 36px !important;
            background-color: #1a1a1a !important;
            border: 1px solid #555555 !important;
            color: #f0f0f0 !important;
            padding: 0 10px !important;
            line-height: 36px;
            vertical-align: middle;
            transition: border-color 0.2s;
        }
        #projectName:hover, #projectName:focus {
            border-color: var(--accent) !important;
            outline: none !important;
        }

        .header-actions { 
            display: flex; 
            gap: 8px; 
            align-items: center; 
            flex-wrap: nowrap; 
        }
        
        .main-container {
            display: flex;
            align-items: stretch;
            position: relative;
        }
        
        sidebar {
            width: 320px;
            background: var(--panel-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 12px;
            gap: 10px;
            flex-shrink: 0;
        }

        details.tool-section, div.tool-section {
            background: #1a1a1a;
            border: 1px solid var(--border-color);
            padding: 8px 10px;
            transition: border-color 0.2s, background 0.2s;
        }
        details.tool-section.dragging, div.tool-section.dragging {
            opacity: 0.3;
            border: 2px dashed var(--accent);
        }
        
        details.tool-section summary {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            cursor: pointer;
            font-weight: bold;
            user-select: none;
            outline: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .summary-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .drag-handle {
            cursor: grab;
            color: var(--text-muted);
            font-size: 1.1rem;
            padding: 0 4px;
            letter-spacing: -2px;
            display: inline-block;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
        details.tool-section summary::-webkit-details-marker {
            display: none;
        }
        details.tool-section summary::after {
            content: '▼';
            font-size: 0.65rem;
            transition: transform 0.2s;
        }
        details.tool-section[open] summary::after {
            transform: rotate(180deg);
        }
        .section-content {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }



/* Style général des boutons */
        button, .btn {
            background: #333333;
            color: var(--text-main);
            border: none;
            padding: 0 10px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: background 0.2s;
            text-align: center;
            height: 36px;
            display: flex;
            align-items: center;    /* Centrage vertical */
            justify-content: center; /* Centrage horizontal */
            gap: 8px;                /* Espace entre icône et texte */
        }

        /* Spécifique pour les boutons avec icônes pour forcer le même comportement */
        .tools-grid button {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 36px;
            padding: 0 10px;
        }








        button:hover, .btn:hover { background: #444444; }
        button.active { background: var(--accent); color: var(--bg-dark); }










        .color-picker-box {
            background: #1a1a1a;
            border: 1px solid var(--border-color);
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .picker-canvas-container {
            position: relative;
            width: 100%;
            height: 110px;
            overflow: hidden;
            cursor: crosshair;
        }
        .picker-canvas-container canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        .hue-slider-container {
            position: relative;
            width: 100%;
            height: 14px;
            overflow: hidden;
            cursor: pointer;
        }
        .hue-slider-container canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        .picker-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .color-preview-circle {
            width: 26px;
            height: 26px;
            border: 2px solid var(--border-color);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.75rem;
        }
        .rgb-inputs {
            display: flex;
            gap: 4px;
        }
        .rgb-input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        .rgb-input-group input {
            width: 100%;
            background: #222222;
            border: 1px solid var(--border-color);
            color: white;
            text-align: center;
            padding: 4px 2px;
            font-size: 0.75rem;
        }
        .rgb-input-group label {
            font-size: 0.65rem;
            color: var(--text-muted);
            margin-top: 2px;
            text-transform: uppercase;
        }

        .color-palette {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }
        .color-swatch {
            width: 28px;
            height: 28px;
            border: 2px solid var(--border-color);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .color-swatch:hover { transform: scale(1.1); }
        .color-swatch.active-swatch { border-color: var(--accent); transform: scale(1.15); }
        .add-color-btn {
            width: 28px;
            height: 28px;
            border: 2px dashed var(--border-color);
            background: rgba(255,255,255,0.05);
            color: var(--text-main);
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
        }
        .add-color-btn:hover { background: var(--accent); color: var(--bg-dark); border-style: solid; }

        .canvas-container-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #1a1a1a;
        }
        .canvas-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: visible;
            padding: 20px;
        }
        .canvas-wrapper {
            position: relative;
            box-shadow: none;
            background: #ffffff;
            background-image: linear-gradient(45deg, #ccc 25%, transparent 25%), 
                              linear-gradient(-45deg, #ccc 25%, transparent 25%), 
                              linear-gradient(45deg, transparent 75%, #ccc 75%), 
                              linear-gradient(-45deg, transparent 75%, #ccc 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
        canvas {
            display: block;
            cursor: crosshair;
        }
        #bgCanvas, #bgCanvasMobile {
            position: absolute;
            top: 0; left: 0;
            pointer-events: none;
        }

        .bg-bottom-panel {
            background: var(--panel-bg);
            border-top: 1px solid var(--border-color);
            padding: 12px 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .bg-bottom-panel h3 {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .project-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-height: 120px;
            overflow-y: auto;
        }
        .project-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0,0,0,0.2);
            padding: 5px 8px;
            font-size: 0.75rem;
            cursor: pointer;
        }
        .project-item:hover { background: rgba(243,156,18,0.1); }
        .project-item.active { border-left: 3px solid var(--accent); }

        input[type="text"], input[type="range"], select {
            background: #1a1a1a;
            border: 1px solid var(--border-color);
            color: white;
            padding: 6px 10px;
            font-size: 0.8rem;
        }

        #autosaveStatus {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-left: 5px;
            font-style: italic;
            white-space: nowrap;
        }

        #canvasMobileSection { display: none !important; }
        #desktopCanvasContainer { display: flex !important; }

        @media (max-width: 768px) {
            body { min-height: 100vh; overflow: auto; }
            .main-container { flex-direction: column; height: auto; }
            sidebar { width: 100%; max-height: none; border-right: none; border-bottom: 1px solid var(--border-color); }
            #desktopCanvasContainer { display: none !important; }
            #canvasMobileSection { display: block !important; flex: 1; flex-direction: column; }
            #canvasMobileSection .section-content { flex: 1; display: flex; flex-direction: column; }
            .canvas-area { min-height: 350px !important; padding: 10px !important; overflow: hidden !important; }
            
            header { 
                height: auto; 
                padding: 12px; 
                gap: 8px; 
                display: flex;
                flex-direction: column; 
                align-items: stretch; 
            }
            .header-left {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
            }
            header h1 { font-size: 1rem; }
            
            .header-actions { 
                width: 100%; 
                display: grid; 
                grid-template-columns: 1fr 1fr; 
                gap: 6px; 
            }
            #projectName { 
                width: 100% !important; 
            }
            .header-actions button:nth-of-type(1) { grid-column: 1; }
            .header-actions button:nth-of-type(2) { grid-column: 2; }
            .header-actions button:nth-of-type(3) { grid-column: span 2; width: 100%; }

            .bg-bottom-panel { flex-direction: column; align-items: stretch; padding: 6px 10px; }
        }
    </style>
    <link rel="stylesheet" href="../global.css">
</head>
<body>

    <header>
        <div class="header-left">
            <h1>🎨 PixelArt Studio</h1>
            <span id="autosaveStatus"></span>
        </div>
        <input type="text" id="projectName" value="Mon Super Projet" oninput="triggerAutoSave()">
        <div class="header-actions">
            <button onclick="saveProject(false)" class="active">Sauvegarder</button>
            <button onclick="saveProject(true)">Enregistrer sous</button>
            <button onclick="exportPNG()">PNG</button>
        </div>
    </header>

    <div class="main-container">
        <sidebar id="sidebarSections">
            <details class="tool-section" id="detailsProjects" open data-section="projects" draggable="true">
                <summary>
                    <span class="summary-left"><span class="drag-handle" title="Glisser pour déplacer">⠿</span> MES PROJETS</span>
                </summary>
                <div class="section-content">
                    <div class="project-list" id="projectList"></div>
                    <button onclick="newProject()" style="width: 100%;">+ Nouveau Projet</button>
                </div>
            </details>

            <details class="tool-section" id="detailsTools" open data-section="tools" draggable="true">
                <summary>
                    <span class="summary-left"><span class="drag-handle" title="Glisser pour déplacer">⠿</span> OUTILS</span>
                </summary>
                <div class="section-content">
                    <div class="tools-grid">
                        <button id="tool-pencil" class="active" onclick="setTool('pencil')">✏️ Crayon</button>
                        <button id="tool-eraser" onclick="setTool('eraser')">🧹 Gomme</button>
                        <button id="tool-bucket" onclick="setTool('bucket')">🪣 Remplir</button>
                        <button id="tool-picker" onclick="setTool('picker')">🧪 Pipette</button>
                    </div>
                </div>
            </details>

            <div class="tool-section" id="canvasMobileSection" data-section="canvasArea" draggable="true" style="background: #1a1a1a; border: 1px solid var(--border-color); padding: 8px 10px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: bold; user-select: none; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span class="summary-left"><span class="drag-handle" title="Glisser pour déplacer">⠿</span> ZONE DE DESSIN</span>
                </div>
                <div class="section-content">
                    <div class="canvas-area">
                        <div class="canvas-wrapper" id="canvasWrapperMobile">
                            <canvas id="bgCanvasMobile"></canvas>
                            <canvas id="pixelCanvasMobile"></canvas>
                        </div>
                    </div>
                    <div class="bg-bottom-panel" style="border-top: none; padding: 4px 0 0 0;">
                        <div>
                            <h3>Arrière-plan Modèle</h3>
                            <input type="file" id="bgInputMobile" accept="image/png, image/jpeg" onchange="loadBackgroundMobile(event); triggerAutoSave();" style="font-size: 0.75rem; color: var(--text-muted);">
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center; justify-content: space-between; margin-top: 6px;">
                            <button onclick="toggleBackground()" id="toggleBgBtnMobile">Masquer BG</button>
                            <button onclick="removeBackground(); triggerAutoSave();" style="background: #7f1d1d;">Supprimer</button>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-top: 6px;">
                            <label style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap;">Opacité : <span id="opacityValMobile">50</span>%</label>
                            <input type="range" id="bgOpacityMobile" min="0" max="1" step="0.05" value="0.5" oninput="updateOpacity(this.value); triggerAutoSave();" style="flex: 1;">
                        </div>
                    </div>
                </div>
            </div>

            <details class="tool-section" id="detailsColor" open data-section="color" draggable="true">
                <summary>
                    <span class="summary-left"><span class="drag-handle" title="Glisser pour déplacer">⠿</span> COULEUR</span>
                </summary>
                <div class="section-content">
                    <div class="color-picker-box">
                        <div class="picker-canvas-container" id="svContainer">
                            <canvas id="svCanvas"></canvas>
                        </div>
                        <div class="hue-slider-container" id="hueContainer">
                            <canvas id="hueCanvas"></canvas>
                        </div>
                        <div class="picker-info-row">
                            <div class="color-preview-circle" id="previewCircle" style="background-color: #000000;"></div>
                            <div class="rgb-inputs">
                                <div class="rgb-input-group">
                                    <input type="number" id="inputR" min="0" max="255" value="0" onchange="onRgbInputChange()">
                                    <label>R</label>
                                </div>
                                <div class="rgb-input-group">
                                    <input type="number" id="inputG" min="0" max="255" value="0" onchange="onRgbInputChange()">
                                    <label>G</label>
                                </div>
                                <div class="rgb-input-group">
                                    <input type="number" id="inputB" min="0" max="255" value="0" onchange="onRgbInputChange()">
                                    <label>B</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="color-palette" id="colorPalette"></div>
                    <div style="font-size: 0.65rem; color: var(--text-muted);">Clic droit sur une case pour la supprimer.</div>
                </div>
            </details>

            <details class="tool-section" id="detailsGrid" open data-section="grid" draggable="true">
                <summary>
                    <span class="summary-left"><span class="drag-handle" title="Glisser pour déplacer">⠿</span> GRILLE</span>
                </summary>
                <div class="section-content">
                    <select id="gridSize" onchange="changeGridSize(this.value)" style="width: 100%;">
                        <option value="8">8 x 8</option>
                        <option value="16" selected>16 x 16</option>
                        <option value="32">32 x 32</option>
                        <option value="64">64 x 64</option>
                    </select>
                </div>
            </details>
        </sidebar>

        <div class="canvas-container-area" id="desktopCanvasContainer">
            <div class="canvas-area">
                <div class="canvas-wrapper" id="canvasWrapper">
                    <canvas id="bgCanvas"></canvas>
                    <canvas id="pixelCanvas"></canvas>
                </div>
            </div>
            <div class="bg-bottom-panel">
                <div>
                    <h3>Arrière-plan Modèle</h3>
                    <input type="file" id="bgInput" accept="image/png, image/jpeg" onchange="loadBackground(event); triggerAutoSave();" style="font-size: 0.75rem; color: var(--text-muted);">
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <button onclick="toggleBackground()" id="toggleBgBtn">Masquer BG</button>
                    <button onclick="removeBackground(); triggerAutoSave();" style="background: #7f1d1d;">Supprimer</button>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; min-width: 200px;">
                    <label style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap;">Opacité : <span id="opacityVal">50</span>%</label>
                    <input type="range" id="bgOpacity" min="0" max="1" step="0.05" value="0.5" oninput="updateOpacity(this.value); triggerAutoSave();" style="flex: 1;">
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentProjectId = null;
        let gridSize = 16;
        let pixelSize = 25; 
        let currentTool = 'pencil';
        let currentColor = '#000000';
        let isDrawing = false;
        let gridData = []; 
        let projectsList = [];
        let customPalette = ['#000000'];
        let activeSwatchIndex = 0;
        let currentHue = 0, currentSat = 0, currentVal = 0; 
        let bgImg = null, showBg = true, bgOpacityVal = 0.5, autoSaveTimer = null, isInitialized = false;

        const canvas = document.getElementById('pixelCanvas');
        const ctx = canvas.getContext('2d');
        const bgCanvas = document.getElementById('bgCanvas');
        const bgCtx = bgCanvas.getContext('2d');
        const canvasMobile = document.getElementById('pixelCanvasMobile');
        const ctxMobile = canvasMobile.getContext('2d');
        const bgCanvasMobile = document.getElementById('bgCanvasMobile');
        const bgCtxMobile = bgCanvasMobile.getContext('2d');
        const svCanvas = document.getElementById('svCanvas');
        const svCtx = svCanvas.getContext('2d');
        const hueCanvas = document.getElementById('hueCanvas');
        const hueCtx = hueCanvas.getContext('2d');

        window.onload = function() {
            initColorPicker();
            initDragAndDrop();
            initDetailsListeners();
            loadProjectsList();

            [canvas, canvasMobile].forEach(c => {
                c.addEventListener('mousedown', startDrawing);
                c.addEventListener('mousemove', draw);
                c.addEventListener('touchstart', handleTouch, { passive: false });
                c.addEventListener('touchmove', handleTouch, { passive: false });
            });
            window.addEventListener('mouseup', stopDrawing);
            window.addEventListener('touchend', stopDrawing);
        };

        function notifyParentHeight() {
            const height = document.body.scrollHeight;
            window.parent.postMessage({ type: 'resizeIframe', height: height }, '*');
        }

        const observer = new ResizeObserver(() => {
            notifyParentHeight();
        });
        observer.observe(document.body);

        function initDetailsListeners() {
            document.querySelectorAll('details.tool-section').forEach(det => {
                det.addEventListener('toggle', () => {
                    notifyParentHeight();
                    triggerAutoSave();
                });
            });
        }

        function initDragAndDrop() {
            const sidebar = document.getElementById('sidebarSections');
            let draggedItem = null;
            sidebar.querySelectorAll('.tool-section').forEach(section => {
                section.addEventListener('dragstart', (e) => {
                    draggedItem = section;
                    section.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                });
                section.addEventListener('dragend', () => {
                    section.classList.remove('dragging');
                    draggedItem = null;
                    triggerAutoSave();
                    notifyParentHeight();
                });
                section.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    if (!draggedItem) return;
                    const afterElement = getDragAfterElement(sidebar, e.clientY);
                    if (afterElement == null) sidebar.appendChild(draggedItem);
                    else sidebar.insertBefore(draggedItem, afterElement);
                });
            });
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.tool-section:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) return { offset: offset, element: child };
                else return closest;
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        function initColorPicker() {
            svCanvas.width = 240; svCanvas.height = 110;
            hueCanvas.width = 240; hueCanvas.height = 14;
            drawHueSlider(); drawSVBox();
            let isDraggingSV = false;
            svCanvas.addEventListener('mousedown', (e) => { isDraggingSV = true; updateSVFromEvent(e); });
            window.addEventListener('mousemove', (e) => { if (isDraggingSV) updateSVFromEvent(e); });
            window.addEventListener('mouseup', () => { isDraggingSV = false; });
            let isDraggingHue = false;
            hueCanvas.addEventListener('mousedown', (e) => { isDraggingHue = true; updateHueFromEvent(e); });
            window.addEventListener('mousemove', (e) => { if (isDraggingHue) updateHueFromEvent(e); });
            window.addEventListener('mouseup', () => { isDraggingHue = false; });
        }

        function drawHueSlider() {
            const gradient = hueCtx.createLinearGradient(0, 0, hueCanvas.width, 0);
            for (let i = 0; i <= 360; i += 60) gradient.addColorStop(i / 360, `hsl(${i}, 100%, 50%)`);
            hueCtx.fillStyle = gradient;
            hueCtx.fillRect(0, 0, hueCanvas.width, hueCanvas.height);
        }

        function drawSVBox() {
            svCtx.fillStyle = `hsl(${currentHue}, 100%, 50%)`;
            svCtx.fillRect(0, 0, svCanvas.width, svCanvas.height);
            const horizGrad = svCtx.createLinearGradient(0, 0, svCanvas.width, 0);
            horizGrad.addColorStop(0, 'rgba(255, 255, 255, 1)'); horizGrad.addColorStop(1, 'rgba(255, 255, 255, 0)');
            svCtx.fillStyle = horizGrad; svCtx.fillRect(0, 0, svCanvas.width, svCanvas.height);
            const vertGrad = svCtx.createLinearGradient(0, 0, 0, svCanvas.height);
            vertGrad.addColorStop(0, 'rgba(0, 0, 0, 0)'); vertGrad.addColorStop(1, 'rgba(0, 0, 0, 1)');
            svCtx.fillStyle = vertGrad; svCtx.fillRect(0, 0, svCanvas.width, svCanvas.height);
        }

        function updateHueFromEvent(e) {
            const rect = hueCanvas.getBoundingClientRect();
            let x = Math.max(0, Math.min(e.clientX - rect.left, hueCanvas.width));
            currentHue = (x / hueCanvas.width) * 360;
            drawSVBox(); updateColorFromHSV();
        }

        function updateSVFromEvent(e) {
            const rect = svCanvas.getBoundingClientRect();
            let x = Math.max(0, Math.min(e.clientX - rect.left, svCanvas.width));
            let y = Math.max(0, Math.min(e.clientY - rect.top, svCanvas.height));
            currentSat = x / svCanvas.width; currentVal = 1 - (y / svCanvas.height);
            updateColorFromHSV();
        }

        function updateColorFromHSV() {
            const rgb = hsvToRgb(currentHue, currentSat, currentVal);
            applyNewColor(rgbToHex(rgb.r, rgb.g, rgb.b));
        }

        function applyNewColor(hex) {
            currentColor = hex;
            document.getElementById('previewCircle').style.backgroundColor = hex;
            document.getElementById('previewCircle').innerHTML = '';
            const rgb = hexToRgb(hex);
            if (rgb) {
                document.getElementById('inputR').value = rgb.r;
                document.getElementById('inputG').value = rgb.g;
                document.getElementById('inputB').value = rgb.b;
            }
            customPalette[activeSwatchIndex] = hex;
            renderPalette();
            triggerAutoSave();
        }

        function onRgbInputChange() {
            if (!isInitialized) return;
            const r = parseInt(document.getElementById('inputR').value) || 0;
            const g = parseInt(document.getElementById('inputG').value) || 0;
            const b = parseInt(document.getElementById('inputB').value) || 0;
            const hex = rgbToHex(Math.min(255, Math.max(0, r)), Math.min(255, Math.max(0, g)), Math.min(255, Math.max(0, b)));
            const hsv = rgbToHsv(r, g, b);
            currentHue = hsv.h; currentSat = hsv.s; currentVal = hsv.v;
            drawSVBox(); applyNewColor(hex);
        }

        function hsvToRgb(h, s, v) {
            let r, g, b, i = Math.floor(h / 60), f = h / 60 - i, p = v * (1 - s), q = v * (1 - f * s), t = v * (1 - (1 - f) * s);
            switch (i % 6) {
                case 0: r = v, g = t, b = p; break;
                case 1: r = q, g = v, b = p; break;
                case 2: r = p, g = v, b = t; break;
                case 3: r = p, g = q, b = v; break;
                case 4: r = t, g = p, b = v; break;
                case 5: r = v, g = p, b = q; break;
            }
            return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
        }

        function rgbToHsv(r, g, b) {
            r /= 255; g /= 255; b /= 255;
            let max = Math.max(r, g, b), min = Math.min(r, g, b), h, s, v = max, d = max - min;
            s = max === 0 ? 0 : d / max;
            if (max === min) h = 0;
            else {
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            return { h: h * 360, s: s, v: v };
        }

        function rgbToHex(r, g, b) {
            return "#" + [r, g, b].map(x => { const hex = x.toString(16); return hex.length === 1 ? "0" + hex : hex; }).join('');
        }

        function hexToRgb(hex) {
            let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? { r: parseInt(result[1], 16), g: parseInt(result[2], 16), b: parseInt(result[3], 16) } : null;
        }

        function setCurrentColor(col) {
            currentColor = col;
            const preview = document.getElementById('previewCircle');
            if (typeof col === 'object' && col.empty) {
                preview.style.backgroundColor = '#ffffff'; preview.innerHTML = '?';
                document.getElementById('inputR').value = 0; document.getElementById('inputG').value = 0; document.getElementById('inputB').value = 0;
            } else {
                preview.style.backgroundColor = col; preview.innerHTML = '';
                const rgb = hexToRgb(col);
                if (rgb) {
                    document.getElementById('inputR').value = rgb.r; document.getElementById('inputG').value = rgb.g; document.getElementById('inputB').value = rgb.b;
                    const hsv = rgbToHsv(rgb.r, rgb.g, rgb.b);
                    currentHue = hsv.h; currentSat = hsv.s; currentVal = hsv.v;
                    drawSVBox();
                }
            }
            renderPalette();
        }

        function renderPalette() {
            const paletteEl = document.getElementById('colorPalette');
            paletteEl.innerHTML = '';
            customPalette.forEach((item, index) => {
                const swatch = document.createElement('div');
                const isEmpty = (typeof item === 'object' && item.empty);
                swatch.className = 'color-swatch' + (index === activeSwatchIndex ? ' active-swatch' : '');
                if (isEmpty) { swatch.style.backgroundColor = '#ffffff'; swatch.style.color = '#000000'; swatch.innerHTML = '?'; }
                else { swatch.style.backgroundColor = item; swatch.innerHTML = ''; }
                swatch.onclick = () => { activeSwatchIndex = index; setCurrentColor(item); };
                swatch.oncontextmenu = (e) => {
                    e.preventDefault();
                    if (customPalette.length > 1) {
                        customPalette.splice(index, 1);
                        if (activeSwatchIndex >= customPalette.length) activeSwatchIndex = customPalette.length - 1;
                        setCurrentColor(customPalette[activeSwatchIndex]);
                        triggerAutoSave();
                    } else alert("La palette doit contenir au moins une case.");
                };
                paletteEl.appendChild(swatch);
            });
            const addBtn = document.createElement('button');
            addBtn.type = 'button'; addBtn.className = 'add-color-btn'; addBtn.innerHTML = '+'; addBtn.title = 'Créer une nouvelle case vide';
            addBtn.onclick = (e) => {
                e.preventDefault();
                customPalette.push({ empty: true });
                activeSwatchIndex = customPalette.length - 1;
                setCurrentColor({ empty: true });
                triggerAutoSave();
            };
            paletteEl.appendChild(addBtn);
        }

        function initGrid(savedPixels = null) {
            canvas.width = gridSize * pixelSize; canvas.height = gridSize * pixelSize;
            bgCanvas.width = canvas.width; bgCanvas.height = canvas.height;
            canvasMobile.width = canvas.width; canvasMobile.height = canvas.height;
            bgCanvasMobile.width = canvas.width; bgCanvasMobile.height = canvas.height;
            if (savedPixels && savedPixels.length === gridSize) gridData = savedPixels;
            else {
                gridData = [];
                for (let y = 0; y < gridSize; y++) {
                    let row = []; for (let x = 0; x < gridSize; x++) row.push(null);
                    gridData.push(row);
                }
            }
            redraw(); redrawBg();
        }

        function redraw() {
            [ctx, ctxMobile].forEach(cContext => {
                cContext.clearRect(0, 0, canvas.width, canvas.height);
                for (let y = 0; y < gridSize; y++) {
                    for (let x = 0; x < gridSize; x++) {
                        if (gridData[y][x]) {
                            cContext.fillStyle = gridData[y][x];
                            cContext.fillRect(x * pixelSize, y * pixelSize, pixelSize, pixelSize);
                        }
                    }
                }
                cContext.strokeStyle = 'rgba(255, 255, 255, 0.05)'; cContext.lineWidth = 0.5;
                for (let i = 0; i <= gridSize; i++) {
                    cContext.beginPath(); cContext.moveTo(i * pixelSize, 0); cContext.lineTo(i * pixelSize, canvas.height); cContext.stroke();
                    cContext.beginPath(); cContext.moveTo(0, i * pixelSize); cContext.lineTo(canvas.width, i * pixelSize); cContext.stroke();
                }
            });
        }

        function setTool(tool) {
            currentTool = tool;
            document.querySelectorAll('.tools-grid button').forEach(b => b.classList.remove('active'));
            document.getElementById('tool-' + tool).classList.add('active');
        }

        function getMousePos(e) {
            const targetCanvas = (window.innerWidth <= 768) ? canvasMobile : canvas;
            const rect = targetCanvas.getBoundingClientRect();
            return { x: Math.floor((e.clientX - rect.left) / pixelSize), y: Math.floor((e.clientY - rect.top) / pixelSize) };
        }

        function startDrawing(e) { isDrawing = true; handleAction(e); }
        function draw(e) { if (!isDrawing) return; handleAction(e); }
        function stopDrawing() { if (isDrawing) triggerAutoSave(); isDrawing = false; }

        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 'mousemove', { clientX: touch.clientX, clientY: touch.clientY });
            e.target.dispatchEvent(mouseEvent);
            if (e.type === 'touchstart') isDrawing = true;
            if (e.type === 'touchend') stopDrawing();
        }

        function handleAction(e) {
            const pos = getMousePos(e);
            if (pos.x < 0 || pos.x >= gridSize || pos.y < 0 || pos.y >= gridSize) return;
            const currentItem = customPalette[activeSwatchIndex];
            if (typeof currentItem === 'object' && currentItem.empty && currentTool !== 'eraser' && currentTool !== 'picker') return;

            if (currentTool === 'pencil') { gridData[pos.y][pos.x] = currentColor; redraw(); triggerAutoSave(); }
            else if (currentTool === 'eraser') { gridData[pos.y][pos.x] = null; redraw(); triggerAutoSave(); }
            else if (currentTool === 'picker') {
                if (gridData[pos.y][pos.x]) {
                    setCurrentColor(gridData[pos.y][pos.x]);
                    if (!customPalette.includes(currentColor)) { customPalette[activeSwatchIndex] = currentColor; renderPalette(); }
                }
                setTool('pencil');
            } else if (currentTool === 'bucket') {
                floodFill(pos.x, pos.y, gridData[pos.y][pos.x], currentColor);
                redraw(); isDrawing = false; triggerAutoSave();
            }
        }

        function floodFill(x, y, targetColor, fillColor) {
            if (targetColor === fillColor || x < 0 || x >= gridSize || y < 0 || y >= gridSize || gridData[y][x] !== targetColor) return;
            gridData[y][x] = fillColor;
            floodFill(x + 1, y, targetColor, fillColor); floodFill(x - 1, y, targetColor, fillColor);
            floodFill(x, y + 1, targetColor, fillColor); floodFill(x, y - 1, targetColor, fillColor);
        }

        function changeGridSize(val) {
            gridSize = parseInt(val);
            if (gridSize === 8) pixelSize = 40;
            else if (gridSize === 16) pixelSize = 22;
            else if (gridSize === 32) pixelSize = 12;
            else if (gridSize === 64) pixelSize = 6;
            initGrid(); triggerAutoSave();
        }

        function loadBackground(event) {
            const file = event.target.files[0]; if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) { bgImg = new Image(); bgImg.onload = function() { redrawBg(); }; bgImg.src = e.target.result; };
            reader.readAsDataURL(file);
        }

        function loadBackgroundMobile(event) { loadBackground(event); }

        function redrawBg() {
            [{ctx: bgCtx, canvas: bgCanvas}, {ctx: bgCtxMobile, canvas: bgCanvasMobile}].forEach(item => {
                item.ctx.clearRect(0, 0, item.canvas.width, item.canvas.height);
                if (bgImg && showBg) { item.ctx.globalAlpha = bgOpacityVal; item.ctx.drawImage(bgImg, 0, 0, item.canvas.width, item.canvas.height); item.ctx.globalAlpha = 1.0; }
            });
        }

        function toggleBackground() {
            showBg = !showBg;
            const btnText = showBg ? "Masquer BG" : "Afficher BG";
            document.getElementById('toggleBgBtn').innerText = btnText;
            const btnMob = document.getElementById('toggleBgBtnMobile'); if (btnMob) btnMob.innerText = btnText;
            bgCanvas.style.display = showBg ? 'block' : 'none';
            if (bgCanvasMobile) bgCanvasMobile.style.display = showBg ? 'block' : 'none';
            triggerAutoSave();
        }

        function removeBackground() {
            bgImg = null; bgCtx.clearRect(0, 0, bgCanvas.width, bgCanvas.height);
            if (bgCtxMobile) bgCtxMobile.clearRect(0, 0, bgCanvasMobile.width, bgCanvasMobile.height);
            document.getElementById('bgInput').value = '';
            const inputMob = document.getElementById('bgInputMobile'); if (inputMob) inputMob.value = '';
            triggerAutoSave();
        }

        function updateOpacity(val) {
            bgOpacityVal = parseFloat(val);
            const pct = Math.round(bgOpacityVal * 100);
            document.getElementById('opacityVal').innerText = pct;
            const opMob = document.getElementById('opacityValMobile'); if (opMob) opMob.innerText = pct;
            const rangeMob = document.getElementById('bgOpacityMobile'); if (rangeMob) rangeMob.value = val;
            const rangeDesk = document.getElementById('bgOpacity'); if (rangeDesk) rangeDesk.value = val;
            redrawBg(); triggerAutoSave();
        }

        function loadProjectsList() {
            fetch('save.php').then(res => res.json()).then(data => {
                projectsList = data; renderProjectsList();
                if (projectsList.length > 0) loadProject(projectsList[projectsList.length - 1].id);
                else newProject();
            });
        }

        function renderProjectsList() {
            const listEl = document.getElementById('projectList'); listEl.innerHTML = '';
            if (projectsList.length === 0) { listEl.innerHTML = '<span style="font-size:0.75rem; color:var(--text-muted);">Aucun projet enregistré</span>'; return; }
            projectsList.forEach(p => {
                const item = document.createElement('div');
                item.className = 'project-item' + (p.id === currentProjectId ? ' active' : '');
                item.innerHTML = `<span>${p.name} (${p.width}x${p.height})</span> <button onclick="event.stopPropagation(); deleteProject('${p.id}')" style="background:none; border:none; color:#ef4444; cursor:pointer; font-weight:bold;">×</button>`;
                item.onclick = () => loadProject(p.id);
                listEl.appendChild(item);
            });
        }

        function triggerAutoSave() {
            if (!isInitialized) return;
            const statusEl = document.getElementById('autosaveStatus'); statusEl.innerText = "Modifications...";
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => { saveProject(false, true); }, 1500);
        }

        function saveProject(asNew = false, isAuto = false) {
            const name = document.getElementById('projectName').value;
            const sidebar = document.getElementById('sidebarSections');
            const sectionsOrder = Array.from(sidebar.children).map(el => el.getAttribute('data-section'));
            const payload = {
                action: 'save', id: asNew ? null : currentProjectId, name: name, width: gridSize, height: gridSize,
                pixels: gridData, bgOpacity: bgOpacityVal, palette: customPalette, activeSwatchIndex: activeSwatchIndex,
                sectionsOrder: sectionsOrder,
                detailsState: {
                    projects: document.getElementById('detailsProjects').open,
                    tools: document.getElementById('detailsTools').open,
                    color: document.getElementById('detailsColor').open,
                    grid: document.getElementById('detailsGrid').open
                }
            };
            fetch('save.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
            .then(res => res.json()).then(data => {
                if (data.success) {
                    currentProjectId = data.id;
                    const statusEl = document.getElementById('autosaveStatus');
                    statusEl.innerText = isAuto ? "Enregistré" : "Sauvegardé !";
                    setTimeout(() => { if(statusEl.innerText.includes("registré")) statusEl.innerText = ""; }, 2000);
                    fetch('save.php').then(res => res.json()).then(list => { projectsList = list; renderProjectsList(); });
                }
            });
        }

        function loadProject(id) {
            isInitialized = false;
            const p = projectsList.find(item => item.id === id); if (!p) return;
            currentProjectId = p.id; document.getElementById('projectName').value = p.name;
            gridSize = p.width; document.getElementById('gridSize').value = gridSize;
            customPalette = p.palette ? p.palette : ['#000000'];
            activeSwatchIndex = (p.activeSwatchIndex !== undefined) ? p.activeSwatchIndex : 0;
            if (p.sectionsOrder && Array.isArray(p.sectionsOrder)) {
                const sidebar = document.getElementById('sidebarSections');
                p.sectionsOrder.forEach(secKey => { const el = sidebar.querySelector(`[data-section="${secKey}"]`); if (el) sidebar.appendChild(el); });
            }
            if (p.detailsState) {
                document.getElementById('detailsProjects').open = p.detailsState.projects ?? true;
                document.getElementById('detailsTools').open = p.detailsState.tools ?? true;
                document.getElementById('detailsColor').open = p.detailsState.color ?? true;
                document.getElementById('detailsGrid').open = p.detailsState.grid ?? true;
            }
            const activeColor = customPalette[activeSwatchIndex];
            currentColor = (typeof activeColor === 'string') ? activeColor : '#000000';
            changeGridSize(gridSize); initGrid(p.pixels); renderPalette(); setCurrentColor(currentColor); renderProjectsList();
            setTimeout(() => { isInitialized = true; notifyParentHeight(); }, 400);
        }

        function deleteProject(id) {
            if (!confirm("Voulez-vous vraiment supprimer ce projet ?")) return;
            fetch('save.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'delete', id: id }) })
            .then(res => res.json()).then(data => { if (data.success) { if (currentProjectId === id) newProject(); loadProjectsList(); } });
        }

        function newProject() {
            isInitialized = false; currentProjectId = null; document.getElementById('projectName').value = "Nouveau Projet";
            customPalette = ['#000000']; activeSwatchIndex = 0; setCurrentColor('#000000'); initGrid(); renderProjectsList();
            setTimeout(() => { isInitialized = true; notifyParentHeight(); }, 400);
        }

        function exportPNG() {
            const scale = 20, expCanvas = document.createElement('canvas');
            expCanvas.width = gridSize * scale; expCanvas.height = gridSize * scale;
            const expCtx = expCanvas.getContext('2d');
            for (let y = 0; y < gridSize; y++) {
                for (let x = 0; x < gridSize; x++) {
                    if (gridData[y][x]) { expCtx.fillStyle = gridData[y][x]; expCtx.fillRect(x * scale, y * scale, scale, scale); }
                }
            }
            const link = document.createElement('a');
            link.download = (document.getElementById('projectName').value || 'pixelart') + '.png';
            link.href = expCanvas.toDataURL('image/png'); link.click();
        }
    </script>
</body>
</html>