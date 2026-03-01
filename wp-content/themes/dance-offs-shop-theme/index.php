<?php

// Enable WooCommerce support
add_action('after_setup_theme', function() {
    add_theme_support('woocommerce');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
});

