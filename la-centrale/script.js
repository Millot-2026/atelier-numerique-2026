document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('modules-container');

    const sortable = new Sortable(container, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function (evt) {
            saveLayoutOrder();
        }
    });

    function saveLayoutOrder() {
        const order = [];
        const items = container.querySelectorAll('.module-item');

        items.forEach(item => {
            order.push(item.getAttribute('data-id'));
        });

        fetch('save-layout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ order: order })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('Layout saved successfully:', data.message);
                } else {
                    console.error('Error saving layout:', data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    }

    // Gestion des iframes sans ResizeObserver (anti-boucle infinie)
    document.querySelectorAll('.module-iframe').forEach(iframe => {
        const item = iframe.closest('.module-item');
        const moduleId = item ? item.getAttribute('data-id') : '';

        // Texturor : Règle absolue (ne pas toucher)
        if (moduleId === 'texturor') {
            iframe.style.height = '750px';
            return;
        }

        const updateHeight = () => {
            try {
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (doc && doc.body) {
                    const heights = [
                        doc.body.scrollHeight,
                        doc.body.offsetHeight,
                        doc.documentElement ? doc.documentElement.scrollHeight : 0,
                        doc.documentElement ? doc.documentElement.offsetHeight : 0
                    ];

                    let height = Math.max(...heights);
                    if (!Number.isFinite(height) || height < 150) height = 150;

                    const newHeight = Math.ceil(height) + 15;

                    // On applique uniquement si l'écart est significatif pour stabiliser le rendu
                    if (Math.abs(iframe.offsetHeight - newHeight) > 2) {
                        iframe.style.height = newHeight + 'px';
                    }
                }
            } catch (e) {
                // Erreur cross-origin ou chargement en cours
            }
        };

        // Événement de chargement initial
        iframe.addEventListener('load', () => {
            updateHeight();
            try {
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (doc) {
                    doc.querySelectorAll('details').forEach(det => {
                        det.addEventListener('toggle', () => {
                            setTimeout(updateHeight, 50);
                            setTimeout(updateHeight, 200);
                        });
                    });
                }
            } catch (e) { }
        });

        // Événement sur l'accordéon parent de La Centrale
        const details = iframe.closest('details');
        if (details) {
            details.addEventListener('toggle', () => {
                if (details.open) {
                    setTimeout(updateHeight, 50);
                    setTimeout(updateHeight, 200);
                }
            });
        }
    });
});

// Redimensionnement global de la fenêtre
let globalResizeTimer = null;
window.addEventListener('resize', () => {
    clearTimeout(globalResizeTimer);
    globalResizeTimer = setTimeout(() => {
        document.querySelectorAll('.module-iframe').forEach(iframe => {
            const item = iframe.closest('.module-item');
            const moduleId = item ? item.getAttribute('data-id') : '';
            if (moduleId === 'texturor') return;

            try {
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (doc && doc.body) {
                    const heights = [
                        doc.body.scrollHeight,
                        doc.body.offsetHeight,
                        doc.documentElement ? doc.documentElement.scrollHeight : 0,
                        doc.documentElement ? doc.documentElement.offsetHeight : 0
                    ];
                    let height = Math.max(...heights);
                    if (!Number.isFinite(height) || height < 150) height = 150;
                    iframe.style.height = (Math.ceil(height) + 15) + 'px';
                }
            } catch (e) { }
        });
    }, 100);
});