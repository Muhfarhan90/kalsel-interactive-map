import Sortable from 'sortablejs';

function initCategorySort() {
    const list = document.querySelector('[data-category-sort-list]');
    const form = document.querySelector('[data-category-sort-form]');

    if (!list || !form) return;

    const saveButton = form.querySelector('button[type="submit"]');
    const orderInputs = form.querySelector('[data-order-inputs]');
    const sortable = new Sortable(list, {
        handle: '[data-category-drag-handle]',
        draggable: 'tr[data-category-id]',
        dataIdAttr: 'data-category-id',
        animation: 150,
        ghostClass: 'opacity-40',
        onEnd: updateOrder,
    });
    const originalOrder = sortable.toArray().join(',');

    function updateOrder() {
        const unchanged = sortable.toArray().join(',') === originalOrder;
        form.hidden = unchanged;
        saveButton.disabled = unchanged;
    }

    list.addEventListener('keydown', (event) => {
        const handle = event.target.closest('[data-category-drag-handle]');
        if (!handle || (event.key !== 'ArrowUp' && event.key !== 'ArrowDown')) return;

        event.preventDefault();
        const row = handle.closest('tr[data-category-id]');
        const neighbor = event.key === 'ArrowUp' ? row.previousElementSibling : row.nextElementSibling;
        if (!neighbor?.matches('tr[data-category-id]')) return;

        list.insertBefore(row, event.key === 'ArrowUp' ? neighbor : neighbor.nextElementSibling);
        handle.focus();
        updateOrder();
    });

    form.addEventListener('submit', (event) => {
        if (saveButton.disabled) {
            event.preventDefault();
            return;
        }

        orderInputs.replaceChildren(...sortable.toArray().map((id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'order[]';
            input.value = id;
            return input;
        }));
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCategorySort);
} else {
    initCategorySort();
}
