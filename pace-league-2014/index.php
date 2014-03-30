<?php get_header(); ?>

<div id="content-wrapper"  class="clearfix content-wrapper row">

    <div class="col-lg-12" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="post-header">

                    <h1 class="post-title"><?php the_title(); ?></h1>

                </header>

                <div class="body text">

                    <?php the_content(); ?>

                </div>

            </article>

        <?php endwhile; ?>

    </div>


</div>

<?php get_footer(); ?>

