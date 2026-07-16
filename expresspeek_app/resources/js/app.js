import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

const openQuoteModal = () => {
	window.dispatchEvent(new CustomEvent('open-quote-modal'));
};

window.openQuoteModal = openQuoteModal;

document.addEventListener('click', (event) => {
	const trigger = event.target.closest('[data-open-quote-modal]');

	if (!trigger) {
		return;
	}

	event.preventDefault();
	openQuoteModal();
});

document.addEventListener('keydown', (event) => {
	if (event.key !== 'Enter' && event.key !== ' ') {
		return;
	}

	const trigger = event.target.closest('[data-open-quote-modal]');

	if (!trigger) {
		return;
	}

	event.preventDefault();
	openQuoteModal();
});
