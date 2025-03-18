<?php
/**
 * Schema Handler
 *
 * @package ASM_Base_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ASM_Schema_Handler {
	private static $instance = null;
	private $cache_key = 'asm_schema_cache';
	private $enabled_schemas = [];

	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->enabled_schemas = get_option( 'asm_enabled_schemas', [] );
		add_action( 'wp_head', array( $this, 'inject_schema_markup' ) );
	}

	public function inject_schema_markup() {
		$schema_data = $this->generate_schema();
		if ( $schema_data ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		}
	}

	private function generate_schema() {
		$cached_schema = get_transient( $this->cache_key );
		if ( $cached_schema ) {
			return $cached_schema;
		}

		$schema_data = [];

		if ( is_single() && in_array( 'article', $this->enabled_schemas, true ) ) {
			$schema_data[] = $this->get_article_schema();
		} elseif ( is_front_page() && in_array( 'website', $this->enabled_schemas, true ) ) {
			$schema_data[] = $this->get_website_schema();
		} elseif ( is_product() && in_array( 'product', $this->enabled_schemas, true ) ) {
			$schema_data[] = $this->get_product_schema();
		}

		set_transient( $this->cache_key, $schema_data, 12 * HOUR_IN_SECONDS );
		return $schema_data;
	}

	private function get_article_schema() {
		return [
			"@context" => "https://schema.org",
			"@type"    => "Article",
			"headline" => get_the_title(),
			"author"   => [
				"@type" => "Person",
				"name"  => get_the_author()
			],
			"datePublished" => get_the_date( 'c' ),
			"dateModified"  => get_the_modified_date( 'c' ),
			"mainEntityOfPage" => get_permalink()
		];
	}

	private function get_website_schema() {
		return [
			"@context" => "https://schema.org",
			"@type"    => "WebSite",
			"name"     => get_bloginfo( 'name' ),
			"url"      => home_url()
		];
	}

	private function get_product_schema() {
		global $product;
		if ( ! $product ) {
			return null;
		}

		return [
			"@context"  => "https://schema.org",
			"@type"     => "Product",
			"name"      => get_the_title(),
			"image"     => get_the_post_thumbnail_url(),
			"description" => get_the_excerpt(),
			"offers"    => [
				"@type"        => "Offer",
				"priceCurrency"=> get_woocommerce_currency(),
				"price"        => $product->get_price(),
				"availability" => "https://schema.org/" . ( $product->is_in_stock() ? 'InStock' : 'OutOfStock' ),
				"url"          => get_permalink()
			]
		];
	}
}

ASM_Schema_Handler::get_instance();
