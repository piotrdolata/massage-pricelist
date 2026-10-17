<?php
/**
 * Plugin Name: Cennik Masaży - Gutenberg Block
 * Description: Dwukolumnowy blok cennika masaży z wariantami oraz ikonami cech i tooltipami.
 * Version: 1.0.0
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

/**
 * Definicje cech, opisów do tooltipów oraz ikon SVG.
 */
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

function mp_render_pricelist_block( $attributes ) {
	$items = isset( $attributes['items'] ) ? $attributes['items'] : array();
	if ( empty( $items ) ) {
		return '';
	}

	$features_map       = mp_get_features_map();
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'mp-grid' ) );

	ob_start();
	?>
	<div <?php echo $wrapper_attributes; ?>>
		<?php foreach ( $items as $item ) :
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
								<?php echo $data['svg']; ?>
							</span>
							<?php
						endif;
					endforeach;

					foreach ( $scope as $part ) :
						if ( isset( $features_map['scope'][ $part ] ) ) :
							$data = $features_map['scope'][ $part ];
							?>
							<span class="mp-feature-icon mp-feature-icon--scope" tabindex="0" data-tooltip="<?php echo esc_attr( $data['label'] ); ?>" aria-label="<?php echo esc_attr( $data['label'] ); ?>">
								<?php echo $data['svg']; ?>
							</span>
							<?php
						endif;
					endforeach;
					?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}