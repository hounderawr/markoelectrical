<?php
/**
 * Maatek Block Theme functions and definitions.
 *
 * @package Maatek
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAATEK_THEME_VERSION', '2.1' );

require_once get_template_directory() . '/inc/page-blocks.php';

/**
 * Configure theme supports and Block Editor styles.
 */
function maatek_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 60,
			'width'       => 170,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_editor_style(
		array(
			'https://use.typekit.net/ash7fiq.css',
			'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
			'style.css',
		)
	);
}
add_action( 'after_setup_theme', 'maatek_setup' );

/**
 * Build native core/navigation-link block markup for Header and Footer navigation blocks.
 *
 * @param array<string, string> $items Map of page slug => menu label.
 * @return string Gutenberg navigation-link blocks.
 */
function maatek_navigation_links_markup( array $items ) {
	$links = array();

	foreach ( $items as $slug => $label ) {
		$url   = function_exists( 'maatek_get_page_url' ) ? maatek_get_page_url( $slug ) : ( ( 'home' === $slug ) ? trailingslashit( home_url( '/' ) ) : trailingslashit( home_url( '/' . $slug ) ) );
		$attrs = array(
			'label'          => $label,
			'url'            => $url,
			'kind'           => 'custom',
			'isTopLevelLink' => true,
		);

		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page instanceof WP_Post ) {
			$attrs['id']   = (int) $page->ID;
			$attrs['kind'] = 'post-type';
			$attrs['type'] = 'page';
		}

		$links[] = '<!-- wp:navigation-link ' . wp_json_encode( $attrs ) . ' /-->';
	}

	return implode( "\n\t\t", $links );
}

/**
 * Enqueue Adobe Typekit, Inter font, custom utility/component stylesheet, and interactive JS.
 */
function maatek_enqueue_assets() {
	$style_path = get_template_directory() . '/style.css';
	$style_ver  = file_exists( $style_path ) ? (string) filemtime( $style_path ) : MAATEK_THEME_VERSION;

	// Adobe Typekit (Handel Gothic D).
	wp_enqueue_style(
		'maatek-typekit',
		'https://use.typekit.net/ash7fiq.css',
		array(),
		null
	);

	// Google Fonts (Inter body & UI).
	wp_enqueue_style(
		'maatek-inter-font',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	// Maatek custom utility and component stylesheet.
	wp_enqueue_style(
		'maatek-style',
		get_stylesheet_uri(),
		array( 'maatek-typekit', 'maatek-inter-font' ),
		$style_ver
	);

	// Front-end interactions (Active nav indicator, Mobile menu drawer, Photo lightbox).
	if ( ! is_admin() ) {
		$js_path = get_template_directory() . '/assets/js/maatek.js';
		$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : MAATEK_THEME_VERSION;

		wp_enqueue_script(
			'maatek-script',
			get_template_directory_uri() . '/assets/js/maatek.js',
			array(),
			$js_ver,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'maatek_enqueue_assets' );
// Note: the Block Editor receives style.css + fonts via add_editor_style() (scoped to the canvas),
// so the unscoped front-end stylesheet is NOT enqueued on enqueue_block_assets (it would leak into wp-admin).

/**
 * Output Typekit preconnect and theme favicons in <head>.
 */
function maatek_head_links() {
	$assets_uri = esc_url( untrailingslashit( get_template_directory_uri() ) . '/assets' );
	echo '<link rel="preconnect" href="https://use.typekit.net" crossorigin>' . "\n";
	echo '<link rel="preconnect" href="https://p.typekit.net" crossorigin>' . "\n";
	echo '<link rel="icon" href="' . $assets_uri . '/favicon.svg" type="image/svg+xml">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . $assets_uri . '/apple-touch-icon.png">' . "\n";
}
add_action( 'wp_head', 'maatek_head_links', 1 );

/**
 * Check whether the global Project Photo Lightbox is enabled.
 *
 * @return bool True if enabled (default), false if disabled.
 */
function maatek_is_lightbox_enabled() {
	return '0' !== (string) get_option( 'maatek_lightbox_enabled', '1' );
}

/**
 * Add body class when the global Lightbox is disabled so CSS hides zoom badges and cursors.
 *
 * @param string[] $classes Existing body classes.
 * @return string[] Modified body classes.
 */
function maatek_lightbox_body_class( $classes ) {
	if ( ! maatek_is_lightbox_enabled() ) {
		$classes[] = 'mtk-lightbox-disabled';
	}
	return $classes;
}
add_filter( 'body_class', 'maatek_lightbox_body_class' );

/**
 * Render the accessible Lightbox <dialog> in wp_footer so parts/footer.html stays 100% visual blocks.
 */
function maatek_render_lightbox_dialog() {
	if ( is_admin() || ! maatek_is_lightbox_enabled() ) {
		return;
	}
	?>
	<dialog id="mtk-lightbox" class="mtk-lightbox" aria-label="Project photo viewer">
		<div class="mtk-lightbox-inner">
			<div class="mtk-lightbox-topbar">
				<div class="mtk-lightbox-meta">
					<span>PROJECT PHOTOGRAPH</span>
					<span class="mtk-lightbox-counter" aria-live="polite"></span>
				</div>
				<div class="mtk-lightbox-controls">
					<button type="button" class="mtk-lightbox-nav mtk-lightbox-prev" aria-label="Previous photograph" title="Previous photograph (←)">&#8592;</button>
					<button type="button" class="mtk-lightbox-nav mtk-lightbox-next" aria-label="Next photograph" title="Next photograph (→)">&#8594;</button>
					<button type="button" class="mtk-lightbox-close" aria-label="Close photo viewer" title="Close (Esc)">&times;</button>
				</div>
			</div>
			<div class="mtk-lightbox-stage">
				<img class="mtk-lightbox-img" src="" alt="" />
			</div>
			<div class="mtk-lightbox-caption">
				<p class="mtk-lightbox-title"></p>
				<p class="mtk-lightbox-desc"></p>
			</div>
		</div>
	</dialog>
	<?php
}
add_action( 'wp_footer', 'maatek_render_lightbox_dialog' );

/**
 * Replace {{MAATEK_ASSETS}} and {{MAATEK_HOME}} placeholders in rendered blocks (Header/Footer/Patterns).
 *
 * @param string $block_content Rendered block HTML.
 * @return string Filtered block HTML.
 */
function maatek_replace_block_placeholders( $block_content ) {
	if ( false === strpos( $block_content, '{{MAATEK_' ) ) {
		return $block_content;
	}

	$replacements = array(
		'{{MAATEK_ASSETS}}' => esc_url( untrailingslashit( get_template_directory_uri() ) . '/assets' ),
		'{{MAATEK_HOME}}'   => esc_url( trailingslashit( home_url( '/' ) ) ),
	);

	return strtr( $block_content, $replacements );
}
add_filter( 'render_block', 'maatek_replace_block_placeholders' );

/**
 * Supply smaller theme-owned image candidates without changing saved editor blocks.
 * WordPress media-library images keep their own srcset and loading decisions.
 *
 * @param string $content Rendered block HTML.
 * @param array  $block Parsed block.
 * @return string
 */
function maatek_responsive_theme_images( $content, $block ) {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! is_string( $content ) || empty( $block['blockName'] ) || ! in_array( $block['blockName'], array( 'core/image', 'core/cover' ), true ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$assets_url = untrailingslashit( get_template_directory_uri() ) . '/assets/';
	$assets_path = (string) wp_parse_url( $assets_url, PHP_URL_PATH );
	$assets_host = strtolower( (string) wp_parse_url( $assets_url, PHP_URL_HOST ) );
	$widths = array(
		'home-hero-process-tanks.webp'            => 1806,
		'9241893b534162b6.webp'                  => 1806,
		'home-control-cabinet-enhanced.jpg'       => 1218,
		'home-robot-maintenance-enhanced.jpg'     => 1448,
		'home-fabrication-workshop-enhanced.webp' => 1774,
		'factory-equipment-installation.webp'     => 1536,
		'f00e6bf8ff0c2934.webp'                  => 1536,
	);
	$processor = new WP_HTML_Tag_Processor( $content );
	while ( $processor->next_tag( 'img' ) ) {
		$src = $processor->get_attribute( 'src' );
		if ( ! is_string( $src ) || '' === $src ) {
			continue;
		}
		$path = (string) wp_parse_url( $src, PHP_URL_PATH );
		$host = wp_parse_url( $src, PHP_URL_HOST );
		if ( ( is_string( $host ) && strtolower( $host ) !== $assets_host ) || 0 !== strpos( $path, $assets_path ) ) {
			continue;
		}
		$name = substr( $path, strlen( $assets_path ) );
		if ( 'core/image' === $block['blockName'] && ( isset( $widths[ $name ] ) || in_array( $name, array( 'crane-equipment-handling.webp', '55e5d9a70651bf11.webp' ), true ) ) && ! $processor->get_attribute( 'loading' ) ) {
			$processor->set_attribute( 'loading', 'lazy' );
		}
		if ( ! isset( $widths[ $name ] ) || $processor->get_attribute( 'srcset' ) ) {
			continue;
		}
		$parts = pathinfo( $name );
		$first = $parts['filename'] . '-480.' . $parts['extension'];
		$second = $parts['filename'] . '-960.' . $parts['extension'];
		// Fall back to the original image if thumbnail assets are missing.
		$asset_dir = get_template_directory() . '/assets/';
		if ( ! is_file( $asset_dir . $first ) || ! is_file( $asset_dir . $second ) ) {
			continue;
		}
		$processor->set_attribute( 'srcset', $assets_url . $first . ' 480w, ' . $assets_url . $second . ' 960w, ' . $assets_url . $name . ' ' . $widths[ $name ] . 'w' );
		$processor->set_attribute( 'sizes', 'core/cover' === $block['blockName'] ? '100vw' : '(max-width: 960px) calc(100vw - 48px), 624px' );
	}
	return $processor->get_updated_html();
}
add_filter( 'render_block', 'maatek_responsive_theme_images', 15, 2 );

/**
 * Use the larger process-tanks photograph as the home hero, including on pages already saved in the editor.
 *
 * @param string $content Rendered block HTML.
 * @param array  $block   Parsed block.
 * @return string
 */
function maatek_use_home_hero_image( $content, $block ) {
	if ( is_admin() || ! is_string( $content ) || '' === $content || empty( $block['blockName'] ) || 'core/cover' !== $block['blockName'] ) {
		return $content;
	}

	$class_name = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	if ( false === strpos( $class_name, 'mtk-hero-home' ) && false === strpos( $content, 'mtk-hero-home' ) ) {
		return $content;
	}

	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$src       = esc_url( untrailingslashit( get_template_directory_uri() ) . '/assets/home-hero-process-tanks-bigger.jpg' );
	$processor = new WP_HTML_Tag_Processor( $content );
	if ( $processor->next_tag( array( 'tag_name' => 'img', 'class_name' => 'wp-block-cover__image-background' ) ) ) {
		$processor->set_attribute( 'src', $src );
		$processor->remove_attribute( 'srcset' );
		$processor->remove_attribute( 'sizes' );
		return $processor->get_updated_html();
	}

	return $content;
}
add_filter( 'render_block', 'maatek_use_home_hero_image', 16, 2 );

/**
 * Find the theme's destination pages which are not available to site visitors.
 * Saved blocks remain unchanged; links become available when the page is published.
 *
 * @return array<string, WP_Post> Existing unpublished pages indexed by slug.
 */
function maatek_unpublished_destinations() {
	static $pages = null;
	if ( null !== $pages ) {
		return $pages;
	}
	$pages = array();
	foreach ( array( 'control-automation', 'maintenance', 'fabrication', 'installations', 'projects', 'contact' ) as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! ( $page instanceof WP_Post ) ) {
			$matches = get_posts( array( 'name' => $slug, 'post_type' => 'page', 'post_status' => array( 'draft', 'pending', 'private', 'future' ), 'posts_per_page' => 1 ) );
			$page    = ! empty( $matches[0] ) ? $matches[0] : null;
		}
		if ( $page instanceof WP_Post && 'publish' !== $page->post_status ) {
			$pages[ $slug ] = $page;
		}
	}
	return $pages;
}

/**
 * Temporarily hide visitor-facing links to the theme's unpublished destinations.
 * Use the HTML processor rather than altering saved content or suppressing blocks.
 *
 * @param string $content Rendered block HTML.
 * @return string Filtered HTML.
 */
function maatek_hide_unpublished_destination_links( $content ) {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! is_string( $content ) || false === stripos( $content, '<a' ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}
	$pages = maatek_unpublished_destinations();
	if ( ! $pages ) {
		return $content;
	}
	$home_host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	$processor = new WP_HTML_Tag_Processor( $content );
	while ( $processor->next_tag( 'a' ) ) {
		$href = $processor->get_attribute( 'href' );
		if ( ! is_string( $href ) || '' === $href || '#' === $href[0] ) {
			continue;
		}
		$host = wp_parse_url( $href, PHP_URL_HOST );
		if ( is_string( $host ) && strtolower( $host ) !== $home_host ) {
			continue;
		}
		$path  = untrailingslashit( (string) wp_parse_url( $href, PHP_URL_PATH ) );
		$query = array();
		parse_str( (string) wp_parse_url( $href, PHP_URL_QUERY ), $query );
		foreach ( $pages as $slug => $page ) {
			$pretty_path = untrailingslashit( (string) wp_parse_url( home_url( '/' . $slug ), PHP_URL_PATH ) );
			if ( ( '' !== $path && $path === $pretty_path ) || ( isset( $query['page_id'] ) && (string) $page->ID === (string) $query['page_id'] ) || ( isset( $query['pagename'] ) && $slug === $query['pagename'] ) ) {
				$processor->set_attribute( 'hidden', true );
				// Author display rules can override the browser's [hidden] rule.
				$style = $processor->get_attribute( 'style' );
				$processor->set_attribute( 'style', ( is_string( $style ) ? rtrim( $style, '; ' ) . ';' : '' ) . 'display:none!important' );
				break;
			}
		}
	}
	return $processor->get_updated_html();
}
add_filter( 'render_block', 'maatek_hide_unpublished_destination_links', 25 );

/**
 * Drop numbered service eyebrows (01, 02 / PLC PROGRAMMING, 03) from pages already saved in WordPress.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Parsed block.
 * @return string
 */
function maatek_strip_numbered_overlines( $block_content, $block ) {
	if ( ! is_string( $block_content ) || empty( $block['blockName'] ) || 'core/paragraph' !== $block['blockName'] ) {
		return $block_content;
	}

	$class_name = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	if ( false === strpos( $class_name, 'mtk-overline' ) ) {
		return $block_content;
	}

	// Only strip pure index labels like "01", "02 / PLC PROGRAMMING" — never real copy like "24/7 SUPPORT" or "3 PHASE".
	$text = trim( wp_strip_all_tags( $block_content ) );
	if ( preg_match( '/^\d{1,2}(\s*$|\s+\/\s)/u', $text ) ) {
		return '';
	}

	return $block_content;
}
add_filter( 'render_block', 'maatek_strip_numbered_overlines', 12, 2 );

/**
 * Keep the header logo linked to the homepage, including headers already saved in the Site Editor.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Parsed block.
 * @return string
 */
function maatek_ensure_logo_links_home( $block_content, $block ) {
	if ( ! is_string( $block_content ) || '' === $block_content || empty( $block['blockName'] ) ) {
		return $block_content;
	}

	$is_site_logo = ( 'core/site-logo' === $block['blockName'] );
	$class_name   = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	$is_brand     = ( 'core/image' === $block['blockName'] && false !== strpos( $class_name, 'mtk-brand-block' ) );

	if ( ! $is_site_logo && ! $is_brand ) {
		return $block_content;
	}

	if ( preg_match( '/<a\b[^>]*\bhref=/i', $block_content ) ) {
		return $block_content;
	}

	$home   = esc_url( home_url( '/' ) );
	$linked = preg_replace(
		'/<img\b[^>]*>/i',
		'<a class="mtk-brand-link" href="' . $home . '" rel="home">$0</a>',
		$block_content,
		1
	);

	return is_string( $linked ) ? $linked : $block_content;
}
add_filter( 'render_block', 'maatek_ensure_logo_links_home', 20, 2 );
add_filter( 'the_content', 'maatek_replace_block_placeholders' );

/**
 * Register one-click Block Style variations in the WordPress Block Editor sidebar.
 */
function maatek_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}

	// Button Block Styles.
	register_block_style(
		'core/button',
		array(
			'name'  => 'mtk-primary',
			'label' => __( 'Maatek Primary', 'maatek' ),
		)
	);
	register_block_style(
		'core/button',
		array(
			'name'  => 'mtk-secondary',
			'label' => __( 'Maatek Secondary Outline', 'maatek' ),
		)
	);
	register_block_style(
		'core/button',
		array(
			'name'  => 'mtk-hero-outline',
			'label' => __( 'Maatek Dark Hero Outline', 'maatek' ),
		)
	);

	// Paragraph Block Styles.
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'mtk-overline',
			'label' => __( 'Maatek Eyebrow / Overline', 'maatek' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'mtk-check-item',
			'label' => __( 'Maatek Cyan Square Bullet', 'maatek' ),
		)
	);

	// Group / Section Block Styles.
	register_block_style(
		'core/group',
		array(
			'name'  => 'mtk-contact-card',
			'label' => __( 'Maatek Bordered White Card', 'maatek' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'mtk-section-grey',
			'label' => __( 'Maatek Cool Grey Section', 'maatek' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'mtk-surface',
			'label' => __( 'Maatek White Section', 'maatek' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'mtk-no-lightbox',
			'label' => __( 'Disable Lightbox in Section', 'maatek' ),
		)
	);

	// Photo Card / Gallery Lightbox Toggle Block Styles.
	register_block_style(
		'core/columns',
		array(
			'name'  => 'mtk-no-lightbox',
			'label' => __( 'Disable Lightbox on Gallery', 'maatek' ),
		)
	);
	register_block_style(
		'core/column',
		array(
			'name'  => 'mtk-no-lightbox',
			'label' => __( 'Disable Lightbox on Card', 'maatek' ),
		)
	);
}
add_action( 'init', 'maatek_register_block_styles', 12 );

/**
 * Fallback Page Resolver: If Apache mod_rewrite/.htaccess or Nginx try_files isn't configured yet
 * and a request for /control-automation (or any of the 7 Maatek page slugs) hits a 404 or index,
 * resolve it cleanly to the seeded WP_Post page so every page works out of the box.
 */
function maatek_fallback_resolve_slug_pages() {
	if ( is_admin() || ! is_404() ) {
		return;
	}

	$req_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$req_path = wp_parse_url( $req_uri, PHP_URL_PATH );
	if ( ! is_string( $req_path ) ) {
		return;
	}

	$home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$rel_path  = trim( substr( $req_path, strlen( rtrim( $home_path, '/' ) ) ), '/' );
	if ( '' === $rel_path ) {
		return;
	}

	$page = get_page_by_path( $rel_path, OBJECT, 'page' );
	if ( ! ( $page instanceof WP_Post ) || 'publish' !== $page->post_status ) {
		return;
	}

	global $wp_query, $post;
	$wp_query = new WP_Query(
		array(
			'page_id'   => (int) $page->ID,
			'post_type' => 'page',
		)
	);
	if ( $wp_query->have_posts() ) {
		$wp_query->the_post();
		$wp_query->rewind_posts();
		status_header( 200 );
		$wp_query->is_404     = false;
		$wp_query->is_page    = true;
		$wp_query->is_singular = true;
	}
}
add_action( 'template_redirect', 'maatek_fallback_resolve_slug_pages', 1 );

/**
 * Seed the 7 Maatek pages with native Gutenberg block content.
 *
 * By default ($force = false), if a page already exists in WordPress with content,
 * its existing content is NEVER overwritten—preserving all client edits across
 * theme updates or re-uploads. Only missing/empty pages are populated unless
 * an administrator explicitly clicks "Reset Default Page Content" ($force = true).
 *
 * @param bool $force Whether to force-reset existing pages back to default Figma content.
 * @return bool Whether all required writes succeeded.
 */
function maatek_seed_pages( $force = false ) {
	$already_installed = (bool) get_option( 'maatek_theme_installed', false );
	$seeded_version    = get_option( 'maatek_block_pages_version', '' );

	if ( ! $force && $already_installed && MAATEK_THEME_VERSION === $seeded_version && ! get_option( 'maatek_seed_errors', false ) ) {
		return true;
	}

	// Ensure wp_filter_post_kses never strips block attributes (e.g. data-object-fit or style) if triggered by an unauthenticated init request.
	$had_kses = ( false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' ) );
	if ( $had_kses && function_exists( 'kses_remove_filters' ) ) {
		kses_remove_filters();
	}

	$initial_pages = maatek_get_seeded_pages();
	$page_ids      = array();
	$created_new   = array();
	$errors        = array();

	// Pass 1: Check if each page already exists (by slug or title) so we never create duplicates.
	foreach ( $initial_pages as $slug => $page_data ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );

		if ( ! ( $existing instanceof WP_Post ) ) {
			// Also check if a trashed or draft page with the same slug exists.
			$by_slug = get_posts(
				array(
					'name'           => $slug,
					'post_type'      => 'page',
					'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
					'posts_per_page' => 1,
				)
			);
			if ( ! empty( $by_slug[0] ) && $by_slug[0] instanceof WP_Post ) {
				$existing = $by_slug[0];
			}
		}

		if ( $existing instanceof WP_Post ) {
			$page_id = (int) $existing->ID;
			// Preserve existing status and template; a reset changes the template only after a successful content update.
		} else {
			$page_result = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_title'   => $page_data['title'],
					'post_name'    => $slug,
					'post_content' => '',
					'post_status'  => 'publish',
				),
				true
			);
			if ( is_wp_error( $page_result ) || ! $page_result ) {
				$errors[] = sprintf( __( 'Could not create the %s page.', 'maatek' ), $page_data['title'] );
				continue;
			}
			$page_id = (int) $page_result;
			if ( $page_id > 0 ) {
				$created_new[ $slug ] = true;
			}
		}

		if ( $page_id > 0 ) {
			$page_ids[ $slug ] = $page_id;
		}
	}

	// The site's permalink and Reading settings are owned by its administrator. In
	// particular, installing a theme for the first time does not mean the site is new.

	// Pass 2: Populate block content ONLY for newly created or empty pages (unless $force is true).
	$pages = maatek_get_seeded_pages();
	foreach ( $pages as $slug => $page_data ) {
		if ( empty( $page_ids[ $slug ] ) ) {
			continue;
		}
		$page_id  = $page_ids[ $slug ];
		$existing = get_post( $page_id );
		$is_empty = ! ( $existing instanceof WP_Post ) || '' === trim( $existing->post_content );

		if ( $force || ! empty( $created_new[ $slug ] ) || $is_empty ) {
			$update_args = array(
				'ID'           => $page_id,
				'post_title'   => $page_data['title'],
				'post_name'    => $slug,
				'post_content' => wp_slash( $page_data['content'] ),
			);
			// Only specify post_status as 'publish' if creating new or if the page was previously draft/empty from theme creation.
			if ( ! empty( $created_new[ $slug ] ) ) {
				$update_args['post_status'] = 'publish';
			}
			$result = wp_update_post( $update_args, true );
			if ( is_wp_error( $result ) || ! $result ) {
				$errors[] = sprintf( __( 'Could not update the %s page.', 'maatek' ), $page_data['title'] );
				continue;
			}
			if ( $force ) {
				delete_post_meta( $page_id, '_wp_page_template' );
			}
			update_post_meta( $page_id, '_maatek_seeded_version', MAATEK_THEME_VERSION );
		}
	}

	// A page-content reset must never overwrite the administrator's menu.
	$nav_items = array(
		'home'               => __( 'Home', 'maatek' ),
		'control-automation' => __( 'Control & Automation', 'maatek' ),
		'maintenance'        => __( 'Maintenance', 'maatek' ),
		'fabrication'        => __( 'Fabrication', 'maatek' ),
		'installations'      => __( 'Installations', 'maatek' ),
		'projects'           => __( 'Projects', 'maatek' ),
		'contact'            => __( 'Contact', 'maatek' ),
	);
	$nav_post  = get_page_by_path( 'maatek-primary-navigation', OBJECT, 'wp_navigation' );
	if ( ! ( $nav_post instanceof WP_Post ) ) {
		$nav_result = wp_insert_post(
			array(
				'post_type'    => 'wp_navigation',
				'post_title'   => __( 'Maatek Primary Navigation', 'maatek' ),
				'post_name'    => 'maatek-primary-navigation',
				'post_content' => wp_slash( maatek_navigation_links_markup( $nav_items ) ),
				'post_status'  => 'publish',
			),
			true
		);
		if ( is_wp_error( $nav_result ) || ! $nav_result ) {
			$errors[] = __( 'Could not create the primary navigation.', 'maatek' );
		}
	}

	if ( $had_kses && function_exists( 'kses_init_filters' ) ) {
		kses_init_filters();
	}

	if ( $errors ) {
		delete_option( 'maatek_block_pages_version' );
		update_option( 'maatek_seed_errors', array( 'messages' => $errors, 'reset' => (bool) $force ), false );
		return false;
	}

	update_option( 'maatek_theme_installed', '1' );
	update_option( 'maatek_block_pages_version', MAATEK_THEME_VERSION );
	if ( '1' !== (string) get_option( 'maatek_theme_installed', '' ) || MAATEK_THEME_VERSION !== get_option( 'maatek_block_pages_version', '' ) ) {
		update_option( 'maatek_seed_errors', array( 'messages' => array( __( 'Could not save the setup completion settings.', 'maatek' ) ), 'reset' => (bool) $force ), false );
		return false;
	}
	delete_option( 'maatek_seed_errors' );
	if ( did_action( 'init' ) ) {
		flush_rewrite_rules( false );
	} else {
		add_action(
			'init',
			function () {
				flush_rewrite_rules( false );
			},
			20
		);
	}
	return true;
}

/**
 * Handle after_switch_theme safely.
 * WordPress passes the old theme name string to after_switch_theme callbacks,
 * which would evaluate to truthy if passed directly to $force. We explicitly pass false.
 *
 * @param string   $old_name  Old theme name.
 * @param WP_Theme $old_theme Old theme object.
 */
function maatek_on_after_switch_theme( $old_name = '', $old_theme = null ) {
	maatek_seed_pages( false );
}
add_action( 'after_switch_theme', 'maatek_on_after_switch_theme', 10, 2 );

/**
 * Ensure pages are seeded on first load and allow admins to re-seed or toggle Lightbox.
 */
function maatek_maybe_seed_on_init() {
	if ( is_admin() && current_user_can( 'manage_options' ) ) {
		if ( isset( $_GET['maatek_reseed'] ) && '1' === $_GET['maatek_reseed'] ) {
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'maatek_reseed_pages' ) ) {
				$success = maatek_seed_pages( true );
				wp_safe_redirect( add_query_arg( 'maatek_reseeded', $success ? '1' : '0', admin_url( 'themes.php' ) ) );
				exit;
			}
		}
		if ( isset( $_GET['maatek_retry_setup'], $_GET['_wpnonce'] ) && '1' === $_GET['maatek_retry_setup'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'maatek_retry_setup' ) ) {
			maatek_seed_pages( false );
			wp_safe_redirect( admin_url( 'themes.php' ) );
			exit;
		}

		if ( isset( $_GET['maatek_toggle_lightbox'] ) ) {
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'maatek_toggle_lightbox' ) ) {
				$new_state = ( '1' === $_GET['maatek_toggle_lightbox'] ) ? '1' : '0';
				update_option( 'maatek_lightbox_enabled', $new_state );
				wp_safe_redirect( add_query_arg( 'maatek_lightbox_updated', $new_state, admin_url( 'themes.php' ) ) );
				exit;
			}
		}
	}

	if ( ! get_option( 'maatek_seed_errors', false ) && MAATEK_THEME_VERSION !== get_option( 'maatek_block_pages_version', '' ) ) {
		maatek_seed_pages( false );
	}
}
add_action( 'init', 'maatek_maybe_seed_on_init', 1 );

/**
 * Update only the known Home and Contact copy from the previous Maatek layout.
 *
 * This is separate from page seeding: it runs once after a theme update, changes
 * only exact paragraph/heading text in the two named pages, and leaves all other
 * saved blocks intact. Text edited by a site administrator is not replaced.
 *
 * @param string $content Existing Gutenberg post content.
 * @param array  $changes Map of old text to its replacement; null removes a block.
 * @return array{content:string,skipped:int} Updated content and unmatched entries.
 */
function maatek_replace_known_copy_blocks( $content, $changes ) {
	$pattern = '~<!--\s+wp:(paragraph|heading)(?:\s+.*?)?\s*-->(.*?)<!--\s+/wp:\1\s*-->~s';
	if ( ! preg_match_all( $pattern, $content, $blocks, PREG_SET_ORDER ) ) {
		return array( 'content' => $content, 'skipped' => count( array_filter( $changes, 'is_string' ) ) );
	}

	$old_counts = array_fill_keys( array_keys( $changes ), 0 );
	$new_counts = array();
	$removable  = array();
	foreach ( $blocks as $block ) {
		$text = trim( html_entity_decode( wp_strip_all_tags( $block[2] ), ENT_QUOTES, 'UTF-8' ) );
		$expected_type = in_array( $text, array( 'What to include', 'Next steps' ), true ) ? 'heading' : 'paragraph';
		if ( array_key_exists( $text, $old_counts ) && $expected_type === $block[1] && 1 === substr_count( $block[0], $text ) && 1 === substr_count( $block[2], $text ) ) {
			++$old_counts[ $text ];
			if ( null === $changes[ $text ] && preg_match( '~^\s*<p\b[^>]*>' . preg_quote( $text, '~' ) . '</p>\s*$~s', $block[2] ) ) {
				$removable[ $text ] = true;
			}
		}
		$new_counts[ $text ] = true;
	}

	$skipped = 0;
	foreach ( $changes as $old => $new ) {
		if ( $old_counts[ $old ] > 1 || ( 0 === $old_counts[ $old ] && null !== $new && ! isset( $new_counts[ $new ] ) ) || ( 1 === $old_counts[ $old ] && null === $new && ! isset( $removable[ $old ] ) ) ) {
			++$skipped;
		}
	}

	$updated = preg_replace_callback(
		$pattern,
		static function ( $block ) use ( $changes, $old_counts ) {
			$text = trim( html_entity_decode( wp_strip_all_tags( $block[2] ), ENT_QUOTES, 'UTF-8' ) );
			$expected_type = in_array( $text, array( 'What to include', 'Next steps' ), true ) ? 'heading' : 'paragraph';
			if ( ! array_key_exists( $text, $changes ) || $expected_type !== $block[1] || 1 !== $old_counts[ $text ] || 1 !== substr_count( $block[0], $text ) || 1 !== substr_count( $block[2], $text ) ) {
				return $block[0];
			}
			if ( null === $changes[ $text ] ) {
				// Remove only the original plain paragraph, never a modified block.
				return preg_match( '~^\s*<p\b[^>]*>' . preg_quote( $text, '~' ) . '</p>\s*$~s', $block[2] ) ? '' : $block[0];
			}
			return str_replace( $text, $changes[ $text ], $block[0] );
		},
		$content
	);
	return array( 'content' => is_string( $updated ) ? $updated : $content, 'skipped' => $skipped );
}

/** Apply the targeted copy update after pages have been seeded, including on WP Puller updates. */
function maatek_apply_copy_update() {
	$option_name = 'maatek_copy_update_20261112';
	$lock_name   = 'maatek_copy_update_lock_20261112';
	if ( get_option( $option_name, false ) ) {
		return;
	}
	$lock = (int) get_option( $lock_name, 0 );
	if ( $lock && $lock < time() - 120 ) {
		delete_option( $lock_name );
	}
	if ( ! add_option( $lock_name, time(), '', false ) ) {
		return;
	}

	$changes = array(
		'home' => array(
			'Electrical, mechanical and automation services for industrial plant and production facilities.' => 'Maatek Electrical provides electrical control and automation, industrial maintenance, mechanical fabrication and factory installations. Explore our services and project image galleries below.',
			'Switchboards, PLC programming, HMI, SCADA, industrial networks, process controls and machinery safety.' => 'Electrical switchboards, PLC programming, HMI and SCADA systems and machinery safety designed around the needs of industrial production.',
			'Troubleshooting, breakdown response, equipment servicing and planned maintenance for industrial plant.' => 'Practical support for industrial plant reliability, troubleshooting, equipment servicing and planned maintenance.',
			'Conveyors, machinery, steelwork and equipment integration, with attention to access and maintainability.' => 'Mechanical fabrication and equipment integration, with attention to plant access, installation requirements and maintainability.',
			'Plant relocation, equipment installation, machinery integration and coordinated commissioning.' => 'Industrial equipment installation, machinery integration and coordinated commissioning in working production environments.',
		),
		'contact' => array(
			'For industrial plant and production facilities. Tell us about your site, equipment and what you need to achieve.' => 'From industrial maintenance and machine upgrades to switchboards, automation and turnkey installations, start with a conversation about your site and what you need to achieve.',
			'What to include' => 'Tell us about the challenge.',
			'Share your site location to discuss suitability, access and installation requirements.' => null,
			'Start with your requirements, discuss the scope and site access, then talk through the appropriate next steps with Marko.' => 'For industrial maintenance, PLC and automation systems, switchboard manufacturing, fabrication and equipment installation, contact Marko directly.',
		),
	);
	$errors  = array();
	$skipped = array();
	try {
		foreach ( $changes as $slug => $replacements ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );
			if ( ! ( $page instanceof WP_Post ) ) {
				$errors[] = $slug;
				continue;
			}
			$result = maatek_replace_known_copy_blocks( $page->post_content, $replacements );
			if ( $result['skipped'] ) {
				$skipped[ $slug ] = $result['skipped'];
			}
			if ( $result['content'] === $page->post_content ) {
				continue;
			}
			// Do not overwrite an edit made after we read the page.
			clean_post_cache( $page->ID );
			$latest = get_post( $page->ID );
			if ( ! ( $latest instanceof WP_Post ) || $latest->post_content !== $page->post_content ) {
				$errors[] = $slug;
				continue;
			}
			// On a logged-out request KSES can strip existing block attributes. The
			// content comes from the database and only exact known text is changed.
			$had_kses = ( false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' ) );
			if ( $had_kses ) {
				kses_remove_filters();
			}
			try {
				$revision = wp_save_post_revision( $page->ID );
				$updated  = is_wp_error( $revision ) ? $revision : wp_update_post( array( 'ID' => $page->ID, 'post_content' => wp_slash( $result['content'] ) ), true );
			} finally {
				if ( $had_kses ) {
					kses_init_filters();
				}
			}
			if ( is_wp_error( $updated ) || ! $updated ) {
				$errors[] = $slug;
			}
		}
		if ( $errors ) {
			update_option( 'maatek_copy_update_errors', $errors, false );
		} else {
			delete_option( 'maatek_copy_update_errors' );
			update_option( $option_name, array( 'done' => true, 'skipped' => $skipped ), false );
		}
	} finally {
		delete_option( $lock_name );
	}
}
add_action( 'init', 'maatek_apply_copy_update', 20 );

/**
 * Restore exact original wording in the newer layout. Only known unchanged
 * blocks are updated; a site editor's replacement text is left untouched.
 */
function maatek_apply_original_details_update() {
	$option = 'maatek_original_details_update_v1';
	$lock = 'maatek_original_details_lock_v1';
	if ( get_option( $option, false ) ) {
		return;
	}
	$lock_time = (int) get_option( $lock, 0 );
	if ( $lock_time && $lock_time < time() - 120 ) {
		delete_option( $lock );
	}
	if ( ! add_option( $lock, time(), '', false ) ) {
		return;
	}
	$changes = array(
		'home' => array(
			'Maatek Electrical provides electrical control and automation, industrial maintenance, mechanical fabrication and factory installations. Explore our services and project galleries.' => 'Maatek Electrical provides electrical control and automation, industrial maintenance, mechanical fabrication and factory installations. Explore our services and project image galleries below.',
		),
		'contact' => array(
			'Share a few details when you call or email:' => 'Share the equipment involved, your project timeframe, the operating environment and any relevant drawings or specifications. We can then discuss the appropriate next steps.',
			'Next steps' => 'Discuss your project',
			'We can then discuss the appropriate next steps.' => 'For industrial maintenance, PLC and automation systems, switchboard manufacturing, fabrication and equipment installation, contact Marko directly.',
		),
		'installations' => array(),
	);
	$shortcuts = array(
		'Control &amp; Automation' => 'PLC, SCADA, HMI, industrial networks and process controls.',
		'Maintenance' => 'Breakdown response, troubleshooting and preventative maintenance.',
		'Fabrication' => 'Mechanical fabrication, conveyors, machinery and steelwork.',
		'Installations' => 'Plant relocation, factory installations and commissioning.',
	);
	$errors = array();
	$skipped = array();
	try {
		foreach ( $changes as $slug => $replacements ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );
			if ( ! ( $page instanceof WP_Post ) ) {
				$errors[] = $slug;
				continue;
			}
			$result = maatek_replace_known_copy_blocks( $page->post_content, $replacements );
			$content = $result['content'];
			if ( $result['skipped'] ) {
				$skipped[ $slug ] = true;
			}
			if ( 'home' === $slug ) {
				// Change only the known default shortcut anchors, leaving links and custom copy intact.
				foreach ( $shortcuts as $title => $description ) {
					$old = '<span>' . $title . '</span><span class="mtk-arrow">→</span>';
					$new = '<span class="mtk-shortcut-copy"><span class="mtk-shortcut-title">' . $title . '</span><span class="mtk-shortcut-desc">' . $description . '</span></span><span class="mtk-arrow">→</span>';
					$pattern = '~(<a\\b(?=[^>]*\\bclass="[^"]*\\bmtk-shortcut\\b[^"]*")[^>]*>)' . preg_quote( $old, '~' ) . '(</a>)~s';
					if ( 1 === preg_match_all( $pattern, $content ) ) {
						$content = preg_replace_callback( $pattern, static function ( $m ) use ( $new ) { return $m[1] . $new . $m[2]; }, $content, 1 );
					} elseif ( false === strpos( $content, $new ) ) {
						$skipped[ $slug ] = true;
					}
				}
				$hero_pattern = '~(<a\\b(?=[^>]*\\bhref="#home-services")[^>]*>)Explore services (\\s*<span class="mtk-arrow">→</span>\\s*</a>)~s';
				if ( 1 === preg_match_all( $hero_pattern, $content ) ) {
					$content = preg_replace_callback( $hero_pattern, static function ( $m ) { return $m[1] . 'Explore Our Services ' . $m[2]; }, $content, 1 );
				}
			} elseif ( 'contact' === $slug ) {
				// Remove only the unchanged default checklist, which repeats the restored paragraph.
				$list_pattern = '~<!--\\s+wp:group\\s+\\{[^>]*"className":"mtk-check-list mtk-mb-6"[^>]*\\}\\s*-->.*?<!--\\s+/wp:group\\s*-->~s';
				preg_match_all( $list_pattern, $content, $lists );
				if ( 1 === count( $lists[0] ) ) {
					$default_list = '<!-- wp:group {"className":"mtk-check-list mtk-mb-6","layout":{"type":"default"}} --><div class="wp-block-group mtk-check-list mtk-mb-6"><!-- wp:paragraph {"className":"mtk-check-item"} --><p class="mtk-check-item">Equipment and work required</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"mtk-check-item"} --><p class="mtk-check-item">Site location and operating environment</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"mtk-check-item"} --><p class="mtk-check-item">Project timeframe</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"mtk-check-item"} --><p class="mtk-check-item">Relevant drawings or specifications</p><!-- /wp:paragraph --></div><!-- /wp:group -->';
					if ( preg_replace( '/\\s+/', '', $lists[0][0] ) === preg_replace( '/\\s+/', '', $default_list ) ) {
						$content = str_replace( $lists[0][0], '', $content );
					} else {
						$skipped[ $slug ] = true;
					}
				}
				if ( false === strpos( $content, 'DIRECT CONTACT' ) ) {
					$label_pattern = '~(<!--\\s+wp:group\\s+\\{[^>]*"className":"mtk-contact-card"[^>]*\\}\\s*-->\\s*<div\\b[^>]*\\bclass="[^"]*\\bmtk-contact-card\\b[^"]*"[^>]*>)(\\s*<!--\\s+wp:heading\\b[^>]*-->\\s*<h3\\b[^>]*>Marko Markovic</h3>)~s';
					if ( 1 === preg_match_all( $label_pattern, $content ) ) {
						$label_block = "\n<!-- wp:paragraph {\"className\":\"mtk-overline mtk-mb-2\"} -->\n<p class=\"mtk-overline mtk-mb-2\">DIRECT CONTACT</p>\n<!-- /wp:paragraph -->\n";
						$content = preg_replace_callback( $label_pattern, static function ( $m ) use ( $label_block ) { return $m[1] . $label_block . $m[2]; }, $content, 1 );
					} else {
						$skipped[ $slug ] = true;
					}
				}
				$hint = 'Tap the number to call from your mobile.';
				if ( false === strpos( $content, $hint ) ) {
					$pattern = '~(<!--\\s+wp:paragraph\\b[^>]*-->\\s*<p\\b[^>]*>\\s*<a\\b[^>]*href="mailto:marko@maatekelectrical\\.com\\.au"[^>]*>marko@maatekelectrical\\.com\\.au</a>\\s*</p>\\s*<!--\\s+/wp:paragraph\\s*-->)~s';
					if ( 1 === preg_match_all( $pattern, $content ) ) {
						$hint_block = "\n<!-- wp:paragraph {\"className\":\"mtk-small mtk-text-muted mtk-mt-2\"} -->\n<p class=\"mtk-small mtk-text-muted mtk-mt-2\">" . $hint . "</p>\n<!-- /wp:paragraph -->";
						$content = preg_replace_callback( $pattern, static function ( $m ) use ( $hint_block ) { return $m[1] . $hint_block; }, $content, 1 );
					} else {
						$skipped[ $slug ] = true;
					}
				}
			} elseif ( 'installations' === $slug ) {
				// Replace this gallery card only when all its original image and text still match.
				$pattern = '~<!--\\s+wp:column\\s+\\{[^>]*mtk-photo-card[^>]*\\}\\s*-->.*?<!--\\s+/wp:column\\s*-->~s';
				preg_match_all( $pattern, $content, $matches );
				$old_caption = 'Site lifting arrangements and safe access are coordinated before equipment is moved into position.';
				$candidates = array_values( array_filter( $matches[0], static function ( $card ) use ( $old_caption ) {
					return 1 === ( substr_count( $card, 'crane-equipment-handling.webp' ) + substr_count( $card, '55e5d9a70651bf11.webp' ) ) && 2 === substr_count( $card, 'Crane and equipment handling' ) && 1 === substr_count( $card, $old_caption );
				} ) );
				if ( 1 === count( $candidates ) ) {
					$updated_card = str_replace( array( 'crane-equipment-handling.webp', '55e5d9a70651bf11.webp', 'Crane and equipment handling', $old_caption ), array( 'factory-equipment-installation.webp', 'factory-equipment-installation.webp', 'Factory equipment installation', 'Industrial machinery installation and positioning within an operating manufacturing facility.' ), $candidates[0] );
					$content = str_replace( $candidates[0], $updated_card, $content );
				} elseif ( false === strpos( $content, 'Factory equipment installation' ) ) {
					$skipped[ $slug ] = true;
				}
			}
			if ( ! is_string( $content ) || $content === $page->post_content ) {
				continue;
			}
			clean_post_cache( $page->ID );
			$latest = get_post( $page->ID );
			if ( ! ( $latest instanceof WP_Post ) || $latest->post_content !== $page->post_content ) {
				$errors[] = $slug;
				continue;
			}
			$had_kses = ( false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' ) );
			if ( $had_kses ) { kses_remove_filters(); }
			try {
				$revision = wp_save_post_revision( $page->ID );
				$updated = is_wp_error( $revision ) ? $revision : wp_update_post( array( 'ID' => $page->ID, 'post_content' => wp_slash( $content ) ), true );
			} finally {
				if ( $had_kses ) { kses_init_filters(); }
			}
			if ( is_wp_error( $updated ) || ! $updated ) { $errors[] = $slug; }
		}
		if ( $errors ) {
			update_option( 'maatek_original_details_errors', $errors, false );
		} else {
			delete_option( 'maatek_original_details_errors' );
			update_option( $option, array( 'done' => true, 'skipped' => $skipped ), false );
		}
	} finally {
		delete_option( $lock );
	}
}
add_action( 'init', 'maatek_apply_original_details_update', 21 );

/**
 * Register Lightbox ON/OFF toggle in WordPress Customizer for live previewing.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function maatek_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'maatek_options',
		array(
			'title'       => __( 'Maatek Lightbox & Options', 'maatek' ),
			'description' => __( 'Control global interactive features for Maatek Electrical.', 'maatek' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_setting(
		'maatek_lightbox_enabled',
		array(
			'type'              => 'option',
			'default'           => '1',
			'sanitize_callback' => function ( $val ) {
				return ( $val && '0' !== (string) $val ) ? '1' : '0';
			},
		)
	);

	$wp_customize->add_control(
		'maatek_lightbox_enabled',
		array(
			'label'       => __( 'Enable Project Photo Lightbox', 'maatek' ),
			'description' => __( 'When enabled, clicking any project photo card opens the full-screen lightbox viewer. You can also disable the lightbox on individual cards or sections in the Block Editor under Styles → "Disable Lightbox".', 'maatek' ),
			'section'     => 'maatek_options',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'maatek_customize_register' );

/**
 * Display a helpful plug-and-play status panel on Appearance -> Themes with quick Editor, Lightbox Toggle & Re-sync actions.
 */
function maatek_admin_theme_notice() {
	global $pagenow;
	if ( 'themes.php' !== $pagenow || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['maatek_reseeded'] ) && '1' === $_GET['maatek_reseeded'] ) {
		echo '<div class="notice notice-success is-dismissible"><p><strong>Maatek Theme:</strong> All 7 pages have been reset to the default Figma block layouts.</p></div>';
	}

	$seed_errors = get_option( 'maatek_seed_errors', false );
	if ( is_array( $seed_errors ) && ! empty( $seed_errors['messages'] ) && is_array( $seed_errors['messages'] ) ) {
		echo '<div class="notice notice-error"><p><strong>Maatek setup was not completed.</strong> ' . esc_html( implode( ' ', $seed_errors['messages'] ) ) . '</p>';
		if ( ! empty( $seed_errors['reset'] ) ) {
			echo '<p>Some pages may have been reset. Check the pages and, if necessary, retry the page reset after resolving the database or plugin error.</p>';
		} else {
			$retry_url = wp_nonce_url( add_query_arg( 'maatek_retry_setup', '1', admin_url( 'themes.php' ) ), 'maatek_retry_setup' );
			echo '<p>Check the database and plugin logs, then <a href="' . esc_url( $retry_url ) . '">retry setup</a>. Existing page content is preserved.</p>';
		}
		echo '</div>';
	}
	$copy_errors = get_option( 'maatek_copy_update_errors', false );
	if ( is_array( $copy_errors ) && $copy_errors ) {
		echo '<div class="notice notice-error"><p><strong>Maatek:</strong> The copy update could not finish for ' . esc_html( implode( ', ', $copy_errors ) ) . '. Check the pages and database or plugin logs; the update will retry on the next request.</p></div>';
	}
	$copy_status = get_option( 'maatek_copy_update_20261112', false );
	if ( is_array( $copy_status ) && ! empty( $copy_status['skipped'] ) && is_array( $copy_status['skipped'] ) ) {
		echo '<div class="notice notice-warning"><p><strong>Maatek:</strong> Some Home or Contact wording was already different and was left unchanged. <a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '">Review the pages</a> if you want to update it manually.</p></div>';
	}
	$details_errors = get_option( 'maatek_original_details_errors', false );
	if ( is_array( $details_errors ) && $details_errors ) {
		echo '<div class="notice notice-error"><p><strong>Maatek:</strong> The original wording update could not finish for ' . esc_html( implode( ', ', $details_errors ) ) . '. Check the pages and database or plugin logs; the update will retry on the next request.</p></div>';
	}
	$details_status = get_option( 'maatek_original_details_update_v1', false );
	if ( is_array( $details_status ) && ! empty( $details_status['skipped'] ) ) {
		echo '<div class="notice notice-warning"><p><strong>Maatek:</strong> Some original wording or gallery details had already been changed and were preserved. <a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '">Review the pages</a> if you want to update them manually.</p></div>';
	}
	$unpublished = maatek_unpublished_destinations();
	if ( $unpublished ) {
		$titles = array();
		foreach ( $unpublished as $slug => $page ) {
			$titles[] = $page->post_title;
		}
		echo '<div class="notice notice-warning"><p><strong>Maatek:</strong> Links to these unpublished pages are hidden from visitors until they are published: ' . esc_html( implode( ', ', $titles ) ) . '. <a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '">Review pages</a>.</p></div>';
	}

	if ( isset( $_GET['maatek_lightbox_updated'] ) ) {
		$status_label = ( '1' === $_GET['maatek_lightbox_updated'] ) ? 'enabled' : 'disabled';
		echo '<div class="notice notice-success is-dismissible"><p><strong>Maatek Theme:</strong> Project photo lightbox is now <strong>' . esc_html( $status_label ) . '</strong> across the site.</p></div>';
	}

	$lb_enabled = maatek_is_lightbox_enabled();
	$lb_target  = $lb_enabled ? '0' : '1';
	$lb_url     = wp_nonce_url( add_query_arg( 'maatek_toggle_lightbox', $lb_target, admin_url( 'themes.php' ) ), 'maatek_toggle_lightbox' );
	$lb_btn_txt = $lb_enabled ? 'Lightbox: ON (Click to Turn Off)' : 'Lightbox: OFF (Click to Turn On)';

	$reseed_url  = wp_nonce_url( add_query_arg( 'maatek_reseed', '1', admin_url( 'themes.php' ) ), 'maatek_reseed_pages' );
	$pages_url   = admin_url( 'edit.php?post_type=page' );
	$editor_url  = admin_url( 'site-editor.php' );
	$reading_url = admin_url( 'options-reading.php' );

	echo '<div class="notice notice-info" style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">';
	echo '<div><strong>Maatek Block Theme Active:</strong> Manage pages, navigation and styles using the options below.</div>';
	echo '<div style="display:flex;gap:8px;flex-wrap:wrap;">';
	echo '<a class="button button-primary" href="' . esc_url( $pages_url ) . '">Edit Pages</a>';
	echo '<a class="button" href="' . esc_url( $editor_url ) . '">Open Site Editor (Header, Footer &amp; Styles)</a>';
	echo '<a class="button" href="' . esc_url( $reading_url ) . '">Choose Homepage</a>';
	echo '<a class="button" href="' . esc_url( $lb_url ) . '">' . esc_html( $lb_btn_txt ) . '</a>';
	echo '<a class="button" href="' . esc_url( $reseed_url ) . '" onclick="return confirm(\'Reset all 7 Maatek pages back to their original Figma content?\');">Reset Default Page Content</a>';
	echo '</div></div>';
}
add_action( 'admin_notices', 'maatek_admin_theme_notice' );

/**
 * Register Maatek Block Pattern Category & Reusable Block Patterns for the Gutenberg Inserter.
 */
function maatek_register_block_patterns() {
	if ( ! function_exists( 'register_block_pattern_category' ) || ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'maatek',
		array(
			'label' => __( 'Maatek Industrial Sections', 'maatek' ),
		)
	);

	$assets       = untrailingslashit( get_template_directory_uri() ) . '/assets';
	$contact      = maatek_get_page_url( 'contact' );
	$contact_attr = esc_url( $contact );

	// 1. Service Hero Pattern (Native Cover Block).
	register_block_pattern(
		'maatek/service-hero',
		array(
			'title'      => __( 'Maatek — Service Page Hero Banner', 'maatek' ),
			'categories' => array( 'maatek' ),
			'content'    => <<<HTML
<!-- wp:cover {"url":"{$assets}/control-automation-service-hero.webp","dimRatio":70,"overlayColor":"heading-dark","isUserOverlayColor":true,"minHeight":470,"minHeightUnit":"px","tagName":"section","className":"mtk-hero-service","layout":{"type":"default"}} -->
<section class="wp-block-cover mtk-hero-service" style="min-height:470px"><span aria-hidden="true" class="wp-block-cover__background has-heading-dark-background-color has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{$assets}/control-automation-service-hero.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-hero-kicker"} -->
		<p class="mtk-hero-kicker">MAATEK ELECTRICAL / INDUSTRIAL SERVICES</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1,"className":"mtk-hero-service-title mtk-max-w-760"} -->
		<h1 class="wp-block-heading mtk-hero-service-title mtk-max-w-760">Engineered for industrial reliability</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-hero-service-lead"} -->
		<p class="mtk-hero-service-lead">Practical electrical, control and mechanical solutions built around real operating environments.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"className":"mtk-btn-group"} -->
		<div class="wp-block-buttons mtk-btn-group">
			<!-- wp:button {"className":"mtk-btn mtk-btn-primary"} -->
			<div class="wp-block-button mtk-btn mtk-btn-primary"><a class="wp-block-button__link wp-element-button" href="{$contact_attr}">Discuss your project <span class="mtk-arrow">→</span></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div></section>
<!-- /wp:cover -->
HTML,
		)
	);

	// 2. Specialist Service Feature Row Pattern.
	register_block_pattern(
		'maatek/service-feature-row',
		array(
			'title'      => __( 'Maatek — Specialist Service Feature Row', 'maatek' ),
			'categories' => array( 'maatek' ),
			'content'    => maatek_block_service_feature_row(
				array(
					'id'            => 'service-feature',
					'overline'      => '01',
					'title'         => 'Industrial Electrical & Control Systems',
					'paragraphs'    => array(
						'Custom switchboards, PLC automation and field installation engineered for maintainable plant operation.',
					),
					'bullets'       => array(
						'Custom switchboards and control panels',
						'Rockwell Studio 5000 PLC & FactoryTalk HMI',
						'Commissioning and ongoing technical support',
					),
					'img_url'       => "{$assets}/small-control-switchboard-enhanced.webp",
					'img_alt'       => 'Industrial electrical control switchboard',
					'image_on_left' => true,
				)
			),
		)
	);

	// 3. 2-Column Photo Gallery Pattern.
	$card_a = maatek_block_photo_card(
		"{$assets}/industrial-switchboard-circuit-breaker.webp",
		'Industrial switchboard circuit breaker',
		'Switchboard protection and isolation equipment forms part of a maintainable electrical control system.'
	);
	$card_b = maatek_block_photo_card(
		"{$assets}/scada-operator-interface.webp",
		'SCADA operator interface',
		'Process overview displays bring plant status, alarms and operating information together.'
	);
	register_block_pattern(
		'maatek/photo-gallery-2col',
		array(
			'title'      => __( 'Maatek — 2-Column Lightbox Photo Gallery', 'maatek' ),
			'categories' => array( 'maatek' ),
			'content'    => <<<HTML
<!-- wp:group {"tagName":"section","className":"mtk-bg-surface mtk-py-16","layout":{"type":"default"}} -->
<section class="wp-block-group mtk-bg-surface mtk-py-16">
	<!-- wp:group {"className":"mtk-container","layout":{"type":"default"}} -->
	<div class="wp-block-group mtk-container">
		<!-- wp:paragraph {"className":"mtk-overline mtk-mb-2"} -->
		<p class="mtk-overline mtk-mb-2">PROJECT DETAILS</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"mtk-h2 mtk-mb-3"} -->
		<h2 class="wp-block-heading mtk-h2 mtk-mb-3">Equipment and installation details</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"mtk-small mtk-text-muted"} -->
		<p class="mtk-small mtk-text-muted">Select a photograph to view it larger.</p>
		<!-- /wp:paragraph -->
		<!-- wp:columns {"className":"mtk-photo-grid-2"} -->
		<div class="wp-block-columns mtk-photo-grid-2">
{$card_a}
{$card_b}
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
HTML,
		)
	);

	// 4. Direct Contact Card Pattern.
	register_block_pattern(
		'maatek/direct-contact-card',
		array(
			'title'      => __( 'Maatek — Direct Contact Card & Action Buttons', 'maatek' ),
			'categories' => array( 'maatek' ),
			'content'    => <<<HTML
<!-- wp:group {"className":"mtk-max-w-520","layout":{"type":"default"}} -->
<div class="wp-block-group mtk-max-w-520">
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
<!-- /wp:group -->
HTML,
		)
	);

	// 5. Project Enquiry Split CTA Band Pattern.
	register_block_pattern(
		'maatek/project-enquiry-cta',
		array(
			'title'      => __( 'Maatek — Project Enquiry Split CTA Band', 'maatek' ),
			'categories' => array( 'maatek' ),
			'content'    => maatek_block_service_enquiry_cta( $contact ),
		)
	);
}
add_action( 'init', 'maatek_register_block_patterns', 15 );
