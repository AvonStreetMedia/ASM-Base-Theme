<?php
/**
 * Template part for displaying posts
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if ( is_singular() ) :
            the_title( '<h1 class="entry-title">', '</h1>' );
        else :
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;

        if ( 'post' === get_post_type() ) :
            ?>
            <div class="entry-meta">
                <?php
                echo 'Posted on ' . get_the_date();
                ?>
            </div>
        <?php endif; ?>
    </header>

    <?php if ( has_post_thumbnail() && !is_singular() ) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        if ( is_singular() ) :
            the_content();
        else :
            the_excerpt();
            ?>
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php echo esc_html__( 'Read More', 'custom-theme' ); ?>
            </a>
            <?php
        endif;
        ?>
    </div>

    <footer class="entry-footer">
        <?php
        if ( 'post' === get_post_type() ) {
            $categories_list = get_the_category_list( esc_html__( ', ', 'custom-theme' ) );
            if ( $categories_list ) {
                printf( '<span class="cat-links">%1$s</span>', $categories_list );
            }
            
            $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'custom-theme' ) );
            if ( $tags_list ) {
                printf( '<span class="tags-links">%1$s</span>', $tags_list );
            }
        }
        ?>
    </footer>
</article>
