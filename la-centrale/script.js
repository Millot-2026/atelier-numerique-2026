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

});