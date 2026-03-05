<div id="confirmModal"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50">

    <div class="bg-white w-[420px] max-w-[90%] rounded-2xl p-6 shadow-2xl">

        <div class="flex items-start gap-3 mb-4">
            <div id="confirmIcon"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 text-red-600">
                <!-- Icono dinámico -->
            </div>

            <div>
                <h3 id="confirmTitle" class="text-lg font-bold text-gray-900">
                    Confirmar acción
                </h3>
                <p id="confirmMessage" class="text-sm text-gray-500 mt-1">
                    ¿Estás seguro?
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeConfirmModal()"
                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800">
                Cancelar
            </button>

            <button id="confirmActionBtn"
                class="text-black px-6 py-2 rounded-xl text-sm font-semibold transition">
                Confirmar
            </button>
        </div>
    </div>
</div>
<script>
    let confirmForm = null;

    window.openConfirmModal = function (options) {

        const modal = document.getElementById('confirmModal');
        const title = document.getElementById('confirmTitle');
        const message = document.getElementById('confirmMessage');
        const actionBtn = document.getElementById('confirmActionBtn');
        const icon = document.getElementById('confirmIcon');

        if (!modal) {
            console.error("Modal no encontrado");
            return;
        }

        confirmForm = options.form;

        title.textContent = options.title;
        message.textContent = options.message;

        actionBtn.textContent = options.buttonText;
        actionBtn.className = "px-6 py-2 rounded-xl text-sm font-semibold transition " + options.buttonClass;

        icon.innerHTML = options.icon;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    window.closeConfirmModal = function () {
        const modal = document.getElementById('confirmModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const confirmBtn = document.getElementById('confirmActionBtn');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                if (confirmForm) {
                    confirmForm.submit();
                }
            });
        }
    });
</script>
