<?php
namespace BYOB\PostTypes;

class Battery {
    public const POST_TYPE = 'battery';
    
    public function register(): void {
        add_action('init', [$this, 'register_post_type']);
    }

    public function register_post_type(): void {
        $labels = [
            'name'               => __('Batteries', 'build-your-own-battery'),
            'singular_name'      => __('Battery', 'build-your-own-battery'),
            'menu_name'          => __('Battery Builder', 'build-your-own-battery'),
            'add_new'            => __('Add New', 'build-your-own-battery'),
            'add_new_item'       => __('Add New Battery', 'build-your-own-battery'),
            'edit_item'          => __('Edit Battery', 'build-your-own-battery'),
            'new_item'           => __('New Battery', 'build-your-own-battery'),
            'view_item'          => __('View Battery', 'build-your-own-battery'),
            'search_items'       => __('Search Batteries', 'build-your-own-battery'),
            'not_found'          => __('No batteries found', 'build-your-own-battery'),
            'not_found_in_trash' => __('No batteries found in trash', 'build-your-own-battery'),
        ];

        $args = [
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => ['slug' => 'battery'],
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-database',
            'supports'            => ['title', 'editor', 'thumbnail'],
            'show_in_rest'        => true,
        ];

        register_post_type(self::POST_TYPE, $args);
    }
} 