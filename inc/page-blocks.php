<?php
/**
 * Native WordPress Gutenberg Block Content Definitions for all 7 Maatek Pages.
 *
 * Every section, card, heading, paragraph, button, and image is built with native
 * core WordPress blocks (wp:group, wp:columns, wp:column, wp:heading, wp:paragraph,
 * wp:buttons, wp:button, wp:image) styled with the custom Maatek utility & component
 * classes (mtk-*), guided by the refined Figma file (zyX5yMKsZjHAqzBjn359vo).
 *
 * @package Maatek
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper: Build a native Gutenberg Photo Card block (wp:column + wp:group + wp:image + wp:paragraph).
 *
 * @param string $img_url     Full URL to the image asset.
 * @param string $title       Card title.
 * @param string $description Card description caption.
 * @param string $extra_class Optional extra class on .mtk-photo-card.
 * @return string Gutenberg block markup.
 */
function maatek_block_photo_card( $img_url, $title, $description, $extra_class = '' ) {
	$card_class = trim( 'mtk-photo-card ' . $extra_class );
	$esc_url    = esc_url( $img_url );
	$esc_title  = esc_html( $title );
	$esc_alt    = esc_attr( $title );
	$esc_desc   = esc_html( $description );

	return <<<HTML
<!-- wp:column {"className":"{$card_class}"} -->
<div class="wp-block-column {$card_class}">
	<!-- wp:group {"className":"mtk-photo-card-media","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-photo-card-media">
		<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="{$esc_url}" alt="{$esc_alt}"/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"mtk-photo-card-body","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-photo-card-body">
		<!-- wp:paragraph {"className":"mtk-card-title mtk-body mtk-font-bold mtk-text-deep mtk-mb-1"} -->
		<p class="mtk-card-title mtk-body mtk-font-bold mtk-text-deep mtk-mb-1">{$esc_title}</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-card-desc mtk-small mtk-text-muted"} -->
		<p class="mtk-card-desc mtk-small mtk-text-muted">{$esc_desc}</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:column -->
HTML;
}

/**
 * Helper: Build a Service Page alternating feature card row (What we deliver).
 *
 * @param array $args {
 *     @type string   $id            Anchor ID for the row.
 *     @type string   $overline      Overline text (e.g. "01", "02 / PLC PROGRAMMING").
 *     @type string   $title         H3 heading text.
 *     @type string[] $paragraphs    Body paragraphs.
 *     @type string[] $bullets       Square-bullet items.
 *     @type string   $img_url       Image asset URL.
 *     @type string   $img_alt       Image alt text.
 *     @type string   $img_caption   Optional dark overlay caption on the photo.
 *     @type bool     $image_on_left True for image left + copy right; false for copy left + image right.
 * }
 * @return string Gutenberg block markup.
 */
function maatek_block_service_feature_row( array $args ) {
	$id            = ! empty( $args['id'] ) ? esc_attr( $args['id'] ) : '';
	$overline_raw  = isset( $args['overline'] ) ? (string) $args['overline'] : '';
	$overline      = esc_html( $overline_raw );
	$title         = esc_html( $args['title'] );
	$img_url       = esc_url( $args['img_url'] );
	$img_alt       = esc_attr( $args['img_alt'] );
	$img_caption   = ! empty( $args['img_caption'] ) ? esc_html( $args['img_caption'] ) : '';
	$image_on_left = ! empty( $args['image_on_left'] );

	$row_classes = 'mtk-service-row mtk-service-row--service-page';
	if ( ! $image_on_left ) {
		$row_classes .= ' mtk-service-row--reverse';
	}

	$caption_block = '';
	if ( '' !== $img_caption ) {
		$caption_block = <<<HTML
		<!-- wp:paragraph {"className":"mtk-media-caption-overlay"} -->
		<p class="mtk-media-caption-overlay">{$img_caption}</p>
		<!-- /wp:paragraph -->
HTML;
	}

	$media_col = <<<HTML
	<!-- wp:column {"className":"mtk-service-media"} -->
	<div class="wp-block-column mtk-service-media">
		<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="{$img_url}" alt="{$img_alt}"/></figure>
		<!-- /wp:image -->
{$caption_block}
	</div>
	<!-- /wp:column -->
HTML;

	$paragraphs_html = '';
	if ( ! empty( $args['paragraphs'] ) ) {
		foreach ( $args['paragraphs'] as $p_text ) {
			$esc_p            = esc_html( $p_text );
			$paragraphs_html .= <<<HTML
		<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-mt-3"} -->
		<p class="mtk-body mtk-text-muted-dark mtk-mt-3">{$esc_p}</p>
		<!-- /wp:paragraph -->
HTML;
		}
	}

	$bullets_html = '';
	if ( ! empty( $args['bullets'] ) ) {
		$bullets_items = '';
		foreach ( $args['bullets'] as $b_text ) {
			$esc_b          = esc_html( $b_text );
			$bullets_items .= <<<HTML
			<!-- wp:paragraph {"className":"mtk-check-item"} -->
			<p class="mtk-check-item">{$esc_b}</p>
			<!-- /wp:paragraph -->
HTML;
		}
		$bullets_html = <<<HTML
		<!-- wp:group {"className":"mtk-check-list","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-check-list">
{$bullets_items}
		</div>
		<!-- /wp:group -->
HTML;
	}

	$overline_html = '';
	if ( '' !== $overline && ! preg_match( '/^\d/', $overline_raw ) ) {
		$overline_html = <<<HTML
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">{$overline}</p>
		<!-- /wp:paragraph -->
HTML;
	}

	$copy_col = <<<HTML
	<!-- wp:column {"className":"mtk-service-body"} -->
	<div class="wp-block-column mtk-service-body">
{$overline_html}
		<!-- wp:heading {"level":3,"className":"mtk-h3 mtk-text-deep"} -->
		<h3 class="wp-block-heading mtk-h3 mtk-text-deep">{$title}</h3>
		<!-- /wp:heading -->
{$paragraphs_html}
{$bullets_html}
	</div>
	<!-- /wp:column -->
HTML;

	$inner_cols = $image_on_left ? ( $media_col . "\n" . $copy_col ) : ( $copy_col . "\n" . $media_col );
	$id_attr    = $id ? ' id="' . $id . '"' : '';
	$col_attrs  = array();
	if ( '' !== $id ) {
		$col_attrs['anchor'] = $id;
	}
	$col_attrs['className'] = $row_classes;
	$col_json               = wp_json_encode( $col_attrs );

	return <<<HTML
<!-- wp:columns {$col_json} -->
<div class="wp-block-columns {$row_classes}"{$id_attr}>
{$inner_cols}
</div>
<!-- /wp:columns -->
HTML;
}

/**
 * Helper: Build the standard Service Page Project Enquiry split CTA band (Figma Node 11:381).
 *
 * @param string $contact_url URL to the Contact page.
 * @return string Gutenberg block markup.
 */
function maatek_block_service_enquiry_cta( $contact_url ) {
	$esc_contact = esc_url( $contact_url );

	return <<<HTML
<!-- wp:group {"tagName":"section","className":"mtk-cta-band","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-cta-band">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:columns {"className":"mtk-cta-split"} -->
		<div class="wp-block-columns mtk-cta-split">
			<!-- wp:column {"className":"mtk-cta-left"} -->
			<div class="wp-block-column mtk-cta-left">
				<!-- wp:paragraph {"className":"mtk-overline mtk-overline--sky mtk-mb-3"} -->
				<p class="mtk-overline mtk-overline--sky mtk-mb-3">LET’S TALK</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"className":"mtk-h2-lg mtk-text-white mtk-mb-3"} -->
				<h2 class="wp-block-heading mtk-h2-lg mtk-text-white mtk-mb-3">Planning an industrial project?</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"mtk-body mtk-text-light mtk-max-w-520"} -->
				<p class="mtk-body mtk-text-light mtk-max-w-520">Discuss the scope, site constraints and the outcome you need with the Maatek Electrical team.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"mtk-cta-right"} -->
			<div class="wp-block-column mtk-cta-right">
				<!-- wp:paragraph {"className":"mtk-overline mtk-overline--sky mtk-mb-2"} -->
				<p class="mtk-overline mtk-overline--sky mtk-mb-2">PROJECT ENQUIRIES</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"mtk-body mtk-font-bold mtk-text-white mtk-mb-4"} -->
				<p class="mtk-body mtk-font-bold mtk-text-white mtk-mb-4">Talk to Marko Markovic · <a class="mtk-text-white" href="tel:+61414421651">0414 421 651</a></p>
				<!-- /wp:paragraph -->
				<!-- wp:buttons {"className":"mtk-btn-group"} -->
				<div class="wp-block-buttons mtk-btn-group">
					<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
					<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$esc_contact}">Contact Maatek Electrical <span class="mtk-arrow">→</span></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
HTML;
}

function maatek_get_page_url( $slug ) {
	if ( 'home' === $slug || '' === $slug ) {
		return trailingslashit( home_url( '/' ) );
	}
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page instanceof WP_Post ) {
		$permalink = get_permalink( $page->ID );
		if ( is_string( $permalink ) && '' !== $permalink ) {
			return $permalink;
		}
	}
	return trailingslashit( home_url( '/' . ltrim( $slug, '/' ) ) );
}

/**
 * Return all 7 Maatek pages with their title, slug, and native Gutenberg block content.
 *
 * @return array<string, array{title: string, content: string}>
 */
function maatek_get_seeded_pages() {
	$assets  = untrailingslashit( get_template_directory_uri() ) . '/assets';
	$home    = maatek_get_page_url( 'home' );
	$contact = maatek_get_page_url( 'contact' );
	$ctrl    = maatek_get_page_url( 'control-automation' );
	$maint   = maatek_get_page_url( 'maintenance' );
	$fab     = maatek_get_page_url( 'fabrication' );
	$inst    = maatek_get_page_url( 'installations' );

	/* ======================================================================
	 * 1. HOME PAGE (Figma Node 11:194)
	 * ====================================================================== */
	$home_content = <<<HTML
<!-- wp:cover {"url":"{$assets}/9241893b534162b6.webp","dimRatio":70,"overlayColor":"heading-dark","isUserOverlayColor":true,"minHeight":680,"minHeightUnit":"px","tagName":"section","className":"mtk-hero-home","layout":{"type":"default"}} -->
<section class="wp-block-cover mtk-hero-home" style="min-height:680px"><span aria-hidden="true" class="wp-block-cover__background has-heading-dark-background-color has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{$assets}/9241893b534162b6.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-eyebrow-text mtk-eyebrow-with-bar"} -->
		<p class="mtk-eyebrow-text mtk-eyebrow-with-bar">INDUSTRIAL ELECTRICAL ENGINEERING</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":1,"className":"mtk-hero-home-title"} -->
		<h1 class="wp-block-heading mtk-hero-home-title">Electrical Control <span class="mtk-accent-line">&amp; Automation</span></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"mtk-hero-home-subtitle"} -->
		<p class="mtk-hero-home-subtitle">Industrial Control &nbsp;|&nbsp; Automation &nbsp;|&nbsp; Electrical &nbsp;|&nbsp; Turnkey Solutions</p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"className":"mtk-btn-group"} -->
		<div class="wp-block-buttons mtk-btn-group">
			<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
			<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$contact}">Discuss your project <span class="mtk-arrow">→</span></a></div>
			<!-- /wp:button -->
			<!-- wp:button {"className":"mtk-btn mtk-btn-hero-outline"} -->
			<div class="wp-block-button mtk-btn mtk-btn-hero-outline"><a class="wp-block-button__link wp-element-button" href="#home-services">Explore services <span class="mtk-arrow">→</span></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div></section>
<!-- /wp:cover -->

<!-- wp:group {"tagName":"section","className":"mtk-bg-surface mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-surface mtk-py-16">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">OUR EXPERTISE</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Integrated industrial solutions</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"mtk-body mtk-text-muted mtk-max-w-720"} -->
		<p class="mtk-body mtk-text-muted mtk-max-w-720">Maatek Electrical provides electrical control and automation, industrial maintenance, mechanical fabrication and factory installations. Explore our services and project galleries.</p>
		<!-- /wp:paragraph -->

		<!-- wp:columns {"className":"mtk-shortcut-grid"} -->
		<div class="wp-block-columns mtk-shortcut-grid">
			<!-- wp:column {"className":"mtk-shortcut-col"} -->
			<div class="wp-block-column mtk-shortcut-col">
				<!-- wp:paragraph -->
				<p><a class="mtk-shortcut" href="{$ctrl}"><span>Control &amp; Automation</span><span class="mtk-arrow">→</span></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column {"className":"mtk-shortcut-col"} -->
			<div class="wp-block-column mtk-shortcut-col">
				<!-- wp:paragraph -->
				<p><a class="mtk-shortcut" href="{$maint}"><span>Maintenance</span><span class="mtk-arrow">→</span></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column {"className":"mtk-shortcut-col"} -->
			<div class="wp-block-column mtk-shortcut-col">
				<!-- wp:paragraph -->
				<p><a class="mtk-shortcut" href="{$fab}"><span>Fabrication</span><span class="mtk-arrow">→</span></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column {"className":"mtk-shortcut-col"} -->
			<div class="wp-block-column mtk-shortcut-col">
				<!-- wp:paragraph -->
				<p><a class="mtk-shortcut" href="{$inst}"><span>Installations</span><span class="mtk-arrow">→</span></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"home-services","className":"mtk-bg-section mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-section mtk-py-16" id="home-services">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">WHAT WE DO</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Industrial expertise, from concept to commissioning</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"mtk-body mtk-text-muted mtk-max-w-720 mtk-mb-10"} -->
		<p class="mtk-body mtk-text-muted mtk-max-w-720 mtk-mb-10">Explore the capabilities that bring electrical, mechanical and automation systems together.</p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"className":"mtk-service-stack","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-service-stack">
			<!-- wp:columns {"className":"mtk-service-row"} -->
			<div class="wp-block-columns mtk-service-row">
				<!-- wp:column {"className":"mtk-service-media"} -->
				<div class="wp-block-column mtk-service-media">
					<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image size-full"><img src="{$assets}/home-control-cabinet-enhanced.jpg" alt="Industrial electrical control cabinet and switchboard"/></figure>
					<!-- /wp:image -->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"className":"mtk-service-body"} -->
				<div class="wp-block-column mtk-service-body">
					<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
					<p class="mtk-overline mtk-mb-2">SERVICES</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3,"className":"mtk-h3 mtk-mb-3"} -->
					<h3 class="wp-block-heading mtk-h3 mtk-mb-3">Control &amp; Automation</h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-mb-6"} -->
					<p class="mtk-body mtk-text-muted-dark mtk-mb-6">Electrical switchboards, PLC programming, HMI and SCADA systems and machinery safety designed around the needs of industrial production.</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph -->
					<p><a class="mtk-link-arrow" href="{$ctrl}"><span>Explore Control &amp; Automation</span><span class="mtk-arrow">→</span></a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->

			<!-- wp:columns {"className":"mtk-service-row mtk-service-row--reverse"} -->
			<div class="wp-block-columns mtk-service-row mtk-service-row--reverse">
				<!-- wp:column {"className":"mtk-service-body"} -->
				<div class="wp-block-column mtk-service-body">
					<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
					<p class="mtk-overline mtk-mb-2">SERVICES</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3,"className":"mtk-h3 mtk-mb-3"} -->
					<h3 class="wp-block-heading mtk-h3 mtk-mb-3">Maintenance</h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-mb-6"} -->
					<p class="mtk-body mtk-text-muted-dark mtk-mb-6">Practical support for industrial plant reliability, troubleshooting, equipment servicing and planned maintenance.</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph -->
					<p><a class="mtk-link-arrow" href="{$maint}"><span>Explore Maintenance</span><span class="mtk-arrow">→</span></a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"className":"mtk-service-media"} -->
				<div class="wp-block-column mtk-service-media">
					<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image size-full"><img src="{$assets}/home-robot-maintenance-enhanced.jpg" alt="Industrial robot and plant maintenance"/></figure>
					<!-- /wp:image -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->

			<!-- wp:columns {"className":"mtk-service-row"} -->
			<div class="wp-block-columns mtk-service-row">
				<!-- wp:column {"className":"mtk-service-media"} -->
				<div class="wp-block-column mtk-service-media">
					<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image size-full"><img src="{$assets}/home-fabrication-workshop-enhanced.webp" alt="Industrial stainless fabrication and workshop assembly"/></figure>
					<!-- /wp:image -->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"className":"mtk-service-body"} -->
				<div class="wp-block-column mtk-service-body">
					<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
					<p class="mtk-overline mtk-mb-2">SERVICES</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3,"className":"mtk-h3 mtk-mb-3"} -->
					<h3 class="wp-block-heading mtk-h3 mtk-mb-3">Fabrication</h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-mb-6"} -->
					<p class="mtk-body mtk-text-muted-dark mtk-mb-6">Mechanical fabrication and equipment integration, with attention to plant access, installation requirements and maintainability.</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph -->
					<p><a class="mtk-link-arrow" href="{$fab}"><span>Explore Fabrication</span><span class="mtk-arrow">→</span></a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->

			<!-- wp:columns {"className":"mtk-service-row mtk-service-row--reverse"} -->
			<div class="wp-block-columns mtk-service-row mtk-service-row--reverse">
				<!-- wp:column {"className":"mtk-service-body"} -->
				<div class="wp-block-column mtk-service-body">
					<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
					<p class="mtk-overline mtk-mb-2">SERVICES</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3,"className":"mtk-h3 mtk-mb-3"} -->
					<h3 class="wp-block-heading mtk-h3 mtk-mb-3">Installations</h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-mb-6"} -->
					<p class="mtk-body mtk-text-muted-dark mtk-mb-6">Industrial equipment installation, machinery integration and coordinated commissioning in working production environments.</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph -->
					<p><a class="mtk-link-arrow" href="{$inst}"><span>Explore Installations</span><span class="mtk-arrow">→</span></a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"className":"mtk-service-media"} -->
				<div class="wp-block-column mtk-service-media">
					<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image size-full"><img src="{$assets}/f00e6bf8ff0c2934.webp" alt="Industrial plant equipment installation"/></figure>
					<!-- /wp:image -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-cta-band","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-cta-band">
		<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-container">
			<!-- wp:columns {"className":"mtk-cta-split"} -->
			<div class="wp-block-columns mtk-cta-split">
				<!-- wp:column {"className":"mtk-cta-left"} -->
				<div class="wp-block-column mtk-cta-left">
					<!-- wp:paragraph {"className":"mtk-overline mtk-overline--sky mtk-mb-3"} -->
					<p class="mtk-overline mtk-overline--sky mtk-mb-3">START A CONVERSATION</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"className":"mtk-h2-lg mtk-text-white mtk-mb-3"} -->
					<h2 class="wp-block-heading mtk-h2-lg mtk-text-white mtk-mb-3">Discuss your next industrial project</h2>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"className":"mtk-body mtk-text-light mtk-max-w-520"} -->
					<p class="mtk-body mtk-text-light mtk-max-w-520">From controls upgrades to complete production-line installations, contact Maatek Electrical to discuss your requirements.</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->

				<!-- wp:column {"className":"mtk-cta-right"} -->
				<div class="wp-block-column mtk-cta-right">
					<!-- wp:paragraph {"className":"mtk-overline mtk-overline--sky mtk-mb-2"} -->
					<p class="mtk-overline mtk-overline--sky mtk-mb-2">PROJECT ENQUIRIES</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"mtk-body mtk-font-bold mtk-text-white mtk-mb-4"} -->
					<p class="mtk-body mtk-font-bold mtk-text-white mtk-mb-4">Talk to Marko Markovic · <a class="mtk-text-white" href="tel:+61414421651">0414 421 651</a></p>
					<!-- /wp:paragraph -->
					<!-- wp:buttons {"className":"mtk-btn-group"} -->
					<div class="wp-block-buttons mtk-btn-group">
						<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
						<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$contact}">Contact Maatek Electrical <span class="mtk-arrow">→</span></a></div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
HTML;

	/* ======================================================================
	 * 2. CONTROL & AUTOMATION PAGE (Figma Node 11:288)
	 * ====================================================================== */
	$ctrl_row_1 = maatek_block_service_feature_row(
		array(
			'id'            => 'electrical-switch-board-manufacturing',
			'overline'      => '01',
			'title'         => 'Electrical Switch Board Manufacturing',
			'paragraphs'    => array(
				'Industrial switchboards, motor control centres and distribution panels form the electrical backbone of a plant. Layout, component selection, protection, cable management and clear identification all contribute to reliable operation and efficient servicing.',
			),
			'bullets'       => array(
				'Custom switchboards and control panels',
				'Motor control and distribution assemblies',
				'Clear labelling, layout and service access',
			),
			'img_url'       => "{$assets}/small-control-switchboard-enhanced.webp",
			'img_alt'       => 'Compact industrial electrical control switchboard with PLC, breakers and neatly organised wiring',
			'image_on_left' => true,
		)
	);

	$ctrl_row_2 = maatek_block_service_feature_row(
		array(
			'id'            => 'plc-design-and-automation',
			'overline'      => '02 / PLC PROGRAMMING',
			'title'         => 'PLC Design and Programming',
			'paragraphs'    => array(
				'Maatek Electrical designs, programs and supports Rockwell Automation and Allen-Bradley PLC systems, including ControlLogix, CompactLogix and legacy platforms.',
				'Using Studio 5000 Logix Designer, we develop and modify ladder logic, machine sequencing, interlocks, alarms, instrumentation and industrial communications. Services include system upgrades, fault diagnosis, commissioning and ongoing technical support.',
			),
			'bullets'       => array(
				'ControlLogix, CompactLogix and legacy PLC upgrades',
				'Studio 5000 ladder logic, interlocks and sequencing',
				'Fault finding, commissioning and plant integration',
			),
			'img_url'       => "{$assets}/plc-rockwell-programming.webp",
			'img_alt'       => 'Engineer using Rockwell FactoryTalk SCADA and Studio 5000 ladder logic on dual monitors',
			'image_on_left' => false,
		)
	);

	$ctrl_row_3 = maatek_block_service_feature_row(
		array(
			'id'            => 'scada-hmi-development',
			'overline'      => '02 / SCADA & HMI',
			'title'         => 'SCADA Development and Operator Interfaces',
			'paragraphs'    => array(
				'We develop operator interfaces and supervisory control systems using Rockwell FactoryTalk View and related HMI software. Clear, practical screens help operators monitor equipment, respond to faults and manage production.',
			),
			'bullets'       => array(
				'FactoryTalk View HMI and SCADA graphics',
				'Alarm management, trends and operator diagnostics',
				'Integration with PLCs, networks and plant equipment',
			),
			'img_url'       => "{$assets}/e8caa829e54e1e25.webp",
			'img_alt'       => 'Industrial SCADA operator interface showing process information and machine controls',
			'img_caption'   => 'Operator interface and SCADA process visualisation',
			'image_on_left' => true,
		)
	);

	$ctrl_row_4 = maatek_block_service_feature_row(
		array(
			'id'            => 'safety',
			'overline'      => '03',
			'title'         => 'Safety',
			'paragraphs'    => array(
				'Machine safety is integrated into the overall design, not treated as an afterthought. Light curtains, safety relays, safety PLCs, interlocks and physical fencing can form coordinated protective measures appropriate to the machinery and its assessed risks.',
			),
			'bullets'       => array(
				'Safety circuits, relays and interlocked guarding',
				'Light curtains, emergency stops and access control',
				'Safety integration with machine control systems',
			),
			'img_url'       => "{$assets}/safety-fencing.webp",
			'img_alt'       => 'Industrial safety fencing with yellow posts, access gate and machinery warning sign',
			'image_on_left' => false,
		)
	);

	$ctrl_photo_1 = maatek_block_photo_card(
		"{$assets}/82a4799828e824f7.webp",
		'Industrial switchboard circuit breaker',
		'Switchboard protection and isolation equipment forms part of a maintainable electrical control system.'
	);
	$ctrl_photo_2 = maatek_block_photo_card(
		"{$assets}/e8caa829e54e1e25.webp",
		'SCADA operator interface',
		'Process overview displays bring plant status, alarms and operating information together.'
	);
	$ctrl_photo_3 = maatek_block_photo_card(
		"{$assets}/b70408ecf9552a48.webp",
		'Machine control interface',
		'Operator controls provide clear machine status and practical access to operating functions.'
	);
	$ctrl_photo_4 = maatek_block_photo_card(
		"{$assets}/70055cff8dc9b49a.webp",
		'Automation panel components',
		'Industrial control panels integrate protection, power supplies and field control connections.'
	);

	$ctrl_cta = maatek_block_service_enquiry_cta( $contact );

	$ctrl_content = <<<HTML
<!-- wp:cover {"url":"{$assets}/af7b40fe4ce443f7.webp","dimRatio":70,"overlayColor":"heading-dark","isUserOverlayColor":true,"minHeight":470,"minHeightUnit":"px","tagName":"section","className":"mtk-hero-service","layout":{"type":"default"}} -->
<section class="wp-block-cover mtk-hero-service" style="min-height:470px"><span aria-hidden="true" class="wp-block-cover__background has-heading-dark-background-color has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{$assets}/af7b40fe4ce443f7.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-hero-kicker"} -->
		<p class="mtk-hero-kicker">MAATEK ELECTRICAL / CONTROL &amp; AUTOMATION</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1,"className":"mtk-hero-service-title mtk-max-w-760"} -->
		<h1 class="wp-block-heading mtk-hero-service-title mtk-max-w-760">Intelligent control, built for the realities of industry</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-hero-service-lead"} -->
		<p class="mtk-hero-service-lead">Effective automation combines dependable electrical infrastructure, well-designed control logic and clear operator information. The result is a system that supports safer operation, consistent production and easier fault diagnosis.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"className":"mtk-btn-group"} -->
		<div class="wp-block-buttons mtk-btn-group">
			<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
			<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$contact}">Discuss your project <span class="mtk-arrow">→</span></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div></section>
<!-- /wp:cover -->

<!-- wp:group {"tagName":"nav","className":"mtk-subnav-bar","layout":{"type":"default"}} -->
<nav class="wp-block-group mtk-subnav-bar">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline"} -->
		<p class="mtk-overline">EXPLORE THIS PAGE</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-subnav-links"} -->
		<p class="mtk-subnav-links"><a href="#electrical-switch-board-manufacturing">Electrical Switch Board Manufacturing</a> <span class="mtk-subnav-sep">·</span> <a href="#plc-design-and-automation">PLC Design and Automation</a> <span class="mtk-subnav-sep">·</span> <a href="#scada-hmi-development">SCADA &amp; HMI</a> <span class="mtk-subnav-sep">·</span> <a href="#safety">Safety</a> <span class="mtk-subnav-sep">·</span> <a href="#photo-gallery">Project photographs</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</nav>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-approach-section","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-approach-section">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:columns {"className":"mtk-approach-grid"} -->
		<div class="wp-block-columns mtk-approach-grid">
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
				<p class="mtk-overline mtk-mb-2">THE MAATEK APPROACH</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"className":"mtk-h2"} -->
				<h2 class="wp-block-heading mtk-h2">Built around your operation.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-pt-4"} -->
				<p class="mtk-body mtk-text-muted-dark mtk-pt-4">From a new switchboard through to PLC and HMI upgrades, each element needs to work as one coordinated system. Design decisions should consider future expansion, maintenance access, operator usability and the safety functions required by the application.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-bg-section mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-section mtk-py-16">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">SPECIALIST SERVICES</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-8"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-8">What we deliver</h2>
		<!-- /wp:heading -->

		<!-- wp:group {"className":"mtk-service-stack","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-service-stack">
{$ctrl_row_1}
{$ctrl_row_2}
{$ctrl_row_3}

			<!-- wp:columns {"className":"mtk-detail-grid"} -->
			<div class="wp-block-columns mtk-detail-grid">
				<!-- wp:column {"className":"mtk-detail-card"} -->
				<div class="wp-block-column mtk-detail-card">
					<!-- wp:heading {"level":4,"className":"mtk-h4 mtk-mb-2"} -->
					<h4 class="wp-block-heading mtk-h4 mtk-mb-2">Operator interface development</h4>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
					<p class="mtk-small mtk-text-muted">Process overview displays, machine controls, equipment status, alarm summaries and intuitive navigation tailored to the plant.</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"className":"mtk-detail-card"} -->
				<div class="wp-block-column mtk-detail-card">
					<!-- wp:heading {"level":4,"className":"mtk-h4 mtk-mb-2"} -->
					<h4 class="wp-block-heading mtk-h4 mtk-mb-2">SCADA integration and support</h4>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
					<p class="mtk-small mtk-text-muted">Historical trends, production reporting, communications integration, system upgrades, commissioning and ongoing troubleshooting.</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->

{$ctrl_row_4}
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"mtk-mt-12 mtk-pt-8 mtk-border-top","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-mt-12 mtk-pt-8 mtk-border-top">
			<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
			<p class="mtk-overline mtk-mb-2">SAFETY SYSTEMS</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
			<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Protection engineered into the process.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"mtk-body mtk-text-muted mtk-max-w-760"} -->
			<p class="mtk-body mtk-text-muted mtk-max-w-760">Protective measures should be selected through a machinery risk assessment and integrated with the control system. These examples illustrate common safety components.</p>
			<!-- /wp:paragraph -->

			<!-- wp:columns {"className":"mtk-safety-grid"} -->
			<div class="wp-block-columns mtk-safety-grid">
				<!-- wp:column {"className":"mtk-safety-card"} -->
				<div class="wp-block-column mtk-safety-card">
					<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image size-full"><img src="{$assets}/safety-light-curtains.webp" alt="Light curtains"/></figure>
					<!-- /wp:image -->
					<!-- wp:group {"className":"mtk-safety-card-body","layout":{"type":"default"}} -->
					<div class="wp-block-group mtk-safety-card-body">
						<!-- wp:heading {"level":3,"className":"mtk-h4 mtk-mb-2"} -->
						<h3 class="wp-block-heading mtk-h4 mtk-mb-2">Light curtains</h3>
						<!-- /wp:heading -->
						<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
						<p class="mtk-small mtk-text-muted">Presence-sensing protection for access to hazardous machinery areas.</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:column -->

				<!-- wp:column {"className":"mtk-safety-card"} -->
				<div class="wp-block-column mtk-safety-card">
					<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image size-full"><img src="{$assets}/safety-relays.webp" alt="Safety relays"/></figure>
					<!-- /wp:image -->
					<!-- wp:group {"className":"mtk-safety-card-body","layout":{"type":"default"}} -->
					<div class="wp-block-group mtk-safety-card-body">
						<!-- wp:heading {"level":3,"className":"mtk-h4 mtk-mb-2"} -->
						<h3 class="wp-block-heading mtk-h4 mtk-mb-2">Safety relays</h3>
						<!-- /wp:heading -->
						<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
						<p class="mtk-small mtk-text-muted">Monitoring of emergency stops, guards and safety devices.</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:column -->

				<!-- wp:column {"className":"mtk-safety-card"} -->
				<div class="wp-block-column mtk-safety-card">
					<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image size-full"><img src="{$assets}/safety-fencing.webp" alt="Safety fencing"/></figure>
					<!-- /wp:image -->
					<!-- wp:group {"className":"mtk-safety-card-body","layout":{"type":"default"}} -->
					<div class="wp-block-group mtk-safety-card-body">
						<!-- wp:heading {"level":3,"className":"mtk-h4 mtk-mb-2"} -->
						<h3 class="wp-block-heading mtk-h4 mtk-mb-2">Safety fencing</h3>
						<!-- /wp:heading -->
						<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
						<p class="mtk-small mtk-text-muted">Physical guarding to separate people from hazardous machinery.</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"photo-gallery","className":"mtk-bg-surface mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-surface mtk-py-16" id="photo-gallery">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">PROJECT DETAILS</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Control systems and operator interfaces</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-body mtk-text-muted mtk-mb-2"} -->
		<p class="mtk-body mtk-text-muted mtk-mb-2">Control-panel hardware and operator interface examples; machine safety systems are shown separately above.</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
		<p class="mtk-small mtk-text-muted">Select a photograph to view it larger. Images illustrate equipment and environments relevant to this service.</p>
		<!-- /wp:paragraph -->

		<!-- wp:columns {"className":"mtk-photo-grid-2"} -->
		<div class="wp-block-columns mtk-photo-grid-2">
{$ctrl_photo_1}
{$ctrl_photo_2}
{$ctrl_photo_3}
{$ctrl_photo_4}
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

{$ctrl_cta}
HTML;

	/* ======================================================================
	 * 3. MAINTENANCE PAGE (Figma Node 11:418)
	 * ====================================================================== */
	$maint_row_1 = maatek_block_service_feature_row(
		array(
			'id'            => 'preventative-maintenance',
			'overline'      => '01',
			'title'         => 'Preventative maintenance',
			'paragraphs'    => array(
				'Planned inspection and servicing of electrical panels, field devices, motor controls, cable systems and production equipment. Maintenance activities can be aligned with plant shutdown windows to minimise disruption.',
			),
			'bullets'       => array(
				'Scheduled inspection of switchboards and controls',
				'Checks on sensors, actuators and safety circuits',
				'Maintenance aligned with planned production windows',
			),
			'img_url'       => "{$assets}/3d0b4b609ee8034b.webp",
			'img_alt'       => 'Guarding and overhead conveyor',
			'image_on_left' => true,
		)
	);

	$maint_row_2 = maatek_block_service_feature_row(
		array(
			'id'            => 'breakdown-diagnosis-and-repair',
			'overline'      => '02',
			'title'         => 'Breakdown diagnosis and repair',
			'paragraphs'    => array(
				'Systematic troubleshooting across power, controls, sensors and machine interfaces to identify root causes rather than repeatedly treating the symptoms.',
			),
			'bullets'       => array(
				'Electrical and PLC fault finding',
				'Replacement and testing of failed components',
				'Practical recovery steps to restore safe operation',
			),
			'img_url'       => "{$assets}/79f90e9ee5e35ee1.webp",
			'img_alt'       => 'Industrial plant services',
			'image_on_left' => false,
		)
	);

	$maint_row_3 = maatek_block_service_feature_row(
		array(
			'id'            => 'plant-upgrades-and-reliability',
			'overline'      => '03',
			'title'         => 'Plant upgrades and reliability',
			'paragraphs'    => array(
				'Targeted replacement of ageing components, improvements to control arrangements and practical recommendations that support safer, more maintainable equipment.',
			),
			'bullets'       => array(
				'Upgrades for obsolete electrical and control hardware',
				'Panel tidying, labelling and documentation updates',
				'Reliability improvements informed by site history',
			),
			'img_url'       => "{$assets}/plant-reliability-control-upgrade.webp",
			'img_alt'       => 'Upgraded industrial PLC control cabinet with organised wiring and operator HMI',
			'image_on_left' => true,
		)
	);

	$maint_photo_1 = maatek_block_photo_card(
		"{$assets}/3d0b4b609ee8034b.webp",
		'Conveyor inspection and servicing',
		'Conveyor guards, supports and overhead equipment require scheduled inspection for wear, damage and safe access.'
	);
	$maint_photo_2 = maatek_block_photo_card(
		"{$assets}/2dab8baeb0eab608.webp",
		'Production machinery service access',
		'Access to production equipment and connected services is considered during maintenance planning.'
	);
	$maint_photo_3 = maatek_block_photo_card(
		"{$assets}/87df35ae4d458f2c.webp",
		'Machine guarding and transfer points',
		'Guarding and transfer interfaces are checked during planned shutdowns and fault investigations.'
	);
	$maint_photo_4 = maatek_block_photo_card(
		"{$assets}/7fe2c5da26eeff88.webp",
		'Electrical cabling and plant services',
		'Cable routes, containment and field connections are inspected when tracing equipment faults or planning upgrades.'
	);

	$maint_content = <<<HTML
<!-- wp:cover {"url":"{$assets}/0836812bfcbdcff3.webp","dimRatio":70,"overlayColor":"heading-dark","isUserOverlayColor":true,"minHeight":470,"minHeightUnit":"px","tagName":"section","className":"mtk-hero-service","layout":{"type":"default"}} -->
<section class="wp-block-cover mtk-hero-service" style="min-height:470px"><span aria-hidden="true" class="wp-block-cover__background has-heading-dark-background-color has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{$assets}/0836812bfcbdcff3.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-hero-kicker"} -->
		<p class="mtk-hero-kicker">MAATEK ELECTRICAL / INDUSTRIAL MAINTENANCE</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1,"className":"mtk-hero-service-title mtk-max-w-760"} -->
		<h1 class="wp-block-heading mtk-hero-service-title mtk-max-w-760">Keeping critical equipment reliable</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-hero-service-lead"} -->
		<p class="mtk-hero-service-lead">Reliable industrial operations depend on more than responding when machinery stops. Planned inspections, disciplined fault-finding and practical improvements help protect production, improve equipment availability and reduce unexpected downtime.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"className":"mtk-btn-group"} -->
		<div class="wp-block-buttons mtk-btn-group">
			<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
			<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$contact}">Discuss your project <span class="mtk-arrow">→</span></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div></section>
<!-- /wp:cover -->

<!-- wp:group {"tagName":"nav","className":"mtk-subnav-bar","layout":{"type":"default"}} -->
<nav class="wp-block-group mtk-subnav-bar">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline"} -->
		<p class="mtk-overline">EXPLORE THIS PAGE</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-subnav-links"} -->
		<p class="mtk-subnav-links"><a href="#preventative-maintenance">Preventative maintenance</a> <span class="mtk-subnav-sep">·</span> <a href="#breakdown-diagnosis-and-repair">Breakdown diagnosis and repair</a> <span class="mtk-subnav-sep">·</span> <a href="#plant-upgrades-and-reliability">Plant upgrades and reliability</a> <span class="mtk-subnav-sep">·</span> <a href="#photo-gallery">Project photographs</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</nav>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-approach-section","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-approach-section">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:columns {"className":"mtk-approach-grid"} -->
		<div class="wp-block-columns mtk-approach-grid">
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
				<p class="mtk-overline mtk-mb-2">THE MAATEK APPROACH</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"className":"mtk-h2"} -->
				<h2 class="wp-block-heading mtk-h2">Built around your operation.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-pt-4"} -->
				<p class="mtk-body mtk-text-muted-dark mtk-pt-4">Maintenance is most effective when electrical systems, controls and mechanical interfaces are considered together. From assessing a recurring fault to coordinating work around production requirements, the objective is to return equipment to dependable service with clear documentation and a practical plan for ongoing reliability.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-bg-section mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-section mtk-py-16">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">SPECIALIST SERVICES</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-8"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-8">What we deliver</h2>
		<!-- /wp:heading -->

		<!-- wp:group {"className":"mtk-service-stack","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-service-stack">
{$maint_row_1}
{$maint_row_2}
{$maint_row_3}
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"photo-gallery","className":"mtk-bg-surface mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-surface mtk-py-16" id="photo-gallery">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">PROJECT DETAILS</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Maintenance and reliability</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-body mtk-text-muted mtk-mb-2"} -->
		<p class="mtk-body mtk-text-muted mtk-mb-2">Examples of equipment interfaces, guarding and electrical services considered during industrial maintenance.</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
		<p class="mtk-small mtk-text-muted">Select a photograph to view it larger. Images illustrate equipment and environments relevant to this service.</p>
		<!-- /wp:paragraph -->

		<!-- wp:columns {"className":"mtk-photo-grid-2"} -->
		<div class="wp-block-columns mtk-photo-grid-2">
{$maint_photo_1}
{$maint_photo_2}
{$maint_photo_3}
{$maint_photo_4}
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

{$ctrl_cta}
HTML;

	/* ======================================================================
	 * 4. FABRICATION PAGE (Figma Node 11:517)
	 * ====================================================================== */
	$fab_row_1 = maatek_block_service_feature_row(
		array(
			'id'            => 'process-equipment-and-assemblies',
			'overline'      => '01',
			'title'         => 'Process equipment and assemblies',
			'paragraphs'    => array(
				'Fabricated supports, frames and plant assemblies designed around equipment access, loads, service connections and the constraints of an operating factory.',
			),
			'bullets'       => array(
				'Equipment frames, skids and support structures',
				'Practical access for operation, cleaning and servicing',
				'Fabrication coordinated with electrical and control fit-out',
			),
			'img_url'       => "{$assets}/7dafc6cd30405e3d.webp",
			'img_alt'       => 'Conveyor transfer line',
			'image_on_left' => true,
		)
	);

	$fab_row_2 = maatek_block_service_feature_row(
		array(
			'id'            => 'conveyors-and-material-handling',
			'overline'      => '02',
			'title'         => 'Conveyors and material handling',
			'paragraphs'    => array(
				'Integration of transfer equipment, guarding, structural supports and drive arrangements into existing production and material-handling processes.',
			),
			'bullets'       => array(
				'Conveyor frames, transfers and support steelwork',
				'Guarding, sensor brackets and drive mounting interfaces',
				'Layout adjustments to suit existing line geometry',
			),
			'img_url'       => "{$assets}/c926c266cc45c31d.webp",
			'img_alt'       => 'Industrial material handling and conveyor area',
			'image_on_left' => false,
		)
	);

	$fab_row_3 = maatek_block_service_feature_row(
		array(
			'id'            => 'pipework-and-plant-integration',
			'overline'      => '03',
			'title'         => 'Pipework and plant integration',
			'paragraphs'    => array(
				'Practical installation interfaces for process vessels, piping, valves and associated services, with attention to maintainability and plant layout.',
			),
			'bullets'       => array(
				'Process pipework supports and equipment connections',
				'Integration of valves, instrumentation and field services',
				'Clean routing for long-term inspection and maintenance',
			),
			'img_url'       => "{$assets}/57fb1be7e2ab0989.webp",
			'img_alt'       => 'Industrial equipment interfaces',
			'image_on_left' => true,
		)
	);

	$fab_photo_1 = maatek_block_photo_card(
		"{$assets}/7dafc6cd30405e3d.webp",
		'Conveyor transfer assembly',
		'Transfer sections and supporting frames are arranged to suit product flow and available plant space.'
	);
	$fab_photo_2 = maatek_block_photo_card(
		"{$assets}/962ec8be994aec05.webp",
		'Process valves and mechanical interfaces',
		'Valves, fittings and equipment interfaces are considered when assembling process systems.'
	);
	$fab_photo_3 = maatek_block_photo_card(
		"{$assets}/ba4e572aaed635a6.webp",
		'Stainless process pipework',
		'Fabricated pipework connections require careful alignment, support and access for future servicing.'
	);
	$fab_photo_4 = maatek_block_photo_card(
		"{$assets}/451ee99b28f4b398.webp",
		'Belt conveyor installation',
		'Conveyor frames, belt runs and transfer points must be aligned with adjacent production equipment.'
	);
	$fab_photo_5 = maatek_block_photo_card(
		"{$assets}/b1e87f7b08cc9ffc.webp",
		'Conveyor transfer integration',
		'Material-handling equipment is integrated with surrounding plant structures and access requirements.'
	);

	$fab_content = <<<HTML
<!-- wp:cover {"url":"{$assets}/8c0777edd3ad9583.webp","dimRatio":70,"overlayColor":"heading-dark","isUserOverlayColor":true,"minHeight":470,"minHeightUnit":"px","tagName":"section","className":"mtk-hero-service","layout":{"type":"default"}} -->
<section class="wp-block-cover mtk-hero-service" style="min-height:470px"><span aria-hidden="true" class="wp-block-cover__background has-heading-dark-background-color has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{$assets}/8c0777edd3ad9583.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-hero-kicker"} -->
		<p class="mtk-hero-kicker">MAATEK ELECTRICAL / INDUSTRIAL FABRICATION</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1,"className":"mtk-hero-service-title mtk-max-w-760"} -->
		<h1 class="wp-block-heading mtk-hero-service-title mtk-max-w-760">Practical fabrication for demanding production environments</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-hero-service-lead"} -->
		<p class="mtk-hero-service-lead">Industrial equipment needs robust supports, accurate interfaces and careful integration with the surrounding plant. Fabrication is about creating practical, serviceable solutions that fit real operating conditions.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"className":"mtk-btn-group"} -->
		<div class="wp-block-buttons mtk-btn-group">
			<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
			<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$contact}">Discuss your project <span class="mtk-arrow">→</span></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div></section>
<!-- /wp:cover -->

<!-- wp:group {"tagName":"nav","className":"mtk-subnav-bar","layout":{"type":"default"}} -->
<nav class="wp-block-group mtk-subnav-bar">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline"} -->
		<p class="mtk-overline">EXPLORE THIS PAGE</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-subnav-links"} -->
		<p class="mtk-subnav-links"><a href="#process-equipment-and-assemblies">Process equipment and assemblies</a> <span class="mtk-subnav-sep">·</span> <a href="#conveyors-and-material-handling">Conveyors and material handling</a> <span class="mtk-subnav-sep">·</span> <a href="#pipework-and-plant-integration">Pipework and plant integration</a> <span class="mtk-subnav-sep">·</span> <a href="#photo-gallery">Project photographs</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</nav>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-approach-section","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-approach-section">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:columns {"className":"mtk-approach-grid"} -->
		<div class="wp-block-columns mtk-approach-grid">
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
				<p class="mtk-overline mtk-mb-2">THE MAATEK APPROACH</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"className":"mtk-h2"} -->
				<h2 class="wp-block-heading mtk-h2">Built around your operation.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-pt-4"} -->
				<p class="mtk-body mtk-text-muted-dark mtk-pt-4">Whether the work involves process equipment, access structures, conveyors or supporting infrastructure, successful fabrication begins with understanding how the finished assembly will be installed, operated and maintained. Coordination between fabrication, electrical and automation disciplines helps reduce installation conflicts and commissioning delays.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-bg-section mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-section mtk-py-16">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">SPECIALIST SERVICES</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-8"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-8">What we deliver</h2>
		<!-- /wp:heading -->

		<!-- wp:group {"className":"mtk-service-stack","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-service-stack">
{$fab_row_1}
{$fab_row_2}
{$fab_row_3}
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"photo-gallery","className":"mtk-bg-surface mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-surface mtk-py-16" id="photo-gallery">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">PROJECT DETAILS</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Fabrication and plant assemblies</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-body mtk-text-muted mtk-mb-2"} -->
		<p class="mtk-body mtk-text-muted mtk-mb-2">Relevant process pipework, conveyor and mechanical equipment interfaces from the project image library.</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
		<p class="mtk-small mtk-text-muted">Select a photograph to view it larger. Images illustrate equipment and environments relevant to this service.</p>
		<!-- /wp:paragraph -->

		<!-- wp:columns {"className":"mtk-photo-grid-2"} -->
		<div class="wp-block-columns mtk-photo-grid-2">
{$fab_photo_1}
{$fab_photo_2}
{$fab_photo_3}
{$fab_photo_4}
{$fab_photo_5}
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

{$ctrl_cta}
HTML;

	/* ======================================================================
	 * 5. INSTALLATIONS PAGE (Figma Node 11:619)
	 * ====================================================================== */
	$inst_row_1 = maatek_block_service_feature_row(
		array(
			'id'            => 'equipment-positioning-and-handling',
			'overline'      => '01',
			'title'         => 'Equipment positioning and handling',
			'paragraphs'    => array(
				'Planning lifting routes, access requirements, equipment movement and final placement to suit site constraints and operational needs.',
			),
			'bullets'       => array(
				'Site access, craneage and rigging coordination',
				'Positioning of machinery, vessels and conveyors',
				'Alignment with existing structures and production lines',
			),
			'img_url'       => "{$assets}/dff0d1e1259af8a2.webp",
			'img_alt'       => 'Crane-assisted handling',
			'image_on_left' => true,
		)
	);

	$inst_row_2 = maatek_block_service_feature_row(
		array(
			'id'            => 'electrical-and-mechanical-integration',
			'overline'      => '02',
			'title'         => 'Electrical and mechanical integration',
			'paragraphs'    => array(
				'Coordinating cable routes, control panels, services, machine interfaces and physical installation so each system works as part of the complete plant.',
			),
			'bullets'       => array(
				'Power, control cabling and containment installation',
				'Field terminations, sensors and operator stations',
				'Mechanical and electrical trade coordination on site',
			),
			'img_url'       => "{$assets}/f9abb29a488ca165.webp",
			'img_alt'       => 'Overhead material handling',
			'image_on_left' => false,
		)
	);

	$inst_row_3 = maatek_block_service_feature_row(
		array(
			'id'            => 'commissioning-and-handover',
			'overline'      => '03',
			'title'         => 'Commissioning and handover',
			'paragraphs'    => array(
				'Structured checks of installation readiness, field connections, equipment interfaces and operational requirements before handover to the site team.',
			),
			'bullets'       => array(
				'Pre-commissioning inspection and I/O verification',
				'Functional testing of sequences, interlocks and safety',
				'Handover support and practical site documentation',
			),
			'img_url'       => "{$assets}/95ee88c5d4cb148b.webp",
			'img_alt'       => 'Conveyor lifting equipment',
			'image_on_left' => true,
		)
	);

	$inst_photo_1 = maatek_block_photo_card(
		"{$assets}/55e5d9a70651bf11.webp",
		'Crane and equipment handling',
		'Site lifting arrangements and safe access are coordinated before equipment is moved into position.'
	);
	$inst_photo_2 = maatek_block_photo_card(
		"{$assets}/20d8589f5bff2485.webp",
		'Indoor vessel installation',
		'Positioning large process vessels within an existing factory, coordinating access, lifting and surrounding services.'
	);
	$inst_photo_3 = maatek_block_photo_card(
		"{$assets}/dff0d1e1259af8a2.webp",
		'Crane-assisted machinery placement',
		'Specialist lifting equipment used to safely position industrial machinery during plant installation.'
	);
	$inst_photo_4 = maatek_block_photo_card(
		"{$assets}/f9abb29a488ca165.webp",
		'Overhead equipment handling',
		'Installation planning around overhead structures, access constraints and heavy plant movement.'
	);
	$inst_photo_5 = maatek_block_photo_card(
		"{$assets}/95ee88c5d4cb148b.webp",
		'Conveyor installation and handling',
		'Lifting and alignment of conveyor equipment for integration into existing production lines.'
	);
	$inst_photo_6 = maatek_block_photo_card(
		"{$assets}/d945aaa6185add20.webp",
		'Process vessel handling',
		'Outdoor movement and staging of process equipment before final installation and commissioning.'
	);

	$inst_content = <<<HTML
<!-- wp:cover {"url":"{$assets}/55e5d9a70651bf11.webp","dimRatio":70,"overlayColor":"heading-dark","isUserOverlayColor":true,"minHeight":470,"minHeightUnit":"px","tagName":"section","className":"mtk-hero-service","layout":{"type":"default"}} -->
<section class="wp-block-cover mtk-hero-service" style="min-height:470px"><span aria-hidden="true" class="wp-block-cover__background has-heading-dark-background-color has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{$assets}/55e5d9a70651bf11.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-hero-kicker"} -->
		<p class="mtk-hero-kicker">MAATEK ELECTRICAL / INDUSTRIAL INSTALLATIONS</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1,"className":"mtk-hero-service-title mtk-max-w-760"} -->
		<h1 class="wp-block-heading mtk-hero-service-title mtk-max-w-760">From equipment delivery to a coordinated plant installation</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-hero-service-lead"} -->
		<p class="mtk-hero-service-lead">Installing equipment in a live industrial environment requires planning, precise positioning and coordination across trades. Every stage matters, from access and lifting arrangements to final connections and commissioning.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"className":"mtk-btn-group"} -->
		<div class="wp-block-buttons mtk-btn-group">
			<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
			<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$contact}">Discuss your project <span class="mtk-arrow">→</span></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div></section>
<!-- /wp:cover -->

<!-- wp:group {"tagName":"nav","className":"mtk-subnav-bar","layout":{"type":"default"}} -->
<nav class="wp-block-group mtk-subnav-bar">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline"} -->
		<p class="mtk-overline">EXPLORE THIS PAGE</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-subnav-links"} -->
		<p class="mtk-subnav-links"><a href="#equipment-positioning-and-handling">Equipment positioning and handling</a> <span class="mtk-subnav-sep">·</span> <a href="#electrical-and-mechanical-integration">Electrical and mechanical integration</a> <span class="mtk-subnav-sep">·</span> <a href="#commissioning-and-handover">Commissioning and handover</a> <span class="mtk-subnav-sep">·</span> <a href="#photo-gallery">Project photographs</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</nav>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-approach-section","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-approach-section">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:columns {"className":"mtk-approach-grid"} -->
		<div class="wp-block-columns mtk-approach-grid">
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
				<p class="mtk-overline mtk-mb-2">THE MAATEK APPROACH</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"className":"mtk-h2"} -->
				<h2 class="wp-block-heading mtk-h2">Built around your operation.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-pt-4"} -->
				<p class="mtk-body mtk-text-muted-dark mtk-pt-4">A well-managed installation brings together site preparation, mechanical positioning, electrical infrastructure and control-system integration. The emphasis is on safe execution, clear sequencing and ensuring the finished equipment is accessible for future operation and maintenance.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-bg-section mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-section mtk-py-16">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">SPECIALIST SERVICES</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-8"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-8">What we deliver</h2>
		<!-- /wp:heading -->

		<!-- wp:group {"className":"mtk-service-stack","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-service-stack">
{$inst_row_1}
{$inst_row_2}
{$inst_row_3}
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"photo-gallery","className":"mtk-bg-surface mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-surface mtk-py-16" id="photo-gallery">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">PROJECT DETAILS</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Equipment and installation details</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-body mtk-text-muted mtk-mb-2"} -->
		<p class="mtk-body mtk-text-muted mtk-mb-2">Selected Maatek project-library photographs of industrial machinery positioning, vessel handling, crane-assisted installation and conveyor integration.</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
		<p class="mtk-small mtk-text-muted">Select a photograph to view it larger. Images illustrate equipment and environments relevant to this service.</p>
		<!-- /wp:paragraph -->

		<!-- wp:columns {"className":"mtk-photo-grid-2"} -->
		<div class="wp-block-columns mtk-photo-grid-2">
{$inst_photo_1}
{$inst_photo_2}
{$inst_photo_3}
{$inst_photo_4}
{$inst_photo_5}
{$inst_photo_6}
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

{$ctrl_cta}
HTML;

	/* ======================================================================
	 * 6. PROJECTS PAGE (Figma Node 11:725)
	 * ====================================================================== */
	$proj_card_1 = maatek_block_photo_card(
		"{$assets}/55e5d9a70651bf11.webp",
		'Crane and equipment handling',
		'Site lifting arrangements and safe access are coordinated before equipment is moved into position.'
	);
	$proj_card_2 = maatek_block_photo_card(
		"{$assets}/dff0d1e1259af8a2.webp",
		'Crane-assisted handling',
		'Mobile lifting equipment supports the careful placement of machinery around existing structures.'
	);
	$proj_card_3 = maatek_block_photo_card(
		"{$assets}/f9abb29a488ca165.webp",
		'Overhead material handling',
		'Lifting plans account for overhead services, structural clearances and access to installation points.'
	);
	$proj_card_4 = maatek_block_photo_card(
		"{$assets}/95ee88c5d4cb148b.webp",
		'Conveyor lifting equipment',
		'Conveyor assemblies are positioned and aligned for integration with production equipment.'
	);
	$proj_card_5 = maatek_block_photo_card(
		"{$assets}/d945aaa6185add20.webp",
		'Vessel handling outdoors',
		'Large process vessels are staged and moved with attention to lifting points and site access.'
	);
	$proj_card_6 = maatek_block_photo_card(
		"{$assets}/20d8589f5bff2485.webp",
		'Vessel handling inside a factory',
		'Process vessels are manoeuvred into existing factory spaces where access and clearances are limited.'
	);

	$projects_content = <<<HTML
<!-- wp:group {"tagName":"section","className":"mtk-hero-dark-solid","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-hero-dark-solid">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-hero-kicker"} -->
		<p class="mtk-hero-kicker">MAATEK ELECTRICAL / SELECTED WORK</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":1,"className":"mtk-hero-service-title mtk-max-w-760"} -->
		<h1 class="wp-block-heading mtk-hero-service-title mtk-max-w-760">Built for the demands of industry.</h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"mtk-hero-service-lead"} -->
		<p class="mtk-hero-service-lead">Explore selected industrial equipment and installation environments. These images illustrate the equipment handling, lifting and installation environments relevant to industrial projects.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-bg-surface mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-surface mtk-py-16">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">OUR WORK</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Industrial installation environments.</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"mtk-body mtk-text-muted mtk-max-w-680 mtk-mb-10"} -->
		<p class="mtk-body mtk-text-muted mtk-max-w-680 mtk-mb-10">From equipment handling to machinery positioning and structural installation, the gallery focuses on site access, lifting arrangements and equipment positioning.</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":3,"className":"mtk-h3-sm mtk-mb-4"} -->
		<h3 class="wp-block-heading mtk-h3-sm mtk-mb-4">Installations</h3>
		<!-- /wp:heading -->

		<!-- wp:columns {"className":"mtk-photo-grid-3"} -->
		<div class="wp-block-columns mtk-photo-grid-3">
{$proj_card_1}
{$proj_card_2}
{$proj_card_3}
{$proj_card_4}
{$proj_card_5}
{$proj_card_6}
		</div>
		<!-- /wp:columns -->

		<!-- wp:group {"className":"mtk-projects-banner","layout":{"type":"default"}} -->
		<div class="wp-block-group mtk-projects-banner">
			<!-- wp:paragraph {"className":"mtk-body mtk-font-semibold mtk-text-white"} -->
			<p class="mtk-body mtk-font-semibold mtk-text-white">Have an installation project in mind? Talk to Marko about your equipment, site access and timeframe.</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons {"className":"mtk-btn-group"} -->
			<div class="wp-block-buttons mtk-btn-group">
				<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
				<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$contact}">Contact Marko <span class="mtk-arrow">→</span></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
HTML;

	/* ======================================================================
	 * 7. CONTACT PAGE (Figma Node 11:785)
	 * ====================================================================== */
	$contact_content = <<<HTML
<!-- wp:group {"tagName":"section","className":"mtk-hero-dark-solid","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-hero-dark-solid">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-hero-kicker"} -->
		<p class="mtk-hero-kicker">MAATEK ELECTRICAL / CONTACT</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":1,"className":"mtk-hero-service-title mtk-max-w-760"} -->
		<h1 class="wp-block-heading mtk-hero-service-title mtk-max-w-760">Let’s talk about your next project.</h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"mtk-hero-service-lead"} -->
		<p class="mtk-hero-service-lead">From industrial maintenance and machine upgrades to switchboards, automation and turnkey installations, start with a conversation about your site and what you need to achieve.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"mtk-bg-section mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-section mtk-py-16">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:columns {"className":"mtk-contact-grid"} -->
		<div class="wp-block-columns mtk-contact-grid">
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
				<p class="mtk-overline mtk-mb-2">GET IN TOUCH</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"className":"mtk-h2 mtk-mb-6"} -->
				<h2 class="wp-block-heading mtk-h2 mtk-mb-6">Contact Maatek Electrical.</h2>
				<!-- /wp:heading -->

				<!-- wp:group {"className":"mtk-contact-card","layout":{"type":"default"}} -->
				<div class="wp-block-group mtk-contact-card">
					<!-- wp:heading {"level":3,"className":"mtk-h3-sm mtk-mb-1"} -->
					<h3 class="wp-block-heading mtk-h3-sm mtk-mb-1">Marko Markovic</h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"className":"mtk-small mtk-text-muted mtk-mb-4"} -->
					<p class="mtk-small mtk-text-muted mtk-mb-4">Maatek Electrical</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"mtk-lead mtk-font-bold mtk-mb-2"} -->
					<p class="mtk-lead mtk-font-bold mtk-mb-2"><a class="mtk-text-blue" href="tel:+61414421651">0414 421 651</a></p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"mtk-body mtk-font-semibold"} -->
					<p class="mtk-body mtk-font-semibold"><a class="mtk-text-blue" href="mailto:marko@maatekelectrical.com.au">marko@maatekelectrical.com.au</a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

				<!-- wp:buttons {"className":"mtk-contact-actions"} -->
				<div class="wp-block-buttons mtk-contact-actions">
					<!-- wp:button {"className":"mtk-btn mtk-btn-primary mtk-btn-block"} -->
					<div class="wp-block-button mtk-btn mtk-btn-primary mtk-btn-block"><a class="wp-block-button__link wp-element-button" href="tel:+61414421651">Call 0414 421 651</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"mtk-btn mtk-btn-secondary--blue-border mtk-btn-block"} -->
					<div class="wp-block-button mtk-btn mtk-btn-secondary--blue-border mtk-btn-block"><a class="wp-block-button__link wp-element-button" href="mailto:marko@maatekelectrical.com.au">Email Marko</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"mtk-pt-8"} -->
			<div class="wp-block-column mtk-pt-8">
				<!-- wp:heading {"level":3,"className":"mtk-h4 mtk-mb-2"} -->
				<h3 class="wp-block-heading mtk-h4 mtk-mb-2">Tell us about the challenge.</h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-mb-4"} -->
				<p class="mtk-body mtk-text-muted-dark mtk-mb-4">Share a few details when you call or email:</p>
				<!-- /wp:paragraph -->

				<!-- wp:group {"className":"mtk-check-list mtk-mb-6","layout":{"type":"default"}} -->
				<div class="wp-block-group mtk-check-list mtk-mb-6">
					<!-- wp:paragraph {"className":"mtk-check-item"} -->
					<p class="mtk-check-item">Equipment and work required</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"mtk-check-item"} -->
					<p class="mtk-check-item">Site location and operating environment</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"mtk-check-item"} -->
					<p class="mtk-check-item">Project timeframe</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"mtk-check-item"} -->
					<p class="mtk-check-item">Relevant drawings or specifications</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

				<!-- wp:heading {"level":3,"className":"mtk-h4 mtk-mb-2"} -->
				<h3 class="wp-block-heading mtk-h4 mtk-mb-2">Next steps</h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"mtk-body mtk-text-muted-dark mtk-max-w-520"} -->
				<p class="mtk-body mtk-text-muted-dark mtk-max-w-520">We can then discuss the appropriate next steps.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
HTML;

	return array(
		'home'               => array(
			'title'   => 'Home',
			'content' => $home_content,
		),
		'control-automation' => array(
			'title'   => 'Control & Automation',
			'content' => $ctrl_content,
		),
		'maintenance'        => array(
			'title'   => 'Maintenance',
			'content' => $maint_content,
		),
		'fabrication'        => array(
			'title'   => 'Fabrication',
			'content' => $fab_content,
		),
		'installations'      => array(
			'title'   => 'Installations',
			'content' => $inst_content,
		),
		'projects'           => array(
			'title'   => 'Projects',
			'content' => $projects_content,
		),
		'contact'            => array(
			'title'   => 'Contact',
			'content' => $contact_content,
		),
	);
}
