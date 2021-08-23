<?php

/**
 * Single post partial template
 *
 *
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<header class="entry-header">

		<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_content();
			do_action('isceb_wiki_after_content',get_the_id())
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->