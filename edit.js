(function (blocks, element, blockEditor, components) {
	const el = element.createElement;
	const { useState, Fragment } = element;
	const { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } =
		blockEditor;
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

	const ICON_KEYS = [
		{ key: "silny", label: "Siła: Silny" },
		{ key: "umiarkowany", label: "Siła: Umiarkowany" },
		{ key: "tak", label: "Olejki: Tak" },
		{ key: "nie", label: "Olejki: Nie" },
		{ key: "bielizna", label: "Ubranie: Bielizna" },
		{ key: "nago", label: "Ubranie: Nago" },
		{ key: "szybkie", label: "Tempo: Szybkie" },
		{ key: "umiarkowane", label: "Tempo: Umiarkowane" },
		{ key: "wolne", label: "Tempo: Wolne" },
		{ key: "kark", label: "Zakres: Kark" },
		{ key: "barki", label: "Zakres: Barki" },
		{ key: "plecy", label: "Zakres: Plecy" },
		{ key: "nogi", label: "Zakres: Nogi" },
	];

	const LABELS = ICON_KEYS.reduce((acc, item) => {
		acc[item.key] = item.label;
		return acc;
	}, {});

	blocks.registerBlockType("studio/massage-pricelist", {
		edit: function (props) {
			const { attributes, setAttributes } = props;
			const items = attributes.items || [];
			const customIcons = attributes.customIcons || {};
			const [activeIndex, setActiveIndex] = useState(0);
			const blockProps = useBlockProps({ className: "mp-grid mp-editor-grid" });

			const setCustomIcon = (featureKey, media) => {
				const nextIcons = Object.assign({}, customIcons);
				if (media && media.url) {
					nextIcons[featureKey] = { id: media.id, url: media.url };
				} else {
					delete nextIcons[featureKey];
				}
				setAttributes({ customIcons: nextIcons });
			};

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

			const moveItem = (index, direction) => {
				const targetIndex = index + direction;
				if (targetIndex < 0 || targetIndex >= items.length) return;
				const next = [...items];
				const temp = next[index];
				next[index] = next[targetIndex];
				next[targetIndex] = temp;
				setAttributes({ items: next });
				setActiveIndex(targetIndex);
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

			const renderTagWithIcon = (key) => {
				const iconUrl = customIcons[key] && customIcons[key].url;
				return el(
					"span",
					{ key: key, className: "mp-tag" },
					iconUrl &&
						el("img", { src: iconUrl, className: "mp-tag__icon", alt: "" }),
					LABELS[key],
				);
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
					el(
						PanelBody,
						{ title: "Własne ikony (SVG / Media)", initialOpen: false },
						ICON_KEYS.map((item) => {
							const currentIcon = customIcons[item.key];
							return el(
								"div",
								{ key: item.key, className: "mp-icon-picker-row" },
								el(
									"div",
									{ className: "mp-icon-picker-row__info" },
									currentIcon && currentIcon.url
										? el("img", {
												src: currentIcon.url,
												className: "mp-icon-picker-row__preview",
												alt: "",
											})
										: el(
												"span",
												{ className: "mp-icon-picker-row__placeholder" },
												"SVG",
											),
									el(
										"span",
										{ className: "mp-icon-picker-row__label" },
										item.label,
									),
								),
								el(
									"div",
									{ className: "mp-icon-picker-row__actions" },
									el(
										MediaUploadCheck,
										null,
										el(MediaUpload, {
											onSelect: (media) => setCustomIcon(item.key, media),
											allowedTypes: ["image/svg+xml", "image"],
											value: currentIcon ? currentIcon.id : "",
											render: ({ open }) =>
												el(
													Button,
													{
														variant: "secondary",
														isSmall: true,
														onClick: open,
													},
													currentIcon ? "Zmień" : "Wybierz",
												),
										}),
									),
									currentIcon &&
										el(
											Button,
											{
												isDestructive: true,
												isSmall: true,
												variant: "link",
												onClick: () => setCustomIcon(item.key, null),
											},
											"Reset",
										),
								),
							);
						}),
					),
				),
				el(
					"div",
					blockProps,
					items.map((item, index) => {
						const isSelected = index === activeIndex;
						const variants = item.variants || [];
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
									"div",
									{ className: "mp-editor-card__actions" },
									el(Button, {
										icon: "arrow-left-alt2",
										label: "Przesuń wcześniej",
										isSmall: true,
										disabled: index === 0,
										onClick: (e) => {
											e.stopPropagation();
											moveItem(index, -1);
										},
									}),
									el(Button, {
										icon: "arrow-right-alt2",
										label: "Przesuń dalej",
										isSmall: true,
										disabled: index === items.length - 1,
										onClick: (e) => {
											e.stopPropagation();
											moveItem(index, 1);
										},
									}),
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
										"Usuń",
									),
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
								variants.map((v, vIdx) =>
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
											isSmall: true,
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
										style: { marginTop: "8px" },
										onClick: () => addVariant(index),
									},
									"+ Dodaj wariant",
								),
							),
							el(
								"div",
								{ className: "mp-editor-tags" },
								renderTagWithIcon(item.strength),
								renderTagWithIcon(item.oils),
								renderTagWithIcon(item.clothing),
								renderTagWithIcon(item.pace),
								(item.scope || []).map((s) => renderTagWithIcon(s)),
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
