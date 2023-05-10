(function ($) {
	"use strict";
	function log(msg, type = "log") {
		console[type](`----------------------------------`);
		console[type](`[FPM Media Cleaner]`);
		console[type](`${msg}`);
		console[type](`----------------------------------`);
	}
	/**
	 *
	 * @param {'media-clean-start' | '...'} action
	 * @param {*} data
	 * @returns
	 */
	function request(action, data) {
		return $.post(ajaxurl, { action, ...data }).pipe(function (response) {
			try {
				return JSON.parse(response);
			} catch (e) {
				log(e.message, "error");
				return response;
			}
		});
	}

	function initPanel() {
		const panel = document.querySelector("[data-fpm-media-cleaner]");
		if (!panel) {
			return;
		}
		request("media-clean-get-cache", {})
			.done(function (response) {
				console.log("safty", response);
			})
			.fail(function () {
				console.error("error");
			});
		document
			.querySelector("[data-fpm-media-cleaner-refresh]")
			.addEventListener("click", function () {
				request("media-clean-fill-cache", {})
					.done(function (response) {
						console.log("safty", response);
					})
					.fail(function () {
						console.error("error");
					});
			});
	}

	// 'ajaxurl' is allways defined and point to admin
	$(function () {
		initPanel();
	});
	console.log(ajaxurl);
})(jQuery);
