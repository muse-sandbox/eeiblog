<?php
/**
 * Custom search form.
 */
$unique_id = esc_attr( uniqid( 'search-form-' ) );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label for="<?php echo $unique_id; ?>" class="screen-reader-text">
        <?php esc_html_e( 'Search for:', 'eeiblog' ); ?>
    </label>
    <input type="search"
           id="<?php echo $unique_id; ?>"
           class="search-field"
           placeholder="<?php esc_attr_e( 'Search…', 'eeiblog' ); ?>"
           value="<?php echo esc_attr( get_search_query() ); ?>"
           name="s"
           autocomplete="off">
    <button type="submit" class="search-submit">
        <?php esc_html_e( 'Search', 'eeiblog' ); ?>
    </button>
</form>
