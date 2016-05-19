<?php
/**
 * Template name: Support
 */

 get_header(); ?>

 <div class="wrapper support-page-wrapper">

 <?php if (have_posts()) : ?>



	<?php while (have_posts()) : the_post(); 

	//Support Blocks
	$boxes 			= get_theme_mod( 'support_boxes', array() );

	// Documentation Button
	$doc_button_label 		= get_theme_mod( 'revive_support_doc_button_label', __('View Full Documentation', 'revivewp') );
	$doc_button_url 		= get_theme_mod( 'revive_support_doc_button_url' );

	// Request Button
	$request_button_label 	= get_theme_mod( 'revive_support_request_button_label', __('Submit Support Request', 'revivewp') );
	?>

	<div class="page_title"><?php the_title(); ?>. 
		<span><?php the_excerpt(); ?></span>
	</div><!-- / .page_title -->

	<section id="support-page">

		<div class="support-page-boxes">
		<?php foreach ( $boxes as $box ) { ?>
			<div class="support-box">
				<a href="<?php echo $box['url'] ?>" class="support-section-link">
					<h4 class="support-section-title"><?php echo $box['title'] ?></h4>
					<p class="support-section-description"><?php echo $box['description'] ?></p>
				</a>
			</div>
		<?php } ?>
			<div class="cf"></div><!-- / .cf -->
		</div>
		
		<div class="support-page-buttons">

			<?php if ( $doc_button_label && $doc_button_url  ) { ?>
				<a class="edd-docs-link orange-button button" href="<?php echo $doc_button_url ?>"><?php echo $doc_button_label ?></a>
			<?php } ?>

			<?php if ( $request_button_label ) { ?>
				<a id="request-ticket" class="button" href="#"><?php echo $request_button_label ?></a>
			<?php } ?>

		</div>

		<div class="cf"></div><!-- / .cf -->

	</section><!-- /#contactpage  -->

	<div class="support-form" style="display:none;">
		<?php the_content(); ?>
	</div>
	

	<?php endwhile; ?>

<?php else : ?>

<?php endif; ?>

</div><!-- / .wrapper -->

<?php get_footer(); ?>