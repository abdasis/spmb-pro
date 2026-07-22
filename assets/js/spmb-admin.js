/* SPMB Pro admin scripts */
(function () {
	'use strict';

	// Sinkronkan input program (kode=nilai,kode=nilai) ke hidden field.
	function syncProgramInputs() {
		document.querySelectorAll('.spmb-program-input').forEach(function (input) {
			var jenjang = input.getAttribute('data-jenjang');
			var hidden = document.querySelector('.spmb-program-hidden[data-jenjang="' + jenjang + '"]');
			var preview = document.getElementById('prog-preview-' + jenjang);
			if (!hidden) {
				return;
			}

			function update() {
				hidden.value = input.value;
				if (preview) {
					preview.textContent = input.value;
				}
			}

			input.addEventListener('input', update);
			update();
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', syncProgramInputs);
	} else {
		syncProgramInputs();
	}
})();