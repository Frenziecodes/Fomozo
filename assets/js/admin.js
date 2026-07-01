(function () {
	'use strict';

	var modal = document.getElementById('noravo-rule-modal');

	if (!modal) {
		return;
	}

	var openButtons = document.querySelectorAll('[data-noravo-open-rule-modal]');
	var closeButtons = modal.querySelectorAll('[data-noravo-close-rule-modal]');
	var backButton = modal.querySelector('[data-noravo-rule-back]');
	var title = modal.querySelector('[data-noravo-modal-title]');
	var steps = modal.querySelectorAll('[data-noravo-rule-step]');
	var categoryButtons = modal.querySelectorAll('[data-noravo-trigger-group]');
	var panels = modal.querySelectorAll('[data-noravo-trigger-panel]');
	var actionCategoryButtons = modal.querySelectorAll('[data-noravo-action-group]');
	var actionPanels = modal.querySelectorAll('[data-noravo-action-panel]');
	var triggerButtons = modal.querySelectorAll('[data-noravo-select-trigger]');
	var actionButtons = modal.querySelectorAll('[data-noravo-select-action]');
	var selectedTrigger = '';

	function openModal() {
		showStep('trigger');
		modal.classList.add('is-open');
		modal.setAttribute('aria-hidden', 'false');
		document.body.classList.add('noravo-rule-modal-open');
	}

	function closeModal() {
		modal.classList.remove('is-open');
		modal.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('noravo-rule-modal-open');
	}

	function showStep(stepName) {
		steps.forEach(function (step) {
			step.classList.toggle('is-active', step.getAttribute('data-noravo-rule-step') === stepName);
		});

		if (backButton) {
			backButton.hidden = stepName === 'trigger';
		}

		if (title) {
			title.textContent = stepName === 'action'
				? 'Select an action for your automation rule'
				: 'Select a trigger for your automation rule';
		}
	}

	function activateGroup(group) {
		categoryButtons.forEach(function (button) {
			button.classList.toggle('is-active', button.getAttribute('data-noravo-trigger-group') === group);
		});

		panels.forEach(function (panel) {
			panel.classList.toggle('is-active', panel.getAttribute('data-noravo-trigger-panel') === group);
		});
	}

	function activateActionGroup(group) {
		actionCategoryButtons.forEach(function (button) {
			button.classList.toggle('is-active', button.getAttribute('data-noravo-action-group') === group);
		});

		actionPanels.forEach(function (panel) {
			panel.classList.toggle('is-active', panel.getAttribute('data-noravo-action-panel') === group);
		});
	}

	openButtons.forEach(function (button) {
		button.addEventListener('click', openModal);
	});

	closeButtons.forEach(function (button) {
		button.addEventListener('click', closeModal);
	});

	categoryButtons.forEach(function (button) {
		button.addEventListener('click', function () {
			activateGroup(button.getAttribute('data-noravo-trigger-group'));
		});
	});

	actionCategoryButtons.forEach(function (button) {
		button.addEventListener('click', function () {
			activateActionGroup(button.getAttribute('data-noravo-action-group'));
		});
	});

	triggerButtons.forEach(function (button) {
		button.addEventListener('click', function () {
			selectedTrigger = button.getAttribute('data-noravo-select-trigger') || '';
			showStep('action');
			activateActionGroup('campaigns');
		});
	});

	actionButtons.forEach(function (button) {
		button.addEventListener('click', function () {
			var selectedAction = button.getAttribute('data-noravo-select-action') || '';
			var url = new URL(window.location.href);

			closeModal();
			url.searchParams.set('page', 'noravo-campaigns');
			url.searchParams.set('view', 'builder');
			url.searchParams.set('trigger', selectedTrigger);
			url.searchParams.set('rule_action', selectedAction);
			window.location.href = url.toString();
		});
	});

	if (backButton) {
		backButton.addEventListener('click', function () {
			showStep('trigger');
		});
	}

	modal.addEventListener('click', function (event) {
		if (event.target === modal) {
			closeModal();
		}
	});

	document.addEventListener('keydown', function (event) {
		if ('Escape' === event.key && modal.classList.contains('is-open')) {
			closeModal();
		}
	});
}());
