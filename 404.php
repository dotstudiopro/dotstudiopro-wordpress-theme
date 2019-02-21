<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0.0
 */
get_header();
?>

<div id="primary" class="content-area container">
    <main id="main" class="site-main" role="main">

        <section class="error-404 not-found text-center pt-5">
            <div class="align-middle">
                <div class="page-header">
                    <h2>404 Not Found</h2>
                </div><!-- .page-header -->

                <div class="page-content">
                    <p>The page you requested was not found on this server. If you entered the URL manually please check your spelling and try again.</p>
                    <a class="btn cc-readmore" href="/">Take Me Home</a>
                </div><!-- .page-content -->
            </div>
        </section><!-- .error-404 -->

    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
