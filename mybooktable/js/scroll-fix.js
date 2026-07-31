// ==================== START Stormhill ====================
// STORMHILL FIX - v3.6.4
// Native smooth scroll for .mbt-book-purchase-button using capture phase
// to bypass jQuery animate conflicts with themes like Divi.
// DO NOT REVERT on plugin update.
// ==================== END Stormhill ====================
document.addEventListener('DOMContentLoaded', function() {
	var btn = document.querySelector('a.mbt-book-purchase-button');
	if (btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			e.stopImmediatePropagation();
			var adminBar = document.getElementById('wpadminbar');
			var adminH = adminBar ? adminBar.offsetHeight : 0;
			var target = document.querySelector('.mbt-book-purchase-section');
			if (target) {
				var scrollTo = target.getBoundingClientRect().top + window.pageYOffset - adminH - 100;
				window.scrollTo({ top: scrollTo, behavior: 'smooth' });
			}
		}, true); // true = capture phase, fires before all bubble-phase handlers
	}
});
