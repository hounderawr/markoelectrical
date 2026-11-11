<?php
/**
 * Title: Maatek Footer
 * Slug: maatek/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Three-column dark footer with brand, Explore menu and project enquiry contact details.
 * Inserter: no
 *
 * @package Maatek
 */

$maatek_footer_items = array(
	'home'               => __( 'Home', 'maatek' ),
	'control-automation' => __( 'Control & Automation', 'maatek' ),
	'maintenance'        => __( 'Maintenance', 'maatek' ),
	'fabrication'        => __( 'Fabrication', 'maatek' ),
	'installations'      => __( 'Installations', 'maatek' ),
	'projects'           => __( 'Projects', 'maatek' ),
	'contact'            => __( 'Contact', 'maatek' ),
);
?>
<!-- wp:group {"tagName":"footer","className":"mtk-site-footer","layout":{"type":"default"}} -->
<footer class="wp-block-group mtk-site-footer">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:columns {"className":"mtk-footer-grid"} -->
		<div class="wp-block-columns mtk-footer-grid">
			<!-- wp:column {"className":"mtk-footer-col"} -->
			<div class="wp-block-column mtk-footer-col">
				<!-- wp:paragraph {"className":"mtk-footer-brand-title"} -->
				<p class="mtk-footer-brand-title"><?php esc_html_e( 'MAATEK ELECTRICAL SERVICES', 'maatek' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"mtk-small mtk-text-light mtk-mt-2"} -->
				<p class="mtk-small mtk-text-light mtk-mt-2"><?php esc_html_e( 'Industrial electrical engineering', 'maatek' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"mtk-footer-col"} -->
			<div class="wp-block-column mtk-footer-col">
				<!-- wp:heading {"level":2,"className":"mtk-footer-heading"} -->
				<h2 class="wp-block-heading mtk-footer-heading"><?php esc_html_e( 'EXPLORE', 'maatek' ); ?></h2>
				<!-- /wp:heading -->

				<?php
				$footer_ref_attr = '';
				$footer_nav_post = get_page_by_path( 'maatek-footer-navigation', OBJECT, 'wp_navigation' );
				if ( ! ( $footer_nav_post instanceof WP_Post ) ) {
					$footer_nav_post = get_page_by_path( 'maatek-primary-navigation', OBJECT, 'wp_navigation' );
				}
				if ( $footer_nav_post instanceof WP_Post ) {
					$footer_ref_attr = ' "ref":' . (int) $footer_nav_post->ID . ',';
				}
				?>
				<!-- wp:navigation {<?php echo $footer_ref_attr; ?>"overlayMenu":"never","className":"mtk-footer-nav","layout":{"type":"flex","orientation":"vertical"}} -->
				<?php echo maatek_navigation_links_markup( $maatek_footer_items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built with wp_json_encode. ?>
				<!-- /wp:navigation -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"mtk-footer-col"} -->
			<div class="wp-block-column mtk-footer-col">
				<!-- wp:heading {"level":2,"className":"mtk-footer-heading"} -->
				<h2 class="wp-block-heading mtk-footer-heading"><?php esc_html_e( 'PROJECT ENQUIRIES', 'maatek' ); ?></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"mtk-small mtk-font-bold mtk-text-white mtk-mb-2"} -->
				<p class="mtk-small mtk-font-bold mtk-text-white mtk-mb-2">Marko Markovic</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"mtk-mb-2"} -->
				<p class="mtk-mb-2"><a href="tel:+61414421651">0414 421 651</a></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph -->
				<p><a href="mailto:marko@maatekelectrical.com.au">marko@maatekelectrical.com.au</a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</footer>
<!-- /wp:group -->
