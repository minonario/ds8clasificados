<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies
 *
 * @class       DS8_CPT
 * @version     1.0
 * @package     DS8/Classes/Clasificado
 * @category    Class
 * @author      Jose Luis Morales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DS8_CPT Class
 */
class DS8_CPT {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
                add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {

		if ( post_type_exists('clasificado') ) {
			return;
		}

		$permalinks = get_option( 'ds8clasificado_permalinks' );
		$clasificado_permalink = empty( $permalinks['clasificado_base'] ) ? _x( 'clasificado', 'slug', 'ds8clasificado' ) : $permalinks['clasificado_base'];

		register_post_type( 'clasificado',
			apply_filters( 'ds8clasificado_register_post_type_clasificado',
				array(
                                    'labels'  => array(
                                                    'name'               => __( 'Clasificados', 'ds8clasificado' ),
                                                    'singular_name'      => __( 'Clasificado', 'ds8clasificado' ),
                                                    'menu_name'          => _x( 'Clasificados', 'Admin menu name', 'ds8clasificado' ),
                                                    'add_new'            => __( 'Add new', 'ds8clasificado' ),
                                                    'add_new_item'       => __( 'Add new Clasificado', 'ds8clasificado' ),
                                                    'edit'               => __( 'Edit', 'ds8clasificado' ),
                                                    'edit_item'          => __( 'Edit Clasificado', 'ds8clasificado' ),
                                                    'new_item'           => __( 'New Clasificado', 'ds8clasificado' ),
                                                    'view'               => __( 'View', 'ds8clasificado' ),
                                                    'view_item'          => __( 'View Clasificado', 'ds8clasificado' ),
                                                    'search_items'       => __( 'Search Clasificado', 'ds8clasificado' ),
                                                    'not_found'          => __( 'Not found Clasificado', 'ds8clasificado' ),
                                                    'not_found_in_trash' => __( 'Not found Clasificado in trash', 'ds8clasificado' ),
                                                    'parent'             => __( 'Parent Clasificado', 'ds8clasificado' )
						),
                                    'description'         => __( 'This is where you can add new Clasificados.', 'ds8clasificado' ),
                                    'public'              => true,
                                    'register_meta_box_cb' => array('DS8Clasificados','add_pdfcustom_meta_boxes'),
                                    'show_ui'             => true,
                                    'show_in_menu'        => true,
                                    'show_in_nav_menus'   => true,
                                    'capability_type'     => 'post',
                                    'map_meta_cap'        => true,
                                    'publicly_queryable'  => true,
                                    'exclude_from_search' => false,
                                    'hierarchical'        => false,
                                    'menu_icon'           => 'dashicons-feedback',
                                    //'taxonomies'          => array('category'),
                                    'rewrite'             => $clasificado_permalink ? array( 'slug' => untrailingslashit( $clasificado_permalink ), 'with_front' => false, 'feeds' => false ) : false,
                                    'query_var'           => true,
                                    'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'author' ),
                                    'has_archive'         => true,
                                    'show_in_nav_menus'   => true,
                                    'show_in_menu'        => true
				)
			)
		);
	}
        
        public static function register_taxonomies() {
            
		if ( taxonomy_exists( 'clasificado_cat' ) ) {
			return;
		}
                
                $permalinks = get_option( 'ds8clasificado_permalinks' );
                
		register_taxonomy( 'clasificado_cat',
                                 'clasificado',
			apply_filters( 'ds8_taxonomy_args_clasificado_cat', array(
				'hierarchical'          => true,
                                //'update_count_callback' => '_sc_term_recount',
                                //'has_archive'           => true,
				'label'                 => __( 'Clasificado Categories', 'ds8clasificado' ),
                                'show_admin_column' => true,
				'labels' => array(
						'name'              => __( 'Clasificado Categories', 'ds8clasificado' ),
						'singular_name'     => __( 'Clasificado Category', 'ds8clasificado' ),
                                                'menu_name'         => _x( 'Categories', 'Admin menu name', 'ds8clasificado' ),
						'search_items'      => __( 'Search Clasificado Category', 'ds8clasificado' ),
						'all_items'         => __( 'All Categories', 'ds8clasificado' ),
						'parent_item'       => __( 'Parent Clasificado Category', 'ds8clasificado' ),
						'parent_item_colon' => __( 'Parent Clasificado Category:', 'ds8clasificado' ),
						'edit_item'         => __( 'Edit Category', 'ds8clasificado' ),
						'update_item'       => __( 'Update Category', 'ds8clasificado' ),
						'add_new_item'      => __( 'Add new Clasificado Category', 'ds8clasificado' ),
						'new_item_name'     => __( 'New Clasificado Category', 'ds8clasificado' )
					),
				'show_ui'               => true,
				'query_var'             => true,
                                /*,
				'capabilities'          => array(
					'manage_terms' => 'manage_product_terms',
					'edit_terms'   => 'edit_product_terms',
					'delete_terms' => 'delete_product_terms',
					'assign_terms' => 'assign_product_terms',
				),*/
				'rewrite'               => array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'clasificado-category', 'slug', 'ds8clasificado' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);
	}

}

DS8_CPT::init();
