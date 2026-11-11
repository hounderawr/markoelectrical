<?php
/**
 * Title: Maatek Header
 * Slug: maatek/header
 * Categories: header
 * Block Types: core/template-part/header
 * Description: Dark header with the site logo and the primary navigation menu (MENU / CLOSE drawer on mobile).
 * Inserter: no
 *
 * @package Maatek
 */

$maatek_nav_items = array(
	'home'               => __( 'Home', 'maatek' ),
	'control-automation' => __( 'Control & Automation', 'maatek' ),
	'maintenance'        => __( 'Maintenance', 'maatek' ),
	'fabrication'        => __( 'Fabrication', 'maatek' ),
	'installations'      => __( 'Installations', 'maatek' ),
	'projects'           => __( 'Projects', 'maatek' ),
	'contact'            => __( 'Contact', 'maatek' ),
);
?>
<!-- wp:group {"tagName":"header","className":"mtk-site-header","layout":{"type":"default"}} -->
<header class="wp-block-group mtk-site-header">
	<!-- wp:group {"className":"mtk-container mtk-header-inner","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container mtk-header-inner">
		<?php if ( has_custom_logo() ) : ?>
		<!-- wp:site-logo {"width":170,"isLink":true,"linkTarget":"_self","shouldSyncIcon":false} /-->
		<?php else : ?>
		<!-- wp:image {"width":"170px","height":"60px","sizeSlug":"full","linkDestination":"custom","href":"<?php echo esc_url( home_url( '/' ) ); ?>","className":"mtk-brand-block"} -->
		<figure class="wp-block-image size-full is-resized mtk-brand-block"><a class="mtk-brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/maatek-logo-cleaned.svg' ); ?>" alt="<?php echo esc_attr__( 'Maatek Electrical Services', 'maatek' ); ?>" style="width:170px;height:60px"/></a></figure>
		<!-- /wp:image -->
		<?php endif; ?>

		<?php
		$nav_ref_attr = '';
		$nav_post     = get_page_by_path( 'maatek-primary-navigation', OBJECT, 'wp_navigation' );
		if ( $nav_post instanceof WP_Post ) {
			$nav_ref_attr = ' "ref":' . (int) $nav_post->ID . ',';
		}
		?>
		<!-- wp:navigation {<?php echo $nav_ref_attr; ?>"overlayMenu":"mobile","hasIcon":false,"overlayBackgroundColor":"surface","overlayTextColor":"heading-dark","className":"mtk-nav","layout":{"type":"flex","justifyContent":"right","flexWrap":"nowrap"}} -->
		<?php echo maatek_navigation_links_markup( $maatek_nav_items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built with wp_json_encode. ?>
		<!-- /wp:navigation -->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
