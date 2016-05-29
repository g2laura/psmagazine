<?php
/**
 * The template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php custom_breadcrumbs(); ?>
				<?php get_template_part( 'content', get_post_format() ); ?>
				<?php
					$tag_list = get_the_tag_list( '', __( ' ', 'twentythirteen' ) );
					if ( $tag_list ) {
						echo '<div class="tags"><b>Tags: </b><span class="tags-links">' . $tag_list . '</span></div>';
					}
				?>
				<?php echo do_shortcode( '[AuthorRecommendedPosts]' ); ?>
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>