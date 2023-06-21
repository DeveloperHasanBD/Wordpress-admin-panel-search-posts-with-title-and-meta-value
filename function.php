<?php 

if (!function_exists('extend_admin_search')) {
    add_action('admin_init', 'extend_admin_search');

    function extend_admin_search() {
        global $typenow;

        if ($typenow === 'cards') {
            add_filter('posts_search', 'posts_search_custom_post_type', 10, 2);
        }
    }

    function posts_search_custom_post_type($search, $query) {
        global $wpdb;

        if ($query->is_main_query() && !empty($query->query['s'])) {
            $sql    = "
            or exists (
                select * from {$wpdb->postmeta} where post_id={$wpdb->posts}.ID
                and meta_key in ('aci_card_number', 'aci_card_number_2', 'aci_card_number_3')
                and meta_value like %s
            )
        ";
            $like   = '%' . $wpdb->esc_like($query->query['s']) . '%';
            $search = preg_replace("#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#",
                $wpdb->prepare($sql, $like), $search);
        }

        return $search;
    }
}

?>
