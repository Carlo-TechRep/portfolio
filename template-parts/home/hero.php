<?php
/**
 * Homepage Section: Hero
 */

$featured_post = isset( $args['featured_post'] ) ? $args['featured_post'] : null;
$hero_source   = get_theme_mod( 'navid_hero_source', 'latest' );

if ( ! get_theme_mod( 'navid_hero_show', true ) ) {
    return;
}

$hero_layout = get_theme_mod( 'navid_hero_layout', 'standard' );
if ( has_action( "navid_render_hero_{$hero_layout}" ) ) {
    do_action( "navid_render_hero_{$hero_layout}" );
} else {

    // Determine Hero variables based on source
    $hero_img = ''; $hero_title = ''; $hero_excerpt = ''; $btn_text = ''; $btn_url = ''; $tag_text = ''; $meta_author = ''; $meta_date = ''; $meta_extra = '';

    if ( 'custom' === $hero_source || ! $featured_post ) {
        $hero_img = get_theme_mod( 'navid_hero_image' );
        if ( empty( $hero_img ) ) {
            $hero_img = get_template_directory_uri() . '/assets/images/hero-navid.jpg';
        }
        $hero_title  = get_theme_mod( 'navid_hero_title', 'The Modern <em>Aesthetic</em>' );
        $hero_excerpt = get_theme_mod( 'navid_hero_excerpt', "Discover the world's most fascinating stories, curated for the modern, curious mind. From thought-provoking features to in-depth cultural commentary, every story is crafted to inspire." );
        $btn_text    = get_theme_mod( 'navid_hero_btn_text', 'Read the Story' );
        $btn_url     = get_theme_mod( 'navid_hero_btn_url', '#' );
        $tag_text    = get_theme_mod( 'navid_hero_custom_tag_text', __( 'FEATURED', 'navid' ) );
        $meta_author = get_bloginfo( 'name' );
        $meta_date   = date_i18n( 'F j, Y' );
        $meta_extra  = __( 'Editorial', 'navid' );
    } else {
        // Post Data
        $hero_img = get_the_post_thumbnail_url( $featured_post, 'navid-hero' );
        if ( empty( $hero_img ) ) {
            $hero_img = get_template_directory_uri() . '/assets/images/hero-navid.jpg';
        }
        $hero_title  = get_the_title( $featured_post );
        $hero_excerpt = wp_trim_words( get_the_excerpt( $featured_post ), 60 );
        $btn_text    = __( 'Read the Story', 'navid' );
        $btn_url     = get_permalink( $featured_post );
        
        $cats = get_the_category( $featured_post->ID );
        $cat_name = ! empty( $cats ) ? $cats[0]->name : 'Featured';
        $tag_text    = $cat_name;
        
        $meta_author = get_the_author_meta( 'display_name', $featured_post->post_author );
        $meta_date   = get_the_date( 'F j, Y', $featured_post );
        $meta_extra  = function_exists( 'navid_reading_time' ) ? navid_reading_time( $featured_post->post_content ) : __( 'Featured', 'navid' );
    }

    // Team landing-page message.
    $hero_title   = 'Building the Future, Together.';
    $hero_excerpt = 'Project Manager · System Analyst · Programmer · Technical Writer';
?>
<section class="hero-section">
  <div class="hero-bg">
    <?php if ( $hero_img ) : ?>
      <img class="hero-bg-img"
           src="<?php echo esc_url( $hero_img ); ?>"
           alt="<?php echo esc_attr( wp_strip_all_tags( $hero_title ) ); ?>" />
    <?php endif; ?>
  </div>
  <?php
  $show_editor_picks = get_theme_mod( 'navid_editor_picks_show', true );
  $picks_count  = get_theme_mod( 'navid_editor_picks_count', 9 );
  $picks_source = get_theme_mod( 'navid_editor_picks_source', 'latest' );
  $picks_cat    = get_theme_mod( 'navid_editor_picks_category', 0 );

  $picks_args = array(
      'posts_per_page'      => $picks_count,
      'ignore_sticky_posts' => true,
  );
  if ( 'category' === $picks_source && ! empty( $picks_cat ) ) {
      $picks_args['cat'] = $picks_cat;
  }
  
  $team_highlights = array(
      array( 'category' => 'PROGRAMMING',       'title' => 'Building the Core Features',      'image' => 'exec-aa1bb6ea-49ca-43b3-921a-9ec91000fc18.png' ),
      array( 'category' => 'DATA ANALYTICS',    'title' => 'Turning Data Into Insights',       'image' => 'exec-f5b0f76c-e680-4903-ae64-7ad7d42ba915.png' ),
      array( 'category' => 'TECHNICAL WRITING', 'title' => 'Clear Documentation Matters',      'image' => 'exec-eafec5af-e128-4ce5-8cfa-0fd115287b10.png' ),
      array( 'category' => 'PROJECT MANAGEMENT','title' => 'Keeping the Team on Track',         'image' => 'exec-5418e98b-86f9-4af6-973c-5c53a1a5e973.png' ),
      array( 'category' => 'TEAMWORK',          'title' => 'One Vision, Four Roles',           'image' => 'exec-e608ad41-9437-4190-88c2-9d74b66fd381.png' ),
      array( 'category' => 'UI/UX',             'title' => 'Designing Better Experiences',     'image' => 'exec-e96ffa25-8346-4eac-baf2-056a01f1bfe0.png' ),
      array( 'category' => 'PROBLEM SOLVING',   'title' => 'From Bugs to Breakthroughs',        'image' => 'exec-5309fa74-c37d-4d68-a3ce-705a2a8718c0.png' ),
      array( 'category' => 'RESEARCH',          'title' => 'Exploring New Technologies',       'image' => 'exec-9c1916ea-360d-4fa1-8902-99bb26608bb0.png' ),
      array( 'category' => 'PROJECT SHOWCASE',  'title' => 'What We’re Building Together',      'image' => 'exec-1c936e1c-78ea-4086-8a8a-0582fff08693.png' ),
  );
  $has_editor_picks = $show_editor_picks && ! empty( $team_highlights );
  ?>
  <div class="hero-content hero-grid <?php echo esc_attr( $has_editor_picks ? '' : 'hero-grid-full' ); ?>">
    <div class="hero-main-col">
        <?php $show_hero_tag = false; ?>
        <?php if ( $show_hero_tag && ! empty( $tag_text ) ) : ?>
        <span class="hero-tag">
          <span class="hero-tag-dot"></span>
          <?php echo esc_html( $tag_text ); ?>
        </span>
        <?php endif; ?>
      <h1 class="hero-title"><?php echo wp_kses_post( $hero_title ); ?></h1>
      <p class="hero-excerpt"><?php echo esc_html( $hero_excerpt ); ?></p>
      <div class="hero-bottom">
        <?php if ( $btn_text && $btn_url ) : ?>
        <a href="<?php echo esc_url( $btn_url ); ?>" class="hero-cta">
          <?php echo esc_html( $btn_text ); ?>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="13 6 19 12 13 18"/></svg>
        </a>
        <?php endif; ?>
        <?php $show_hero_meta = false; ?>
        <?php if ( $show_hero_meta ) : ?>
        <span class="hero-meta-inline">
          <span><?php echo esc_html( $meta_author ); ?></span>
          <span class="dot"></span>
          <span><?php echo esc_html( $meta_date ); ?></span>
          <span class="dot"></span>
          <span><?php echo esc_html( $meta_extra ); ?></span>
        </span>
        <?php endif; ?>
      </div>
    </div>

    <?php
    if ( $has_editor_picks ) :
    $ticker_total = count( $team_highlights );
    ?>
    <div class="hero-sidebar-col">
      <div class="hero-sidebar-inner">
        <div class="hero-sidebar-header">
          <h4 class="hero-sidebar-title"><?php esc_html_e( 'Team Highlights', 'navid' ); ?></h4>
          <div class="hero-ticker-controls">
            <button class="hero-ticker-btn hero-ticker-prev" aria-label="<?php esc_attr_e( 'Previous story', 'navid' ); ?>">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="18 15 12 9 6 15"/></svg>
            </button>
            <button class="hero-ticker-btn hero-ticker-next" aria-label="<?php esc_attr_e( 'Next story', 'navid' ); ?>">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
          </div>
        </div>
        <?php 
        $interval = get_theme_mod( 'navid_editor_picks_autoplay', true ) ? '3500' : '0'; 
        $visible_count = get_theme_mod( 'navid_pro_editor_picks_visible', 3 );
        ?>
        <div class="hero-ticker" data-total="<?php echo esc_attr( $ticker_total ); ?>" data-visible="<?php echo esc_attr( $visible_count ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>">
          <div class="hero-ticker-window">
            <div class="hero-ticker-track">
              <?php
              $slide_idx = 0;
              foreach ( $team_highlights as $highlight ) :
              ?>
              <a href="#team-highlights" class="hero-ticker-item has-thumb" data-index="<?php echo esc_attr( $slide_idx ); ?>">
                <div class="hero-ticker-thumb">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/' . $highlight['image'] ); ?>" alt="<?php echo esc_attr( $highlight['title'] ); ?>" loading="lazy" />
                </div>
                <div class="hero-ticker-info">
                  <span class="hero-ticker-cat"><?php echo esc_html( $highlight['category'] ); ?></span>
                  <h5 class="hero-ticker-title"><?php echo esc_html( $highlight['title'] ); ?></h5>
                  <span class="hero-ticker-meta">TechVision Team</span>
                </div>
              </a>
              <?php
                  $slide_idx++;
              endforeach;
              ?>
            </div>
          </div>
          <div class="hero-ticker-progress">
            <div class="hero-ticker-progress-bar"></div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php } // End hero layout override check ?>
