/* SPMB Pro public scripts */
(function () {
	'use strict';

	function initForms() {
		document.querySelectorAll('.spmb-form').forEach(initForm);
	}

	function initForm(form) {
		var steps = Array.prototype.slice.call(form.querySelectorAll('.spmb-step'));
		var jalurSteps = Array.prototype.slice.call(form.querySelectorAll('.spmb-step-jalur'));
		var jalurSelect = form.querySelector('[name="jalur"]');
		var prevBtn = form.querySelector('.spmb-prev');
		var nextBtn = form.querySelector('.spmb-next');
		var visibleSteps = [];

		function recomputeVisible() {
			visibleSteps = steps.filter(function (s) {
				if (s.classList.contains('spmb-step-jalur')) {
					return jalurSelect && s.getAttribute('data-jalur') === jalurSelect.value;
				}
				return true;
			});
			visibleSteps.forEach(function (s, i) {
				s.style.display = i === current() ? '' : 'none';
			});
			form.setAttribute('data-final-step', current() === visibleSteps.length - 1 ? '1' : '0');
			prevBtn.disabled = current() === 0;
		}

		var idx = 0;
		function current() { return idx; }

		function go(n) {
			if (n < 0) { n = 0; }
			if (n >= visibleSteps.length) { n = visibleSteps.length - 1; }
			idx = n;
			recomputeVisible();
		}

		nextBtn.addEventListener('click', function () { go(idx + 1); });
		prevBtn.addEventListener('click', function () { go(idx - 1); });
		if (jalurSelect) {
			jalurSelect.addEventListener('change', function () {
				if (idx > 3) { idx = 3; }
				recomputeVisible();
			});
		}

		recomputeVisible();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initForms);
	} else {
		initForms();
	}
})();