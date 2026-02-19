@props(['action', 'itemName' => 'items'])

<div id="bulk-delete-container"
    class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg hidden dark:bg-red-900 dark:border-red-700">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span id="selected-count" class="font-semibold text-red-800 dark:text-red-200">0 {{ $itemName }}
                selecionado(s)</span>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="cancelBulkDelete()"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                Cancelar
            </button>
            <button type="button" onclick="submitBulkDelete('{{ $action }}')"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700">
                Deletar Selecionados
            </button>
        </div>
    </div>
</div>

<script>
    function updateBulkDeleteUI() {
        const checkboxes = document.querySelectorAll('input[name="bulk_delete"]:checked');
        const container = document.getElementById('bulk-delete-container');
        const countSpan = document.getElementById('selected-count');

        if (checkboxes.length > 0) {
            container.classList.remove('hidden');
            countSpan.textContent = checkboxes.length + ' {{ $itemName }} selecionado(s)';
        } else {
            container.classList.add('hidden');
        }
    }

    function cancelBulkDelete() {
        document.querySelectorAll('input[name="bulk_delete"]').forEach(cb => cb.checked = false);
        updateBulkDeleteUI();
    }

    function submitBulkDelete(action) {
        const checkboxes = document.querySelectorAll('input[name="bulk_delete"]:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);

        if (ids.length === 0) {
            alert('Selecione pelo menos um item para deletar');
            return;
        }

        if (!confirm('Tem certeza que deseja deletar ' + ids.length + ' item(ns)?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action;
        form.innerHTML = `
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" value="${ids.join(',')}">
    `;
        document.body.appendChild(form);
        form.submit();
    }

    // Atualizar UI quando checkboxes são clicadas
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="bulk_delete"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkDeleteUI);
        });
    });
</script>
