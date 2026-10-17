<?php
/**
 * Plugin Name: Cennik Masaży - Gutenberg Block
 * Description: Dwukolumnowy blok cennika masaży z filtrowaniem AJAX i własnymi ikonami SVG.
 * Version: 1.2.0
 * Author: WP Dev
 * Text Domain: massage-pricelist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mp_register_pricelist_block() {
	register_block_type( __DIR__, array(
		'render_callback' => 'mp_render_pricelist_block',
	) );
}
add_action( 'init', 'mp_register_pricelist_block' );

function mp_enqueue_google_fonts() {
	wp_enqueue_style(
		'mp-google-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Jost:ital,wght@0,100..900;1,100..900&display=swap',
		array(),
		null
	);
}
add_action( 'enqueue_block_assets', 'mp_enqueue_google_fonts' );

function mp_allow_svg_uploads( $mimes ) {
	if ( current_user_can( 'upload_files' ) ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'mp_allow_svg_uploads' );

function mp_fix_svg_mime_type( $data, $file, $filename, $mimes ) {
	$ext = isset( $data['ext'] ) ? $data['ext'] : '';
	if ( strlen( $ext ) < 1 ) {
		$exploded = explode( '.', $filename );
		$ext      = strtolower( end( $exploded ) );
	}
	if ( 'svg' === $ext ) {
		$data['type'] = 'image/svg+xml';
		$data['ext']  = 'svg';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'mp_fix_svg_mime_type', 10, 4 );

function mp_get_features_map() {
	return array(
		'strength' => array(
			'silny'       => array(
				'label' => 'Siła: Masaż intensywny / głęboki',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.5 6.5 17.5 17.5"/><path d="M3 10l7-7"/><path d="M14 21l7-7"/><path d="m2 6 4-4"/><path d="m18 22 4-4"/></svg>',
			),
			'umiarkowany' => array(
				'label' => 'Siła: Masaż umiarkowany / relaksacyjny',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0"/><path d="M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2"/><path d="M10 10.5V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg>',
			),
		),
		'oils' => array(
			'tak' => array(
				'label' => 'Olejki: Masaż z użyciem naturalnych olejków',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>',
			),
			'nie' => array(
				'label' => 'Olejki: Masaż na sucho (bez olejków)',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="2" y1="2" x2="22" y2="22"/><path d="M12 2.69l5.66 5.66a8 8 0 0 1-1.48 8.32"/><path d="M7.84 7.84a8 8 0 0 0 8.32 11.32"/></svg>',
			),
		),
		'clothing' => array(
			'bielizna' => array(
				'label' => 'Ubranie: W bieliźnie',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16v4c0 3-4 5-6 8h-4c-2-3-6-5-6-8V6z"/></svg>',
			),
			'nago'     => array(
				'label' => 'Ubranie: Nago (przykrycie ręcznikiem)',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="15"/></svg>',
			),
		),
		'pace' => array(
			'szybkie'     => array(
				'label' => 'Tempo: Szybkie i dynamiczne',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
			),
			'umiarkowane' => array(
				'label' => 'Tempo: Umiarkowane',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12c2-3 5-3 7 0s5 3 7 0 5-3 6 0"/><path d="M2 17c2-3 5-3 7 0s5 3 7 0 5-3 6 0"/></svg>',
			),
			'wolne'       => array(
				'label' => 'Tempo: Wolne i wyciszające',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
			),
		),
		'scope' => array(
			'kark'  => array(
				'label' => 'Zakres: Kark',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M9 11v4m6-4v4m-9 3c2-2 10-2 12 0"/></svg>',
			),
			'barki' => array(
				'label' => 'Zakres: Barki',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="6" r="3"/><path d="M4 18c0-4 3.5-6 8-6s8 2 8 6"/></svg>',
			),
			'plecy' => array(
				'label' => 'Zakres: Plecy',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 4v16M17 4v16M12 4v16M9 8h6M9 12h6M9 16h6"/></svg>',
			),
			'nogi'  => array(
				'label' => 'Zakres: Nogi',
				'svg'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3v12l-2 6m8-18v12l2 6"/></svg>',
			),
		),
	);
}

function mp_get_icon_markup( $feature_key, $default_svg, $custom_icons ) {
	if ( empty( $custom_icons[ $feature_key ] ) || ! is_array( $custom_icons[ $feature_key ] ) ) {
		return $default_svg;
	}

	$icon_data = $custom_icons[ $feature_key ];
	$att_id    = isset( $icon_data['id'] ) ? absint( $icon_data['id'] ) : 0;
	$url       = isset( $icon_data['url'] ) ? esc_url( $icon_data['url'] ) : '';

	if ( $att_id && 'image/svg+xml' === get_post_mime_type( $att_id ) ) {
		$file_path = get_attached_file( $att_id );
		if ( $file_path && file_exists( $file_path ) ) {
			$svg_content = file_get_contents( $file_path );
			if ( false !== strpos( $svg_content, '<svg' ) ) {
				return $svg_content;
			}
		}
	}

	if ( $url ) {
		return '<img src="' . $url . '" alt="" loading="lazy" />';
	}

	return $default_svg;
}

/**
 * Renderuje same karty masaży (używane przy pierwszym ładowaniu oraz w zapytaniu AJAX).
 */
function mp_render_cards_html( $items, $custom_icons, $active_category = 'wszystkie' ) {
	$features_map = mp_get_features_map();
	$found        = false;

	ob_start();
	foreach ( $items as $item ) :
		$item_cat = ! empty( $item['category'] ) ? $item['category'] : 'masaze';

		if ( 'wszystkie' !== $active_category && $item_cat !== $active_category ) {
			continue;
		}

		$found       = true;
		$title       = isset( $item['title'] ) ? $item['title'] : '';
		$description = isset( $item['description'] ) ? $item['description'] : '';
		$variants    = isset( $item['variants'] ) && is_array( $item['variants'] ) ? $item['variants'] : array();
		$scope       = isset( $item['scope'] ) && is_array( $item['scope'] ) ? $item['scope'] : array();
		?>
		<article class="mp-card">
			<div class="mp-card__body">
				<?php if ( ! empty( $title ) ) : ?>
					<h3 class="mp-card__title"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
					<p class="mp-card__desc"><?php echo wp_kses_post( $description ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $variants ) ) : ?>
					<ul class="mp-variants">
						<?php foreach ( $variants as $variant ) :
							if ( empty( $variant['duration'] ) && empty( $variant['price'] ) ) continue;
							?>
							<li class="mp-variants__item">
								<span class="mp-variants__duration"><?php echo esc_html( $variant['duration'] ?? '' ); ?></span>
								<span class="mp-variants__dots"></span>
								<span class="mp-variants__price"><?php echo esc_html( $variant['price'] ?? '' ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="mp-features" aria-label="Właściwości masażu">
				<?php
				$single_props = array( 'strength', 'oils', 'clothing', 'pace' );
				foreach ( $single_props as $prop ) :
					$val = isset( $item[ $prop ] ) ? $item[ $prop ] : '';
					if ( isset( $features_map[ $prop ][ $val ] ) ) :
						$data = $features_map[ $prop ][ $val ];
						?>
						<span class="mp-feature-icon" tabindex="0" data-tooltip="<?php echo esc_attr( $data['label'] ); ?>" aria-label="<?php echo esc_attr( $data['label'] ); ?>">
							<?php echo mp_get_icon_markup( $val, $data['svg'], $custom_icons ); ?>
						</span>
						<?php
					endif;
				endforeach;

				foreach ( $scope as $part ) :
					if ( isset( $features_map['scope'][ $part ] ) ) :
						$data = $features_map['scope'][ $part ];
						?>
						<span class="mp-feature-icon mp-feature-icon--scope" tabindex="0" data-tooltip="<?php echo esc_attr( $data['label'] ); ?>" aria-label="<?php echo esc_attr( $data['label'] ); ?>">
							<?php echo mp_get_icon_markup( $part, $data['svg'], $custom_icons ); ?>
						</span>
						<?php
					endif;
				endforeach;
				?>
			</div>
		</article>
		<?php
	endforeach;

	if ( ! $found ) {
		echo '<p class="mp-empty">Brak pozycji w wybranej kategorii.</p>';
	}

	return ob_get_clean();
}

function mp_render_pricelist_block( $attributes ) {
	$items        = isset( $attributes['items'] ) ? $attributes['items'] : array();
	$custom_icons = isset( $attributes['customIcons'] ) && is_array( $attributes['customIcons'] ) ? $attributes['customIcons'] : array();

	if ( empty( $items ) ) {
		return '';
	}

	$categories = array(
		'wszystkie' => 'Wszystkie',
		'masaze'    => 'Masaże',
		'zabiegi'   => 'Zabiegi i rytuały',
		'pakiety'   => 'Pakiety',
	);

	$encoded_attrs      = base64_encode( wp_json_encode( $attributes ) );
	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class'       => 'mp-pricelist-wrap',
		'data-ajax'   => esc_url( admin_url( 'admin-ajax.php' ) ),
		'data-nonce'  => wp_create_nonce( 'mp_filter_nonce' ),
		'data-config' => esc_attr( $encoded_attrs ),
	) );

	ob_start();
	?>
	<div <?php echo $wrapper_attributes; ?>>
		<div class="mp-filters" role="tablist" aria-label="Kategorie usług">
			<?php foreach ( $categories as $slug => $label ) : ?>
				<button
					type="button"
					class="mp-filter-btn<?php echo 'wszystkie' === $slug ? ' is-active' : ''; ?>"
					data-category="<?php echo esc_attr( $slug ); ?>"
				>
					<?php echo esc_html( $label ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="mp-grid">
			<?php echo mp_render_cards_html( $items, $custom_icons, 'wszystkie' ); ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Obsługa filtrowania AJAX.
 */
function mp_ajax_filter_items() {
	check_ajax_referer( 'mp_filter_nonce', 'nonce' );

	$category   = isset( $_POST['category'] ) ? sanitize_key( $_POST['category'] ) : 'wszystkie';
	$raw_config = isset( $_POST['config'] ) ? sanitize_text_field( wp_unslash( $_POST['config'] ) ) : '';
	$attributes = json_decode( base64_decode( $raw_config ), true );

	if ( ! is_array( $attributes ) ) {
		wp_send_json_error();
	}

	$items        = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
	$custom_icons = isset( $attributes['customIcons'] ) && is_array( $attributes['customIcons'] ) ? $attributes['customIcons'] : array();

	$html = mp_render_cards_html( $items, $custom_icons, $category );
	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_mp_filter_items', 'mp_ajax_filter_items' );
add_action( 'wp_ajax_nopriv_mp_filter_items', 'mp_ajax_filter_items' );
