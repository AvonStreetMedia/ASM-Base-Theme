<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class ASM_Schema {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_head', array($this, 'inject_schema_markup'));
    }

    public function inject_schema_markup() {
        $schema_data = $this->generate_schema();
        if ($schema_data) {
            echo '<script type="application/ld+json">' . json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
    }

    private function generate_schema() {
        if (is_single()) {
            return $this->get_article_schema();
        } elseif (is_front_page()) {
            return $this->get_website_schema();
        }
        return null;
    }

    private function get_article_schema() {
        return [
            "@context" => "https://schema.org",
            "@type" => "Article",
            "headline" => get_the_title(),
            "author" => [
                "@type" => "Person",
                "name" => get_the_author()
            ],
            "datePublished" => get_the_date('c'),
            "dateModified" => get_the_modified_date('c'),
            "mainEntityOfPage" => get_permalink()
        ];
    }

    private function get_website_schema() {
        return [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => get_bloginfo('name'),
            "url" => home_url()
        ];
    }
}

ASM_Schema::get_instance();