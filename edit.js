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
		{ key: "głowa", label: "głowa" },
		{ key: "kark", label: "kark" },
		{ key: "plecy", label: "plecy" },
		{ key: "nogi", label: "nogi" },
		{ key: "ramiona", label: "ramiona" },
		{ key: "stopy", label: "stopy" },
		{ key: "dłonie", label: "dłonie" },
	];

	const LABELS = {
		"intensywny": "intensywny",
		"relaksacyjny": "relaksacyjny",
		"tak": "z olejkami",
		"nie": "bez olejków",
		"w bieliźnie": "w bieliźnie",
		"bez ubrania": "bez ubrania",
		"wolne": "wolne tempo",
		"umiarkowane": "tempo umiarkowane",
		"szybkie": "szybkie tempo",
		"głowa": "głowa",
		"kark": "kark",
		"plecy": "plecy",
		"nogi": "nogi",
		"ramiona": "ramiona",
		"stopy": "stopy",
		"dłonie": "dłonie",
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
						strength: "relaksacyjny",
						oils: "tak",
						clothing: "w bieliźnie",
						pace: "umiarkowane",
						scope: ["plecy", "kark", "ramiona"],
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
				const renderTag = (key) => {
					if (!LABELS[key]) return null;
					return el("span", { key: key, className: "mp-feature-pill" }, LABELS[key]);
				};

				const renderScopeTag = (scopeArray) => {
					if (!scopeArray || !scopeArray.length) return null;
					const labels = scopeArray.map(k => LABELS[k]).filter(Boolean).join(', ');
					if (!labels) return null;
					return el("span", { className: "mp-feature-pill" }, "zakres: " + labels);
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
										{ label: "intensywny", value: "intensywny" },
										{ label: "relaksacyjny", value: "relaksacyjny" },
									],
									onChange: (val) => updateItem(activeIndex, "strength", val),
								}),
								el(SelectControl, {
									label: "Olejki",
									value: activeItem.oils,
									options: [
										{ label: "z olejkami", value: "tak" },
										{ label: "bez olejków", value: "nie" },
									],
									onChange: (val) => updateItem(activeIndex, "oils", val),
								}),
								el(SelectControl, {
									label: "Ubranie",
									value: activeItem.clothing,
									options: [
										{ label: "w bieliźnie", value: "w bieliźnie" },
										{ label: "bez ubrania", value: "bez ubrania" },
									],
									onChange: (val) => updateItem(activeIndex, "clothing", val),
								}),
								el(SelectControl, {
									label: "Tempo",
									value: activeItem.pace,
									options: [
										{ label: "wolne tempo", value: "wolne" },
										{ label: "tempo umiarkowane", value: "umiarkowane" },
										{ label: "szybkie tempo", value: "szybkie" },
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
								renderTag(item.strength),
								renderTag(item.oils),
								renderTag(item.clothing),
								renderTag(item.pace),
									renderScopeTag(item.scope),
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
