let dragSrcEl = null;

function handleDragStart(e) {
    dragSrcEl = this;
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragOver(e) {
    e.preventDefault();
}

function handleDrop(e) {
    e.stopPropagation();
    if (dragSrcEl !== this) {
        const list = this.parentNode;
        const allNodes = Array.from(list.children);
        if (allNodes.indexOf(dragSrcEl) < allNodes.indexOf(this)) {
            list.insertBefore(dragSrcEl, this.nextSibling);
        } else {
            list.insertBefore(dragSrcEl, this);
        }
        updatePreview();
    }
}

function updatePreview() {
    const previewList = document.getElementById('preview-list');
    if (!previewList) return;
    previewList.innerHTML = '';

    document.querySelectorAll('.row').forEach((row) => {
        const start = row.querySelector('[name="time[]"]').value || '...';
        const end = row.querySelector('[name="time_end[]"]').value || '...';
        const context = row.querySelector('[name="context[]"]').value || '...';
        const action = row.querySelector('[name="title[]"]').value || '...';

        const card = document.createElement('div');
        card.style.background = '#2a2a2a';
        card.style.padding = '10px';
        card.style.borderRadius = '4px';
        card.style.borderLeft = '4px solid #e67e22';
        card.innerHTML = `<strong>${start} - ${end}</strong> | <em>${context}</em> : ${action}`;
        previewList.appendChild(card);
    });
}

function addRow() {
    const container = document.getElementById('inputs-container');
    if (!container) return;

    const newRow = document.createElement('div');
    newRow.className = 'row';
    newRow.draggable = true;
    newRow.innerHTML = `
        <div class="drag-handle">☰</div>
        <div class="input-group">
            <input type="text" name="time[]" placeholder="Début" class="level-select">
            <input type="text" name="time_end[]" placeholder="Fin" class="level-select">
            <input type="text" name="context[]" placeholder="Contexte" class="parent-selector">
            <input type="text" name="title[]" placeholder="Action" class="title-input">
        </div>
        <button type="button" class="btn-remove" onclick="this.closest('.row').remove(); updatePreview();">X</button>`;

    newRow.addEventListener('dragstart', handleDragStart);
    newRow.addEventListener('dragover', handleDragOver);
    newRow.addEventListener('drop', handleDrop);

    newRow.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    container.appendChild(newRow);
    updatePreview();
}

window.onload = () => {
    const data = window.initialData;
    if (data && data.time && Array.isArray(data.time) && data.time.length > 0) {
        const container = document.getElementById('inputs-container');
        if (container) container.innerHTML = '';

        data.time.forEach((val, i) => {
            addRow();
            const rows = document.querySelectorAll('.row');
            const r = rows[rows.length - 1];
            if (r) {
                r.querySelector('[name="time[]"]').value = val;
                r.querySelector('[name="time_end[]"]').value = (data.time_end && data.time_end[i]) ? data.time_end[i] : '';
                r.querySelector('[name="context[]"]').value = data.context && data.context[i] ? data.context[i] : '';
                r.querySelector('[name="title[]"]').value = data.title && data.title[i] ? data.title[i] : '';
            }
        });
        updatePreview();
    } else {
        addRow();
    }
};

document.addEventListener("DOMContentLoaded", function () {
    const statusBar = document.querySelector('.status-bar');
    if (statusBar) {
        setTimeout(() => {
            statusBar.style.display = 'none';
        }, 2000);
    }
});