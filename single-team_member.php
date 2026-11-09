<?php
/** Single editable team profile. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
    $fields = array( 'role', 'birthdate', 'age', 'religion', 'elementary_school', 'high_school', 'college', 'skills', 'ambition', 'hobby_image_1', 'hobby_image_2', 'hobby_image_3', 'hobby_image_4', 'image' );
    $data = array();
    foreach ( $fields as $field ) { $data[ $field ] = get_post_meta( get_the_ID(), '_navid_team_' . $field, true ); }
    $image_url = $data['image'] ? get_template_directory_uri() . '/assets/images/team/' . $data['image'] : '';
    $hobby_image_urls = array();
    foreach ( array( 'hobby_image_1', 'hobby_image_2', 'hobby_image_3', 'hobby_image_4' ) as $hobby_key ) {
        if ( ! $data[ $hobby_key ] ) { continue; }
        $hobby_image_url = $data[ $hobby_key ];
        if ( ! wp_http_validate_url( $hobby_image_url ) ) {
            $hobby_image_url = get_template_directory_uri() . '/assets/images/team/' . ltrim( $hobby_image_url, '/' );
        }
        $hobby_image_urls[] = $hobby_image_url;
    }
?>
<main class="team-resume-page"><div class="team-resume-page__inner">
  <section class="team-resume-card">
    <?php if ( $image_url ) : ?><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>"><?php endif; ?>
    <div><span><?php echo esc_html( $data['role'] ); ?></span><h1><?php the_title(); ?></h1><?php the_content(); ?></div>
  </section>
  <section class="team-resume-details">
    <?php foreach ( array( 'Birthdate' => 'birthdate', 'Age' => 'age', 'Religion' => 'religion', 'Elementary School' => 'elementary_school', 'High School' => 'high_school', 'College' => 'college', 'Skills' => 'skills', 'Ambition' => 'ambition' ) as $label => $key ) : ?>
      <article><h2><?php echo esc_html( $label ); ?></h2><p><?php echo $data[ $key ] ? esc_html( $data[ $key ] ) : esc_html__( 'To be added', 'navid' ); ?></p></article>
    <?php endforeach; ?>
  </section>
  <section class="team-resume-favorites">
    <h2><?php esc_html_e( 'Hobbies', 'navid' ); ?></h2>
    <?php if ( $hobby_image_urls ) : ?><div class="team-resume-hobby-images"><?php foreach ( $hobby_image_urls as $hobby_image_url ) : ?><img src="<?php echo esc_url( $hobby_image_url ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s hobby', 'navid' ), get_the_title() ) ); ?>"><?php endforeach; ?></div><?php endif; ?>
  </section>
  <button class="team-resume-return" type="button" data-return-fallback="<?php echo esc_url( home_url( '/#team-gallery' ) ); ?>"><?php esc_html_e( 'Return', 'navid' ); ?></button>
</div></main>
<?php endwhile; get_footer(); ?>
