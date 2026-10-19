document.addEventListener("DOMContentLoaded", function () {
	const wrappers = document.querySelectorAll(".mp-pricelist-wrap");

	wrappers.forEach((wrapper) => {
		const ajaxUrl = wrapper.dataset.ajax;
		const nonce = wrapper.dataset.nonce;
		const config = wrapper.dataset.config;
		const grid = wrapper.querySelector(".mp-grid");
		const buttons = wrapper.querySelectorAll(".mp-filter-btn");

		buttons.forEach((btn) => {
			btn.addEventListener("click", function () {
				if (btn.classList.contains("is-active")) return;

				buttons.forEach((b) => {
					b.classList.remove("is-active");
					b.setAttribute("aria-pressed", "false");
				});
				btn.classList.add("is-active");
				btn.setAttribute("aria-pressed", "true");

				grid.classList.add("is-loading");

				const formData = new FormData();
				formData.append("action", "mp_filter_items");
				formData.append("nonce", nonce);
				formData.append("category", btn.dataset.category);
				formData.append("config", config);

				fetch(ajaxUrl, {
					method: "POST",
					body: formData,
				})
					.then((res) => res.json())
					.then((res) => {
						if (res.success && res.data.html !== undefined) {
							grid.innerHTML = res.data.html;
						}
					})
					.finally(() => {
						grid.classList.remove("is-loading");
					});
			});
		});
	});
});
