<?php
/**
 * Custom Post Type Catalogue'
 *
 * @since 1.0.0
 *
 * @return void
 */
function catalog_cpt() : void {
	$labels = [
		'name' => _x( 'Catalogue', 'Post Type General Name', 'hello-elementor-child' ),
		'singular_name' => _x( 'Catalogue', 'Post Type Singular Name', 'hello-elementor-child' ),
		'menu_name' => __( 'Catalogues', 'hello-elementor-child' ),
		'name_admin_bar' => __( 'Catalogues', 'hello-elementor-child' ),
		'archives' => __( 'Archives catalogues', 'hello-elementor-child' ),
		'attributes' => __( 'Catalogues attributs', 'hello-elementor-child' ),
		'parent_item_colon' => __( 'Revue parent:', 'hello-elementor-child' ),
		'all_items' => __( 'Toutes les revues', 'hello-elementor-child' ),
		'add_new_item' => __( 'Ajouter une revue', 'hello-elementor-child' ),
		'add_new' => __( 'Ajouter', 'hello-elementor-child' ),
		'new_item' => __( 'Nouvelle revue', 'hello-elementor-child' ),
		'edit_item' => __( 'Modifier une revue', 'hello-elementor-child' ),
		'update_item' => __( 'Mettre à jour', 'hello-elementor-child' ),
		'view_item' => __( 'Voir', 'hello-elementor-child' ),
		'view_items' => __( 'Voir les revues', 'hello-elementor-child' ),
		'search_items' => __( 'Chercher des revues', 'hello-elementor-child' ),
		'not_found' => __( 'Aucune revue trouvée', 'hello-elementor-child' ),
		'not_found_in_trash' => __( 'Aucune revue trouvée dans la corbeille', 'hello-elementor-child' ),
		'featured_image' => __( 'Image selectionnée', 'hello-elementor-child' ),
		'set_featured_image' => __( 'Définir l\'image sélectionnée', 'hello-elementor-child' ),
		'remove_featured_image' => __( 'Supprimer l\'image sélectionnée', 'hello-elementor-child' ),
		'use_featured_image' => __( 'Utiliser l\'image sélectionnée', 'hello-elementor-child' ),
		'insert_into_item' => __( 'Insérer dans la revue', 'hello-elementor-child' ),
		'uploaded_to_this_item' => __( 'Téléverser dans la revue', 'hello-elementor-child' ),
		'items_list' => __( 'Listes des revues', 'hello-elementor-child' ),
		'items_list_navigation' => __( 'Liste de navigation des revues', 'hello-elementor-child' ),
		'filter_items_list' => __( 'Filtrer la listes des revues', 'hello-elementor-child' ),
	];
	$labels = apply_filters( 'catalog-labels', $labels );

	$args = [
		'label' => __( 'catalogue', 'hello-elementor-child' ),
		'description' => __( 'Catalogue Mirabel', 'hello-elementor-child' ),
		'labels' => $labels,
		'supports' => [
			'title',
		],
		'hierarchical' => true,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => false,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-book',
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'can_export' => true,
		'rewrite' => [
			'slug' => 'inventaire',
			'with_front' => false,
		],
		'capability_type' => 'post',
		'show_in_rest' => true,
	];
	$args = apply_filters( 'catalog-args', $args );

	register_post_type( 'catalog', $args );
}
add_action( 'init', 'catalog_cpt', 0 );

// Retire single page
add_filter( 'is_post_type_viewable', function ( $is_viewable, $post_type ){
    if ( false == $is_viewable || 'catalog' === $post_type->name ){
        return false;
    }
    return true;
}, 10, 2 );