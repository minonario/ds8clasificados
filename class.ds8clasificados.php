<?php

class DS8Clasificados {

        private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
                        //add_filter( 'load_textdomain_mofile', array('DS8Clasificados','ds8clasificado_textdomain'), 10, 2 );
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
                self::set_locale();
                
                include_once( 'includes/cpt-clasificados.php' );
                DS8_CPT::register_post_types();
                DS8_CPT::register_taxonomies();
                
                add_action('wp_enqueue_scripts', array('DS8Clasificados', 'ds8_clasificados_javascript'), 10);
                add_shortcode( 'ds8clasificado', array('DS8Clasificados', 'ds8clasificado_shortcode_fn') );
                //add_filter( "next_post_link", array('DS8Clasificados','calendar_link'), 10, 5 );
                //add_filter( "previous_post_link", array('DS8Clasificados','calendar_link'), 10, 5 );
                
                add_action('add_meta_boxes', array('DS8Clasificados','add_pdfcustom_meta_boxes'), 10);
                add_action('save_post_clasificado', array('DS8Clasificados','save_pdfcustom_meta_data'));
                add_action('post_edit_form_tag', array('DS8Clasificados','update_edit_form'));
                
                add_filter( 'body_class', array('DS8Clasificados','ds8_modify_body_classes'), 10, 2 );
                //DEPRECATED add_filter('get_image_tag_class', array('DS8Clasificados','ds8_add_image_class'));
                add_filter( 'post_thumbnail_html', array('DS8Clasificados','wpdev_filter_post_thumbnail_html'), 10, 5 );
                add_action( 'pre_get_posts', array('DS8Clasificados','set_posts_per_page_for_clasificados') );
                
                add_filter('single_template', array('DS8Clasificados','load_cpt_template'), 10, 1);
                add_filter('archive_template', array('DS8Clasificados','get_custom_post_type_template'), 10, 1);
	}

        public static function get_custom_post_type_template( $archive_template ) {
             global $post;

             if ( is_post_type_archive ( 'clasificado' ) ) {
                  $archive_template = dirname( __FILE__ ) . '/archive.php';
             }
             return $archive_template;
        }
        
        public static function load_cpt_template($template) {
            global $post;

            
            // Is this a "my-custom-post-type" post?
            if ($post->post_type == "clasificado"){

                //Your plugin path 
                $plugin_path = plugin_dir_path( __FILE__ );

                // The name of custom post type single template
                $template_name = 'singular.php';

                // A specific single template for my custom post type exists in theme folder? Or it also doesn't exist in my plugin?
                if($template === get_stylesheet_directory() . '/' . $template_name
                    || !file_exists($plugin_path . $template_name)) {

                    //Then return "single.php" or "single-my-custom-post-type.php" from theme directory.
                    return $template;
                }

                // If not, return my plugin custom post type template.
                return $plugin_path . $template_name;
            }

            //This is not my custom post type, do nothing with $template
            return $template;
        }
        
        public static function ds8clasificado_shortcode_fn($atts) {
          
          if (is_admin()) return;
          
          extract( shortcode_atts( array(
              'type' => 'clasificado',
              'perpage' => 3
          ), $atts ) );
          $output = '<div class="clasificado-main">';
          
          echo '<div class="loading visible loader oculto">
              <div role="status" class="spinner-border center-block loadingp">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>';
          
          $cat_slug = get_query_var( 'clasificado_cat' );
          wp_dropdown_categories(array('class' => 'filtercat', 'taxonomy'=> 'clasificado_cat', 'selected'=>$cat_slug, 'show_option_none'=> 'Todos', 'hide_empty' => 0, 'name' => 'listofoptions', 'value_field' => 'slug' ));
          ?>
          <script>
            document.getElementById('listofoptions').onchange = function(){
              
              jQuery('.entry-content').find('.visible').removeClass('oculto');
              
              const params = new URLSearchParams(window.location.search);
              const url = window.location.href.split('?')[0];
              
              console.log('destination='+url);
              
              if( this.value !== '-1' ){
                window.location=url+'?clasificado_cat='+this.value
              }else{
                window.location=url
              }
            }
          </script>
          <?php
          
          if (!empty($cat_slug)){
            $tax_query = array('tax_query' => array(
                      array(
                              'taxonomy' => 'clasificado_cat',
                              'field'    => 'slug',
                              'terms'    => $cat_slug,
                      ),
              ));
          }
          
          $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
          $args = array(
              'post_type' => $type,
              'posts_per_page' => -1,
              'paged' => $paged
          );
          
          if (isset($tax_query)) { 
            $args += $tax_query;
          }
          
          $g_query = new  WP_Query( $args );
          $GLOBALS['wp_query']->max_num_pages = $g_query->max_num_pages;
          while ( $g_query->have_posts() ) : $g_query->the_post();
              //$output .= '<article class="ds8-article post-94 clasificado type-clasificado status-publish has-post-thumbnail hentry">'.
              $ds8_media = ds8_get_post_media( 'image' );
              $output .= '<div class="clasificado"><div class="post-thumb entry-media thumbnail">';

                              $output .= sprintf(
                                      '<a href="%1$s" class="entry-image-link">%2$s</a>',
                                      esc_url( ds8_entry_get_permalink() ),
                                      $ds8_media
                              );
                                              
                                    $args = array();
                                    $defaults = array(
                                            'show_published' => true,
                                            'show_modified'  => false,
                                            'modified_label' => esc_html__( 'Last updated on', 'sinatra' ),
                                            'date_format'    => 'F d, Y',
                                            'before'         => '<span class="posted-on">',
                                            'after'          => '</span>',
                                    );
                                    $args = wp_parse_args( $args, $defaults );

                                    $time_string = '<time class="entry-date published updated" datetime="%1$s"%2$s>%3$s</time>';
                                    $args['modified_label'] = $args['modified_label'] ? $args['modified_label'] . ' ' : '';

                                    $time_string = sprintf(
                                            $time_string,
                                            esc_attr( get_the_date( DATE_W3C ) ),
                                            '',
                                            esc_html( get_the_date( $args['date_format'] ) ),
                                            esc_attr( get_the_modified_date( DATE_W3C ) ),
                                            '',
                                            esc_html( $args['modified_label'] ) . esc_html( get_the_modified_date( $args['date_format'] ) )
                                    );

                                    $output .= wp_kses(
                                            sprintf(
                                                    '%1$s%2$s%3$s',
                                                    $args['before'],
                                                    $time_string,
                                                    $args['after'],
                                            ),
                                            ds8_get_allowed_html_tags()
                                    );


                         $output .= sprintf(
                                      '<h2 class="entry-title" itemprop="headline"><a href="%2$s" class="linkclasi" title="%1$s" rel="bookmark">%1$s</a></h2>',
                                      get_the_title(),
                                      esc_url( ds8_entry_get_permalink() )
                              );
                         $output .= '</div></div>';
          endwhile;
          $output .= '</div>';
          $output .= get_the_posts_pagination(array(
					'mid_size'  => 2
              ));
          wp_reset_query();
          return $output;
          
        }
        
        public static function set_posts_per_page_for_clasificados( $query ) {
            if ( !is_admin() && $query->is_main_query() && is_post_type_archive( 'clasificado' ) ) {
              $query->set( 'posts_per_page', '3' );
            }
        }
        
        public static function ds8_modify_body_classes( $classes, $class ) {
            // Modify the array $classes to your needs
            if( is_archive() && is_post_type_archive('clasificado') )
            {
                $classes[] = 'woocommerce';
                $classes[] = 'woocommerce-page';
            }    
            return $classes;
        }
        

        /**
        * Link thumbnails to their posts based on attr
        *
        * @param $html
        * @param int $pid
        * @param int $post_thumbnail_id
        * @param int $size
        * @param array $attr
        *
        * @return string
        */
        public static function wpdev_filter_post_thumbnail_html( $html, $pid, $post_thumbnail_id, $size, $attr ) {

                if ( ! empty( $attr[ 'itemprop' ] ) && $attr['itemprop'] === 'image' ) {
                      
                      $image = wp_get_attachment_image_src( $post_thumbnail_id, "full" );

                      if ($image !== false){
                        $html = sprintf(
                                '<span data-src="%s" title="%s" class="custom-lightbox">%s</span>',
                                $image[0], //get_permalink( $pid ),
                                esc_attr( get_the_title( $pid ) ),
                                $html
                        );
                      }
                      else{
                        return;
                      }
                }

               return $html;
        }
        
        public static function ds8_add_image_class($class){
            if ('clasificado' == get_post_type()) $class .= ' additional-class';
            return $class;
        }
        
        public static function update_edit_form() {
            echo ' enctype="multipart/form-data"';
        }
        
        public static function add_pdfcustom_meta_boxes() {  
            add_meta_box('wp_custom_attachment', 'Guideline Pdf Upload', array('DS8Clasificados','wp_custom_attachment'), 'clasificado', 'normal', 'default');  
        }

        public static function wp_custom_attachment() {
            wp_nonce_field( 'ds8_inner_custom_box' , 'wp_custom_attachment_nonce');
            
            global $wp_query;
            //$custom = get_post_custom($wp_query->post->ID);
            $custom = get_post_meta( get_the_ID(), 'wp_custom_attachment', true );
            
            $html = '<p class="description">';
            // $html .= 'Upload your PDF here.';
            $html .= '</p>';
            $html .= '<label for="wp_custom_attachment">Selecciona archivo</label>';
            $html .= '<input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="" size="25">';
            if (!empty($custom)){
              $html .= "El archivo es: ".$custom['url'];
            }
            echo $html;
        }

        public static function save_pdfcustom_meta_data($id) {

            if (!isset($_POST['wp_custom_attachment_nonce'])) {
              return $id;
            }
            if(!wp_verify_nonce($_POST['wp_custom_attachment_nonce'], 'ds8_inner_custom_box')) {
              return $id;
            }
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
              return $id;
            }
             if('page' == $_POST['post_type']) {
              if(!current_user_can('edit_page', $id)) {
                return $id;
              }
            } else {
                if(!current_user_can('edit_page', $id)) {
                    return $id;
                }
            }

            if(!empty($_FILES['wp_custom_attachment']['name'])) {

                $supported_types = array('application/pdf',"image/jpeg","image/png");
                // Get the file type of the upload
                $arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));
                $uploaded_type = $arr_file_type['type'];

                if(in_array($uploaded_type, $supported_types)) {

                    // Use the WordPress API to upload the file
                    $upload = wp_upload_bits($_FILES['wp_custom_attachment']['name'], null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));

                    if(isset($upload['error']) && $upload['error'] != 0) {
                        wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                    } else {
                        add_post_meta($id, 'wp_custom_attachment', $upload);
                        update_post_meta($id, 'wp_custom_attachment', $upload);     
                    }

                } else {
                    wp_die("The file type that you've uploaded is not a PDF.");
                }
            }
        }
        
        /**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0
	 */
	private static function set_locale() {
		load_plugin_textdomain( 'ds8clasificado', false, plugin_dir_path( dirname( __FILE__ ) ) . '/languages/' );

	}
        
        public static function ds8clasificado_textdomain( $mofile, $domain ) {
                if ( 'ds8clasificado' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
                        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
                        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
                }
                return $mofile;
        }
        
        
        /**
	 * Check if plugin is active
	 *
	 * @since    1.0
	 */
	private static function is_plugin_active( $plugin_file ) {
		return in_array( $plugin_file, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

        public static function ds8_clasificados_javascript(){
          
            wp_enqueue_style('ds8clasificado-css', plugin_dir_url( __FILE__ ) . 'assets/lightGallery/lightgallery-bundle.min.css', array(), DS8CLASIFICADOS_VERSION);
            wp_enqueue_style('anuncios-css', plugin_dir_url( __FILE__ ) . 'assets/css/anuncios.css', array(), DS8CLASIFICADOS_VERSION);
            wp_register_script( 'front-ds8clasificado-js', plugin_dir_url( __FILE__ ) . 'assets/lightGallery/lightgallery.min.js', array('jquery'), DS8CLASIFICADOS_VERSION, true );
            wp_enqueue_script( 'front-ds8clasificado-js' );
            
            wp_enqueue_script( 'front-lgzoom-js', plugin_dir_url( __FILE__ ) . 'assets/lightGallery/lg-zoom.min.js', array('jquery','front-ds8clasificado-js'), DS8CLASIFICADOS_VERSION, true );
            wp_enqueue_script( 'front-thumbnail-js', plugin_dir_url( __FILE__ ) . 'assets/lightGallery/lg-thumbnail.min.js', array('jquery','front-ds8clasificado-js'), DS8CLASIFICADOS_VERSION, true );
            
            //wp_register_script( 'masonry-pkgd-min-js', plugin_dir_url( __FILE__ ) . 'assets/js/masonry.pkgd.min.js', array('jquery'), DS8CLASIFICADOS_VERSION, true );
            //wp_enqueue_script( 'masonry-pkgd-min-js' );
            
            wp_register_script( 'anuncios.js', plugin_dir_url( __FILE__ ) . 'assets/js/anuncios.js', array('jquery','jquery-ui-tooltip'), DS8CLASIFICADOS_VERSION, true );
            wp_enqueue_script( 'anuncios.js' );

        }

        public static function view( $name, array $args = array() ) {
                $args = apply_filters( 'ds8clasificado_view_arguments', $args, $name );

                foreach ( $args AS $key => $val ) {
                        $$key = $val;
                }

                load_plugin_textdomain( 'ds8clasificado' );

                $file = DS8CLASIFICADOS_PLUGIN_DIR . 'views/'. $name . '.php';

                include( $file );
	}
        
        public static function plugin_deactivation( ) {
            unregister_post_type( 'calendar' );
            flush_rewrite_rules();
        }

        /**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], DS8CLASIFICADOS_MINIMUM_WP_VERSION, '<' ) ) {
			load_plugin_textdomain( 'ds8clasificado' );
                        
			$message = '<strong>'.sprintf(esc_html__( 'FD Estadisticas %s requires WordPress %s or higher.' , 'ds8clasificado'), DS8CLASIFICADOS_VERSION, DS8CLASIFICADOS_MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'ds8clasificado'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/ds8clasificado/download/');

			DS8Clasificados::bail_on_activation( $message );
		} elseif ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php' ) ) {
                        flush_rewrite_rules();
			add_option( 'Activated_DS8Clasificados', true );
		}
	}

        private static function bail_on_activation( $message, $deactivate = true ) {
?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<style>
* {
	text-align: center;
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
}
p {
	margin-top: 1em;
	font-size: 18px;
}
</style>
</head>
<body>
<p><?php echo esc_html( $message ); ?></p>
</body>
</html>
<?php
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$ds8clasificado = plugin_basename( DS8CALENDAR__PLUGIN_DIR . 'ds8clasificado.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $ds8clasificado ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}

}