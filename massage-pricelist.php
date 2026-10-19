<?php
/**
 * Plugin Name: Cennik Masaży - Gutenberg Block
 * Description: Dwukolumnowy blok cennika masaży z filtrowaniem AJAX .
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

	/**
	 * Renderuje same karty masaży (używane przy pierwszym ładowaniu oraz w zapytaniu AJAX).
	 */
	function mp_render_cards_html( $items, $active_category = 'wszystkie' ) {
		$labels = array(
			'intensywny' => 'intensywny',
			'silny' => 'intensywny',
			'umiarkowany' => 'relaksacyjny',
			'bielizna' => 'w bieliźnie',
			'nago' => 'bez ubrania',
			'relaksacyjny' => 'relaksacyjny',
			'tak' => 'z olejkami',
			'nie' => 'bez olejków',
			'w bieliźnie' => 'w bieliźnie',
			'bez ubrania' => 'bez ubrania',
			'wolne' => 'wolne tempo',
			'umiarkowane' => 'tempo umiarkowane',
			'szybkie' => 'szybkie tempo',
			'głowa' => 'głowa',
			'kark' => 'kark',
			'plecy' => 'plecy',
			'nogi' => 'nogi',
			'ramiona' => 'ramiona',
			'stopy' => 'stopy',
			'dłonie' => 'dłonie',
		);

		$found = false;

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
			$custom_label = isset( $item['customLabel'] ) ? $item['customLabel'] : '';
			$is_promo    = ! empty( $item['isPromo'] );
			?>
			<article class="mp-card">
				<div class="mp-card__body">
					<?php if ( ! empty( $custom_label ) ) : ?>
						<span class="mp-custom-label"><?php echo esc_html( $custom_label ); ?></span>
					<?php endif; ?>

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
								$price_val = esc_html( $variant['price'] ?? '' );
								$promo_val = esc_html( $variant['promoPrice'] ?? '' );
								?>
								<li class="mp-variants__item">
									<span class="mp-variants__duration"><?php echo esc_html( $variant['duration'] ?? '' ); ?></span>
									<span class="mp-variants__dots" aria-hidden="true"></span>
									<?php if ( $is_promo && ! empty( $promo_val ) ) : ?>
										<span class="mp-promo-tag">PROMOCJA</span>
										<span class="mp-variants__price mp-promo-price"><span class="screen-reader-text">Nowa cena: </span><?php echo $promo_val; ?></span>
										<del class="mp-variants__price mp-old-price"><span class="screen-reader-text">Stara cena: </span><?php echo $price_val; ?></del>
									<?php else : ?>
										<span class="mp-variants__price"><?php echo $price_val; ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<ul class="mp-features" aria-label="Właściwości masażu">
					<?php
					$single_props = array( 'strength', 'oils', 'clothing', 'pace' );
					foreach ( $single_props as $prop ) :
						$val = isset( $item[ $prop ] ) ? $item[ $prop ] : '';
						if ( isset( $labels[ $val ] ) ) :
							?>
							<li class="mp-feature-pill">
								<?php echo esc_html( $labels[ $val ] ); ?>
							</li>
							<?php
						endif;
					endforeach;

					if ( ! empty( $scope ) ) {
						$scope_labels = array();
						foreach ( $scope as $part ) {
							if ( isset( $labels[ $part ] ) ) {
								$scope_labels[] = $labels[ $part ];
							}
						}
						if ( ! empty( $scope_labels ) ) {
							?>
							<li class="mp-feature-pill">
								<?php echo esc_html( 'zakres: ' . implode( ', ', $scope_labels ) ); ?>
							</li>
							<?php
						}
					}
					?>
				</ul>
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
		<div class="mp-filters" role="group" aria-label="Kategorie usług">
			<?php foreach ( $categories as $slug => $label ) :
				$is_active = ( 'wszystkie' === $slug );
			?>
				<button
					type="button"
					class="mp-filter-btn<?php echo $is_active ? ' is-active' : ''; ?>"
					data-category="<?php echo esc_attr( $slug ); ?>"
					aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
				>
					<?php echo esc_html( $label ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="mp-grid" aria-live="polite">
			<?php echo mp_render_cards_html( $items, 'wszystkie' ); ?>
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

	$html = mp_render_cards_html( $items, $category );
	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_mp_filter_items', 'mp_ajax_filter_items' );
add_action( 'wp_ajax_nopriv_mp_filter_items', 'mp_ajax_filter_items' );
