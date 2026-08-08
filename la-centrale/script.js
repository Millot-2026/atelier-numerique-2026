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

    // Gestion des iframes avec règles spécifiques par module
    document.querySelectorAll('.module-iframe').forEach(iframe => {
        const item = iframe.closest('.module-item');
        const moduleId = item ? item.getAttribute('data-id') : '';

        // Modules complexes avec hauteur fixe de confort (évite la boucle infinie)
        if (moduleId === 'pixelart' || moduleId === 'texturor') {
            iframe.style.height = '750px';
            return; // On sort pour ne pas appliquer le ResizeObserver en boucle
        }

        // Calcul dynamique pour les autres (Personnator, Skeletor, User Journey) sans marge superflue
        const updateHeight = () => {
            try {
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (doc && doc.body) {
                    const height = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight);
                    iframe.style.height = height + 'px';
                }
            } catch (e) {
                console.log("Erreur de redimensionnement iframe", e);
            }
        };

        iframe.addEventListener('load', () => {
            updateHeight();
            try {
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (doc && doc.defaultView) {
                    const observer = new ResizeObserver(updateHeight);
                    observer.observe(doc.body);
                }
            } catch (e) { }
        });

        const details = iframe.closest('details');
        if (details) {
            details.addEventListener('toggle', () => {
                if (details.open) {
                    setTimeout(updateHeight, 50);
                }
            });
        }
    });
});

window.addEventListener('resize', () => {
    document.querySelectorAll('.module-iframe').forEach(iframe => {
        const item = iframe.closest('.module-item');
        const moduleId = item ? item.getAttribute('data-id') : '';
        if (moduleId === 'pixelart' || moduleId === 'texturor') return;

        try {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            if (doc && doc.body) {
                const height = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight);
                iframe.style.height = height + 'px';
            }
        } catch (e) { }
    });
});