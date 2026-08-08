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

    // Gestion de la persistance de l'état ouvert/fermé des modules de La Centrale
    document.querySelectorAll('#modules-container > details.module-item, #modules-container details').forEach(details => {
        const item = details.closest('.module-item');
        const moduleId = item ? item.getAttribute('data-id') : details.id;

        if (moduleId) {
            // 1. Restaurer l'état mémorisé au chargement
            const savedState = localStorage.getItem('centrale_module_open_' + moduleId);
            if (savedState !== null) {
                details.open = (savedState === 'true');
            }

            // 2. Sauvegarder l'état lors du toggle (ouverture/fermeture)
            details.addEventListener('toggle', () => {
                localStorage.setItem('centrale_module_open_' + moduleId, details.open);
            });
        }
    });

    // Gestion universelle des hauteurs pour toutes les iframes
    document.querySelectorAll('.module-iframe').forEach(iframe => {
        const item = iframe.closest('.module-item');
        const moduleId = item ? item.getAttribute('data-id') : '';

        // Texturor : Règle absolue (ne pas toucher)
        if (moduleId === 'texturor') {
            iframe.style.height = '750px';
            return;
        }

        const updateHeight = () => {
            // Pixel Art gère sa propre hauteur via postMessage, on ignore son calcul ici
            if (moduleId === 'pixelart') return;

            try {
                iframe.style.height = '0px';
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (doc && doc.body) {
                    let maxBottom = 0;
                    doc.body.querySelectorAll('*').forEach(el => {
                        const style = el.ownerDocument.defaultView.getComputedStyle(el);
                        if (style.position !== 'absolute' && style.position !== 'fixed' && style.display !== 'none' && el.offsetHeight > 0) {
                            if (!el.closest('details:not([open])')) {
                                const bottom = el.offsetTop + el.offsetHeight;
                                if (bottom > maxBottom) maxBottom = bottom;
                            }
                        }
                    });

                    const height = Math.max(
                        maxBottom,
                        doc.body.scrollHeight,
                        doc.documentElement ? doc.documentElement.scrollHeight : 0
                    );

                    iframe.style.height = (height > 0 ? height + 15 : 150) + 'px';
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
                            setTimeout(updateHeight, 30);
                            setTimeout(updateHeight, 150);
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
                    setTimeout(updateHeight, 30);
                    setTimeout(updateHeight, 150);
                }
            });
        }
    });

    // Écouteur pour les messages dynamiques (utilisé par Pixel Art)
    window.addEventListener('message', function (event) {
        if (event.data && event.data.type === 'resizeIframe') {
            document.querySelectorAll('.module-iframe').forEach(iframe => {
                if (iframe.contentWindow === event.source) {
                    const item = iframe.closest('.module-item');
                    const moduleId = item ? item.getAttribute('data-id') : '';
                    if (moduleId === 'texturor') return;

                    const newHeight = Math.ceil(event.data.height);
                    if (Math.abs(iframe.offsetHeight - newHeight) > 2) {
                        iframe.style.height = newHeight + 'px';
                    }
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
            if (moduleId === 'texturor' || moduleId === 'pixelart') return;

            try {
                iframe.style.height = '0px';
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (doc && doc.body) {
                    let maxBottom = 0;
                    doc.body.querySelectorAll('*').forEach(el => {
                        const style = el.ownerDocument.defaultView.getComputedStyle(el);
                        if (style.position !== 'absolute' && style.position !== 'fixed' && style.display !== 'none' && el.offsetHeight > 0) {
                            if (!el.closest('details:not([open])')) {
                                const bottom = el.offsetTop + el.offsetHeight;
                                if (bottom > maxBottom) maxBottom = bottom;
                            }
                        }
                    });

                    const height = Math.max(
                        maxBottom,
                        doc.body.scrollHeight,
                        doc.documentElement ? doc.documentElement.scrollHeight : 0
                    );
                    iframe.style.height = (height > 0 ? height + 15 : 150) + 'px';
                }
            } catch (e) { }
        });
    }, 100);
});