<?php
get_header();
?>
<div class="container container--narrow content-area">
    <?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-page'); ?>>
        <header class="page-header mb-32">
            <h1 class="text-headline-large"><?php the_title(); ?></h1>
        </header>
        <div class="page-content entry-content">
            <?php the_content(); ?>
        </div>
        <?php if (comments_open() || get_comments_number()) comments_template(); ?>
    </article>
    <?php endwhile; ?>
</div>
<?php get_footer(); ?>
