
<div class="<?php echo esc_attr( apply_filters( 'kadence_entry_content_class', 'entry-content single-content' ) ); ?>">
    <?php
    do_action( 'kadence_single_before_entry_content' );

    ?>

    <div class="two-col">
        <div class="col">
            <h1><?php echo get_the_title(); ?></h1>
            <p class="institution">
                <?php echo $args['department']; ?><br/>
                <?php echo $args['institution']; ?>
            </p>
            <?php the_content(); ?>
        </div>
        <div class="col">
            <div class="img-wrap">
                <?php echo $args['photo']; ?>
            </div>
        </div>
    </div>

<?php

    wp_link_pages(
        array(
            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kadence' ),
            'after'  => '</div>',
        )
    );
    do_action( 'kadence_single_after_entry_content' );
    ?>
</div><!-- .entry-content -->
