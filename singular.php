<?php
/**
 * The template for displaying all pages, single posts and attachments.
 *
 * This is a new template file that WordPress introduced in
 * version 4.3.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<?php get_header(); ?>

<div class="si-container">

	<div id="primary" class="content-area grid-75 grid-parent">

		<?php do_action( 'sinatra_before_content' ); ?>

		<main id="contentmain" class="site-content">

			<?php
			if ( have_posts() ) :
                                while ( have_posts() ) :
                                        the_post(); ?>

                                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'sinatra-article' ); ?>>
                                          
                                        <div class="td-post-header">
                                            <header class="td-post-title">
                                            <h1 class="entry-title"><?php the_title(); ?></h1>
                                            <div class="metadate">
                                              <?php
                                                $args = array();
                                                $defaults = array(
                                                        'show_published' => true,
                                                        'show_modified'  => false,
                                                        'modified_label' => esc_html__( 'Last updated on', 'sinatra' ),
                                                        'date_format'    => '',
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
                                                
                                                echo wp_kses(
                                                        sprintf(
                                                                '%1$s%2$s%3$s',
                                                                $args['before'],
                                                                $time_string,
                                                                $args['after'],
                                                        ),
                                                        ds8_get_allowed_html_tags()
                                                );
                                              ?>
                                            </div>
                                            </header>
                                        </div>

                                          <?php
                                              echo render_image();
                                          ?>
                                          
                                          <?php
                                              the_content();
                                          ?>

                                        </article><!-- #post-<?php the_ID(); ?> -->

                                <?php
                                
                                endwhile;
                                else :
                                        get_template_part( 'template-parts/content/content', 'none' );
                        endif;
                        
                        include_once('template-parts/entry-prev-next-post.php' );
			?>

		</main><!-- #content .site-content -->

	</div><!-- #primary .content-area -->
        <div class="secundario">
            <?php get_sidebar(); ?>
        </div>

</div><!-- END .si-container -->

<?php
get_footer();
