(function (blocks, element, blockEditor, components) {
	const el = element.createElement;
	const { useState, Fragment } = element;
	const { useBlockProps, InspectorControls } = blockEditor;
	const {
		PanelBody,
		SelectControl,
		CheckboxControl,
		Button,
		TextControl,
		TextareaControl,
	} = components;

	const SCOPE_OPTIONS = [
		{ key: "kark", label: "Kark" },
		{ key: "barki", label: "Barki" },
		{ key: "plecy", label: "Plecy" },
		{ key: "nogi", label: "Nogi" },
	];

	const LABELS = {
		silny: "Siła: Silny",
		umiarkowany: "Siła: Umiarkowany",
		tak: "Olejki: Tak",
		nie: "Olejki: Nie",
		bielizna: "Ubranie: Bielizna",
		nago: "Ubranie: Nago",
		szybkie: "Tempo: Szybkie",
		umiarkowane: "Tempo: Umiarkowane",
		wolne: "Tempo: Wolne",
		kark: "Kark",
		barki: "Barki",
		plecy: "Plecy",
		nogi: "Nogi",
	};

	blocks.registerBlockType("studio/massage-pricelist", {
		edit: function (props) {
			const { attributes, setAttributes } = props;
			const items = attributes.items || [];
			const [activeIndex, setActiveIndex] = useState(0);
			const blockProps = useBlockProps({ className: "mp-grid mp-editor-grid" });

			const updateItem = (index, field, value) => {
				const next = items.map((item, idx) =>
					idx === index ? Object.assign({}, item, { [field]: value }) : item,
				);
				setAttributes({ items: next });
			};

			const addItem = () => {
				const newItem = {
					title: "Nowy masaż",
					description: "",
					variants: [{ duration: "60 min", price: "180 zł" }],
					strength: "umiarkowany",
					oils: "tak",
					clothing: "bielizna",
					pace: "umiarkowane",
					scope: ["plecy"],
				};
				setAttributes({ items: [...items, newItem] });
				setActiveIndex(items.length);
			};

			const removeItem = (index) => {
				const next = items.filter((_, idx) => idx !== index);
				setAttributes({ items: next });
				if (activeIndex >= next.length) {
					setActiveIndex(Math.max(0, next.length - 1));
				}
			};

			const updateVariant = (itemIdx, varIdx, key, val) => {
				const variants = (items[itemIdx].variants || []).map((v, i) =>
					i === varIdx ? Object.assign({}, v, { [key]: val }) : v,
				);
				updateItem(itemIdx, "variants", variants);
			};

			const addVariant = (itemIdx) => {
				const variants = [
					...(items[itemIdx].variants || []),
					{ duration: "", price: "" },
				];
				updateItem(itemIdx, "variants", variants);
			};

			const removeVariant = (itemIdx, varIdx) => {
				const variants = (items[itemIdx].variants || []).filter(
					(_, i) => i !== varIdx,
				);
				updateItem(itemIdx, "variants", variants);
			};

			const toggleScope = (itemIdx, partKey, checked) => {
				const current = items[itemIdx].scope || [];
				const next = checked
					? [...current, partKey]
					: current.filter((k) => k !== partKey);
				updateItem(itemIdx, "scope", next);
			};

			const activeItem = items[activeIndex] || null;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					activeItem &&
						el(
							PanelBody,
							{
								title:
									"Właściwości: " +
									(activeItem.title || "Masaż #" + (activeIndex + 1)),
								initialOpen: true,
							},
							el(SelectControl, {
								label: "Siła masażu",
								value: activeItem.strength,
								options: [
									{ label: "Umiarkowany", value: "umiarkowany" },
									{ label: "Silny", value: "silny" },
								],
								onChange: (val) => updateItem(activeIndex, "strength", val),
							}),
							el(SelectControl, {
								label: "Olejki",
								value: activeItem.oils,
								options: [
									{ label: "Tak (z olejkami)", value: "tak" },
									{ label: "Nie (bez olejków)", value: "nie" },
								],
								onChange: (val) => updateItem(activeIndex, "oils", val),
							}),
							el(SelectControl, {
								label: "Ubranie",
								value: activeItem.clothing,
								options: [
									{ label: "Bielizna", value: "bielizna" },
									{ label: "Nago", value: "nago" },
								],
								onChange: (val) => updateItem(activeIndex, "clothing", val),
							}),
							el(SelectControl, {
								label: "Tempo",
								value: activeItem.pace,
								options: [
									{ label: "Wolne", value: "wolne" },
									{ label: "Umiarkowane", value: "umiarkowane" },
									{ label: "Szybkie", value: "szybkie" },
								],
								onChange: (val) => updateItem(activeIndex, "pace", val),
							}),
							el(
								"p",
								{ style: { fontWeight: 600, marginBottom: "8px" } },
								"Zakres masażu:",
							),
							SCOPE_OPTIONS.map((opt) =>
								el(CheckboxControl, {
									key: opt.key,
									label: opt.label,
									checked: (activeItem.scope || []).includes(opt.key),
									onChange: (checked) =>
										toggleScope(activeIndex, opt.key, checked),
								}),
							),
						),
				),
				el(
					"div",
					blockProps,
					items.map((item, index) => {
						const isSelected = index === activeIndex;
						return el(
							"div",
							{
								key: index,
								className:
									"mp-card mp-editor-card" + (isSelected ? " is-selected" : ""),
								onClick: () => setActiveIndex(index),
							},
							el(
								"div",
								{ className: "mp-editor-card__header" },
								el(
									"span",
									{ className: "mp-editor-badge" },
									"Pozycja #" + (index + 1),
								),
								el(
									Button,
									{
										isDestructive: true,
										variant: "link",
										onClick: (e) => {
											e.stopPropagation();
											removeItem(index);
										},
									},
									"Usuń masaż",
								),
							),
							el(TextControl, {
								label: "Nazwa masażu",
								value: item.title,
								onChange: (val) => updateItem(index, "title", val),
							}),
							el(TextareaControl, {
								label: "Opis (opcjonalny)",
								rows: 2,
								value: item.description,
								onChange: (val) => updateItem(index, "description", val),
							}),
							el(
								"div",
								{ className: "mp-editor-variants" },
								el("strong", null, "Warianty (Czas i Cena):"),
								(item.variants || []).map((v, vIdx) =>
									el(
										"div",
										{ key: vIdx, className: "mp-editor-variant-row" },
										el(TextControl, {
											placeholder: "np. 60 min",
											value: v.duration,
											onChange: (val) =>
												updateVariant(index, vIdx, "duration", val),
										}),
										el(TextControl, {
											placeholder: "np. 180 zł",
											value: v.price,
											onChange: (val) =>
												updateVariant(index, vIdx, "price", val),
										}),
										el(Button, {
											isDestructive: true,
											icon: "no-alt",
											label: "Usuń wariant",
											onClick: () => removeVariant(index, vIdx),
										}),
									),
								),
								el(
									Button,
									{
										variant: "secondary",
										isSmall: true,
										onClick: () => addVariant(index),
									},
									"+ Dodaj wariant czasu/ceny",
								),
							),
							el(
								"div",
								{ className: "mp-editor-tags" },
								el("span", { className: "mp-tag" }, LABELS[item.strength]),
								el("span", { className: "mp-tag" }, LABELS[item.oils]),
								el("span", { className: "mp-tag" }, LABELS[item.clothing]),
								el("span", { className: "mp-tag" }, LABELS[item.pace]),
								(item.scope || []).map((s) =>
									el(
										"span",
										{ key: s, className: "mp-tag mp-tag--scope" },
										LABELS[s],
									),
								),
							),
						);
					}),
					el(
						"div",
						{ className: "mp-editor-add-wrap" },
						el(
							Button,
							{
								variant: "primary",
								onClick: addItem,
							},
							"+ Dodaj nowy masaż do cennika",
						),
					),
				),
			);
		},
		save: function () {
			return null;
		},
	});
})(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
);
