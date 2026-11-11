<?php
/**
 * Front Page Template
 *
 * @package Navid
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If a static page is set as front page and user disabled magazine sections on static page
if ( 'page' === get_option( 'show_on_front' ) && ! get_theme_mod( 'navid_frontpage_sections_on_static', true ) ) {
    get_template_part( 'page' );
    return;
}

get_header();

// Get featured post (sticky or latest)
$hero_source = get_theme_mod( 'navid_hero_source', 'latest' );

$featured = null;
if ( 'latest' === $hero_source ) {
    $featured = get_posts( array( 'posts_per_page' => 1, 'ignore_sticky_posts' => true ) );
} elseif ( 'sticky' === $hero_source || 'custom' === $hero_source ) {
    $featured = get_posts( array(
        'posts_per_page' => 1,
        'post__in'       => get_option( 'sticky_posts' ),
    ) );
    if ( empty( $featured ) ) {
        $featured = get_posts( array( 'posts_per_page' => 1 ) );
    }
}
$featured_post = ! empty( $featured ) ? $featured[0] : null;

$default_layout = array(
    'hero',
    'marquee',
    'categories',
    'shortcode',
    'custom',
    'feed',
    'podcast',
    'featured',
    'interlude',
    'masonry',
    'newsletter',
    'social-proof',
    'services',
);

$layout = get_theme_mod( 'navid_homepage_layout', $default_layout );
if ( is_string( $layout ) ) {
    $layout = explode( ',', $layout );
}

if ( is_array( $layout ) ) {
    $layout = array_filter( array_map( 'trim', $layout ) );

    // If the database had an empty string, array_filter makes it empty. Fallback to default.
    if ( empty( $layout ) ) {
        $layout = $default_layout;
    }

    // The scrolling ticker is not used on this site.
    $layout = array_values( array_filter( $layout, function( $section_id ) {
        return 'marquee' !== str_replace( '_', '-', $section_id );
    } ) );

    // If layout was saved before newer sections were added, append them at the end.
    if ( ! in_array( 'social-proof', $layout, true ) && ! in_array( 'social_proof', $layout, true ) ) {
        $layout[] = 'social-proof';
    }
    if ( ! in_array( 'services', $layout, true ) ) {
        $layout[] = 'services';
    }

    // This site uses a focused one-page layout. Keep the custom hero and the
    // About/Contact sections below; omit the theme's magazine demo sections.
    $layout = array( 'hero' );

    $static_content_pos = 'hide';
    $rendered_static    = false;

    // Static Page Content: above_hero
    if ( 'above_hero' === $static_content_pos && function_exists( 'navid_render_frontpage_static_content' ) ) {
        navid_render_frontpage_static_content();
        $rendered_static = true;
    }

    // Determine when to inject below_hero: after marquee if present, otherwise after hero.
    $has_marquee     = in_array( 'marquee', $layout, true );
    $hero_break_slug = $has_marquee ? 'marquee' : 'hero';

    foreach ( $layout as $section_id ) {
        $template_slug = str_replace( '_', '-', trim( $section_id ) );

        // If below_sections, inject right before newsletter
        if ( 'below_sections' === $static_content_pos && ! $rendered_static && 'newsletter' === $template_slug && function_exists( 'navid_render_frontpage_static_content' ) ) {
            navid_render_frontpage_static_content();
            $rendered_static = true;
        }

        get_template_part( 'template-parts/home/' . esc_attr( $template_slug ), null, array( 'featured_post' => $featured_post ) );

        // If below_hero, inject right after hero/marquee
        if ( 'below_hero' === $static_content_pos && ! $rendered_static && $template_slug === $hero_break_slug && function_exists( 'navid_render_frontpage_static_content' ) ) {
            navid_render_frontpage_static_content();
            $rendered_static = true;
        }
    }

    // Fallback if target section wasn't in layout
    if ( ! $rendered_static && 'hide' !== $static_content_pos && function_exists( 'navid_render_frontpage_static_content' ) ) {
        navid_render_frontpage_static_content();
    }
}

/* One-page navigation sections displayed at the end of the homepage. */
$about_page   = get_page_by_path( 'about' );
$contact_page = get_page_by_path( 'contact' );
$team_members = array(
    array( 'role' => 'Project Manager', 'name' => 'Wilan Jomart J. Satur', 'tagline' => 'Leading with Vision, Managing with Purpose, and Turning Every Challenge into an Opportunity for Success.', 'skills' => 'Project planning, team coordination, and clear communication.', 'story' => 'Wilan enjoys bringing people and ideas together, turning a shared goal into a focused and supportive team effort.', 'ambition' => 'To lead projects that make technology more useful and accessible for the community.', 'image' => 'Wilan Jomart J. Satur/wilan-jomart-j-satur.png', 'service_image' => 'Wilan Jomart J. Satur/project-manager.png' ),
    array( 'role' => 'System Analyst', 'name' => 'Jhon Phillip V. Polison', 'tagline' => 'Understanding Complex Problems, Discovering Smarter Possibilities, and Designing Solutions That Make a Difference.', 'skills' => 'Requirements analysis, process design, and problem solving.', 'story' => 'Jhon is drawn to understanding how things work and finding thoughtful ways to make systems clearer and more reliable.', 'ambition' => 'To design practical digital solutions that solve real-world needs.', 'image' => 'Jhon Phillip V. Polison/john-phillip-v-polison.jpg', 'service_image' => 'Jhon Phillip V. Polison/system-analyst.png' ),
    array( 'role' => 'Programmer', 'name' => 'Carlo A. Nacua', 'tagline' => 'Transforming Creative Ideas into Powerful Code, Building Innovative Solutions, and Bringing Technology to Life.', 'skills' => 'Web development, programming logic, and debugging.', 'story' => 'Carlo enjoys the patient craft of turning an idea into working code, one detail and improvement at a time.', 'ambition' => 'To build dependable software that creates meaningful everyday impact.', 'image' => 'Carlo A. Nacua/carlo-a-nacua.png', 'service_image' => 'Carlo A. Nacua/programmer.png' ),
    array( 'role' => 'Technical Writer', 'name' => 'Jay Vee C. Bahian', 'tagline' => 'Turning Complex Technical Concepts into Clear Communication, Connecting Innovation with Understanding, and Making Knowledge Accessible to Everyone.', 'skills' => 'Technical documentation, research, and concise writing.', 'story' => 'Jay Vee believes strong communication makes good technology easier to understand, use, and trust.', 'ambition' => 'To make complex information clear and helpful for every kind of reader.', 'image' => 'Jay Vee C. Bahian/Jay-vee-c-bahian.png', 'service_image' => 'Jay Vee C. Bahian/technical-writer.png' ),
);

// Keep the public ID cards in sync with the editable Team Profile details.
foreach ( $team_members as &$member ) {
    $profile = get_page_by_path( sanitize_title( $member['name'] ), OBJECT, 'team_member' );
    foreach ( array( 'id_number', 'address', 'email', 'facebook' ) as $field ) {
        $member[ $field ] = $profile ? get_post_meta( $profile->ID, '_navid_team_' . $field, true ) : '';
    }
}
unset( $member );
?>

<section class="navid-one-page-section navid-home-team-section" id="home-team" aria-labelledby="home-team-heading">
  <div class="container navid-team-scroll-stage">
    <div class="navid-one-page-heading">
      <h2 id="home-team-heading"><span class="navid-team-heading-vision"><?php esc_html_e( 'Our Team', 'navid' ); ?></span></h2>
    </div>
    <div class="team-id-grid navid-home-team-grid">
      <?php foreach ( $team_members as $member_index => $member ) : ?>
        <?php
        $profile_image = dirname( $member['image'] ) . '/profile.png';
        $image_path    = get_template_directory() . '/assets/images/team/' . $profile_image;
        ?>
        <article class="team-id-card team-card-fall" style="--team-card-index: <?php echo esc_attr( $member_index ); ?>" tabindex="0" role="button" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( __( 'Show ID details for %s', 'navid' ), $member['name'] ) ); ?>">
          <div class="team-id-card-inner">
            <div class="team-id-face team-id-front">
              <span class="team-id-role"><?php echo esc_html( $member['role'] ); ?></span>
              <?php if ( file_exists( $image_path ) ) : ?>
                <img class="team-id-photo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/team/' . $profile_image ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy" />
              <?php else : ?>
                <div class="team-id-mark" aria-hidden="true">TV</div>
              <?php endif; ?>
              <h3><?php echo esc_html( $member['name'] ); ?></h3>
              <span class="team-id-hint team-id-click-cue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 11V5a2 2 0 0 1 4 0v6m0-3a2 2 0 0 1 4 0v3m0-1a2 2 0 0 1 4 0v3c0 4.4-3.6 8-8 8h-1c-2.8 0-5.1-1.4-6.5-3.8L4 14.5a2 2 0 0 1 3.4-2L9 14" /></svg><?php esc_html_e( 'Click to view ID', 'navid' ); ?></span>
            </div>
            <div class="team-id-face team-id-back">
              <span class="team-id-role"><?php echo esc_html( $member['role'] ); ?></span>
              <dl class="team-id-details">
                <div><dt><?php esc_html_e( 'Name', 'navid' ); ?></dt><dd><?php echo esc_html( $member['name'] ); ?></dd></div>
                <div><dt><?php esc_html_e( 'ID Number', 'navid' ); ?></dt><dd><?php echo esc_html( $member['id_number'] ); ?></dd></div>
                <div><dt><?php esc_html_e( 'Address', 'navid' ); ?></dt><dd><?php echo esc_html( $member['address'] ); ?></dd></div>
              </dl>
              <span class="team-id-hint"><?php esc_html_e( 'Click to return', 'navid' ); ?></span>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="navid-one-page-section navid-services-section" id="services" aria-labelledby="services-heading">
  <div class="container">
    <div class="navid-one-page-heading">
      <span><?php esc_html_e( 'What We Do', 'navid' ); ?></span>
      <h2 id="services-heading"><?php esc_html_e( 'Skills That Move Projects Forward', 'navid' ); ?></h2>
    </div>
    <div class="navid-services-grid">
      <?php
      $service_descriptions = array(
          'I lead the team with clear goals, organized plans, and effective communication.',
          'I analyze user needs and turn system requirements into practical solutions.',
          'I build reliable applications with efficient code and practical problem-solving.',
          'I make technical information clear, accurate, and easy to understand.',
      );
      foreach ( $team_members as $service_index => $member ) :
          $service_image_path = get_template_directory() . '/assets/images/team/' . $member['service_image'];
          $service_image_url  = get_template_directory_uri() . '/assets/images/team/' . $member['service_image'];
      ?>
        <article class="navid-service-card" aria-label="<?php echo esc_attr( $member['role'] ); ?>">
          <?php if ( file_exists( $service_image_path ) ) : ?>
            <img class="navid-service-photo" src="<?php echo esc_url( add_query_arg( 'ver', (string) filemtime( $service_image_path ), $service_image_url ) ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy" />
          <?php endif; ?>
          <div class="navid-service-card-content">
            <span class="navid-service-number"><?php echo esc_html( str_pad( (string) ( $service_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
            <h3><?php echo esc_html( $member['role'] ); ?></h3>
            <span class="navid-service-member"><?php echo esc_html( $member['name'] ); ?></span>
            <span class="navid-service-tagline"><?php echo esc_html( $member['tagline'] ); ?></span>
            <p><?php echo esc_html( $service_descriptions[ $service_index ] ); ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="navid-one-page-section navid-project-section" id="projects" aria-labelledby="projects-heading">
  <div class="container">
    <div class="navid-project-showcase">
      <div class="navid-project-copy">
        <span><?php esc_html_e( 'Featured Project', 'navid' ); ?></span>
        <p id="projects-heading"><?php esc_html_e( 'We created this system for our client, the owner of an event rental business. It was designed to support their business needs with a clear and easy-to-use digital experience.', 'navid' ); ?></p>
      </div>
      <div class="navid-project-visuals" aria-label="<?php esc_attr_e( 'Event rental system screenshots', 'navid' ); ?>">
        <?php
        $project_image_path = get_template_directory() . '/assets/images/team/project.png';
        if ( file_exists( $project_image_path ) ) :
            $project_image_url = add_query_arg( 'ver', (string) filemtime( $project_image_path ), get_template_directory_uri() . '/assets/images/team/project.png' );
        ?>
          <figure class="navid-project-screen is-full">
            <img src="<?php echo esc_url( $project_image_url ); ?>" alt="<?php esc_attr_e( 'Event rental system screenshot', 'navid' ); ?>" loading="lazy" />
          </figure>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="navid-one-page-section navid-about-section" id="about" aria-labelledby="about-heading">
  <div class="container">
    <div class="navid-one-page-heading">
      <span><?php esc_html_e( 'About Us', 'navid' ); ?></span>
      <h2 id="about-heading"><?php esc_html_e( 'Meet the Team', 'navid' ); ?></h2>
    </div>
    <div class="navid-one-page-content">
      <?php
      if ( $about_page && ! empty( trim( $about_page->post_content ) ) ) {
          echo apply_filters( 'the_content', $about_page->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      } else {
          echo '<p>' . esc_html__( 'We are a collaborative team bringing project management, data analytics, programming, and technical writing together to build better digital solutions.', 'navid' ) . '</p>';
      }
      ?>
    </div>
    <?php if ( false ) : // Replaced by the unified team portrait below. ?>
    <div class="team-id-grid navid-about-profile-grid">
      <?php foreach ( $team_members as $member ) : ?>
        <?php $image_path = get_template_directory() . '/assets/images/team/' . $member['image']; ?>
        <article class="team-id-card anim-up" tabindex="0" role="button" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( __( 'Show profile details for %s', 'navid' ), $member['name'] ) ); ?>">
          <div class="team-id-card-inner">
            <div class="team-id-face team-id-front">
              <span class="team-id-role"><?php echo esc_html( $member['role'] ); ?></span>
              <?php if ( file_exists( $image_path ) ) : ?>
                <img class="team-id-photo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/team/' . $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy" />
              <?php endif; ?>
              <h3><?php echo esc_html( $member['name'] ); ?></h3>
              <span class="team-id-hint"><?php esc_html_e( 'Select to discover their story', 'navid' ); ?></span>
            </div>
            <div class="team-id-face team-id-back">
              <span class="team-id-role"><?php echo esc_html( $member['role'] ); ?></span>
              <dl class="team-id-details">
                <div><dt><?php esc_html_e( 'Name', 'navid' ); ?></dt><dd><?php echo esc_html( $member['name'] ); ?></dd></div>
                <div><dt><?php esc_html_e( 'Skills', 'navid' ); ?></dt><dd><?php echo esc_html( $member['skills'] ); ?></dd></div>
                <div><dt><?php esc_html_e( 'Story', 'navid' ); ?></dt><dd><?php echo esc_html( $member['story'] ); ?></dd></div>
                <div><dt><?php esc_html_e( 'Ambition', 'navid' ); ?></dt><dd><?php echo esc_html( $member['ambition'] ); ?></dd></div>
              </dl>
              <button class="team-story-button" type="button" data-name="<?php echo esc_attr( $member['name'] ); ?>" data-role="<?php echo esc_attr( $member['role'] ); ?>" data-story="<?php echo esc_attr( $member['tagline'] . ' ' . $member['story'] . ' ' . $member['ambition'] ); ?>"><?php esc_html_e( 'More info', 'navid' ); ?></button>
              <span class="team-id-hint"><?php esc_html_e( 'Select to return', 'navid' ); ?></span>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <figure class="navid-team-portrait">
      <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/team/Professional Team Portrait (1).png' ); ?>" alt="<?php esc_attr_e( 'OurTeam portrait', 'navid' ); ?>" loading="lazy" />
    </figure>
    <div class="navid-team-member-gallery" id="team-gallery">
      <?php foreach ( $team_members as $member ) : ?>
        <?php
        $member_image_path = get_template_directory() . '/assets/images/team/' . $member['image'];
        $member_profile    = get_page_by_path( sanitize_title( $member['name'] ), OBJECT, 'team_member' );
        $member_url        = $member_profile ? get_permalink( $member_profile ) : '#';
        ?>
        <?php if ( file_exists( $member_image_path ) ) : ?>
          <figure>
            <a href="<?php echo esc_url( $member_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View the profile of %s', 'navid' ), $member['name'] ) ); ?>">
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/team/' . $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy" />
              <figcaption>
                <span class="navid-team-member-name"><?php echo esc_html( $member['name'] ); ?></span>
                <span class="navid-team-click-cue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 11V5a2 2 0 0 1 4 0v6m0-3a2 2 0 0 1 4 0v3m0-1a2 2 0 0 1 4 0v3c0 4.4-3.6 8-8 8h-1c-2.8 0-5.1-1.4-6.5-3.8L4 14.5a2 2 0 0 1 3.4-2L9 14" /></svg><?php esc_html_e( 'Click to view profile', 'navid' ); ?></span>
              </figcaption>
            </a>
          </figure>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$goal_image = '';
foreach ( array( 'graduation.jpg', 'graduation.jpeg', 'graduation.png', 'graduation.webp' ) as $goal_image_file ) {
    $goal_image_path = get_template_directory() . '/assets/images/team/' . $goal_image_file;
    if ( file_exists( $goal_image_path ) ) {
        $goal_image = add_query_arg( 'ver', (string) filemtime( $goal_image_path ), get_template_directory_uri() . '/assets/images/team/' . $goal_image_file );
        break;
    }
}
$civil_service_image = '';
foreach ( array( 'civil-service.jpg', 'civil-service.jpeg', 'civil-service.png', 'civil-service.webp' ) as $civil_service_image_file ) {
    $civil_service_image_path = get_template_directory() . '/assets/images/team/' . $civil_service_image_file;
    if ( file_exists( $civil_service_image_path ) ) {
        $civil_service_image = add_query_arg( 'ver', (string) filemtime( $civil_service_image_path ), get_template_directory_uri() . '/assets/images/team/' . $civil_service_image_file );
        break;
    }
}
?>
<section class="navid-one-page-section navid-goal-section" id="goal" aria-labelledby="goal-heading">
  <div class="container navid-goal-layout">
    <div class="navid-one-page-heading">
      <span><?php esc_html_e( 'Our Goal', 'navid' ); ?></span>
      <h2 id="goal-heading"><?php esc_html_e( 'Graduate and Grow Together', 'navid' ); ?></h2>
    </div>
    <div class="navid-goal-card">
      <p><?php esc_html_e( 'Together, our goal is to graduate, apply our skills, and build meaningful technology.', 'navid' ); ?></p>
      <?php if ( $goal_image ) : ?>
        <figure class="navid-goal-image"><img src="<?php echo esc_url( $goal_image ); ?>" alt="" loading="lazy" /></figure>
      <?php endif; ?>
    </div>
    <div class="navid-civil-service-card">
      <?php if ( $civil_service_image ) : ?><figure class="navid-civil-service-image"><img src="<?php echo esc_url( $civil_service_image ); ?>" alt="" loading="lazy" /></figure><?php endif; ?>
      <p class="navid-goal-next"><?php esc_html_e( 'To pass the Civil Service Examination in March 2027.', 'navid' ); ?></p>
    </div>
    <div class="navid-goal-milestones" aria-label="<?php esc_attr_e( 'Goal milestones', 'navid' ); ?>">
      <article><span class="navid-goal-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m3 9 9-5 9 5-9 5-9-5Zm4 3.2v4.1c0 1.2 2.2 2.7 5 2.7s5-1.5 5-2.7v-4.1M21 10v6" /></svg></span><h3><?php esc_html_e( 'Graduate', 'navid' ); ?></h3></article>
      <article><span class="navid-goal-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m8 8-4 4 4 4M16 8l4 4-4 4M14 5l-4 14" /></svg></span><h3><?php esc_html_e( 'Build', 'navid' ); ?></h3></article>
      <article><span class="navid-goal-icon" aria-hidden="true"><svg viewBox="M13 3c4.6 1.1 6.9 3.4 8 8-2.1 4.6-5.4 7.9-10 10-1.1-4.6-3.4-7.9-8-10 2.1-4.6 5.4-6.9 10-8Zm-1 7h.01M8 16l-3 3M16 8l3-3" /></svg></span><h3><?php esc_html_e( 'Launch', 'navid' ); ?></h3></article>
    </div>
    <p class="navid-goal-promise"><?php esc_html_e( 'We support each other from our final year to our first professional role.', 'navid' ); ?></p>
  </div>
</section>

<section class="navid-one-page-section navid-contact-section" id="contact" aria-labelledby="contact-heading">
  <div class="container">
    <div class="navid-one-page-heading">
      <span><?php esc_html_e( 'Contact', 'navid' ); ?></span>
      <h2 id="contact-heading"><?php echo esc_html( $contact_page ? $contact_page->post_title : __( 'Get in Touch', 'navid' ) ); ?></h2>
    </div>
    <?php $team_contact_qrs = array( 'Wilan Jomart J. Satur' => 'Wilan Jomart J. Satur/wilan-qr.png', 'Jhon Phillip V. Polison' => 'Jhon Phillip V. Polison/jhon-qr.png', 'Carlo A. Nacua' => 'Carlo A. Nacua/carlo-qr.png', 'Jay Vee C. Bahian' => 'Jay Vee C. Bahian/jayvee-qr.png' ); ?>
    <div class="navid-team-contacts">
      <?php foreach ( $team_members as $member ) : ?>
        <article>
          <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/team/' . $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy" />
          <h3><?php echo esc_html( $member['name'] ); ?></h3>
          <?php if ( $member['email'] ) : ?><a class="navid-team-contact-email" href="mailto:<?php echo esc_attr( antispambot( $member['email'] ) ); ?>"><span aria-hidden="true">✉</span><?php echo esc_html( antispambot( $member['email'] ) ); ?></a><?php else : ?><span class="navid-team-contact-empty">Email to be added</span><?php endif; ?>
          <?php if ( $member['facebook'] ) : ?><a class="navid-team-contact-facebook" href="<?php echo esc_url( $member['facebook'] ); ?>" target="_blank" rel="noopener noreferrer"><span aria-hidden="true">f</span><?php esc_html_e( 'Facebook profile', 'navid' ); ?></a><?php else : ?><span class="navid-team-contact-empty">Facebook link to be added</span><?php endif; ?>
          <?php $contact_qr = isset( $team_contact_qrs[ $member['name'] ] ) ? $team_contact_qrs[ $member['name'] ] : ''; ?>
          <?php if ( $contact_qr && file_exists( get_template_directory() . '/assets/images/team/' . $contact_qr ) ) : ?><img class="navid-team-contact-qr" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/team/' . $contact_qr ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Scan %s’s contact QR code', 'navid' ), $member['name'] ) ); ?>" loading="lazy" /><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
    <div class="navid-contact-layout">
      <div class="navid-contact-copy">
      <?php
      if ( $contact_page && ! empty( trim( $contact_page->post_content ) ) ) {
          echo apply_filters( 'the_content', $contact_page->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      } else {
          echo '<p>' . esc_html__( 'Have a question or want to work with us? Send us a message and we will get back to you soon.', 'navid' ) . '</p>';
      }
      ?>
      <?php $contact_email = get_theme_mod( 'navid_contact_email', get_option( 'admin_email' ) ); ?>
      <?php if ( $contact_email ) : ?>
        <a class="navid-contact-email" href="mailto:<?php echo esc_attr( antispambot( $contact_email ) ); ?>"><?php echo esc_html( antispambot( $contact_email ) ); ?></a>
      <?php endif; ?>
      </div>
      <div class="navid-contact-form">
        <h3><?php echo esc_html( get_theme_mod( 'navid_contact_form_title', __( 'Send a Message', 'navid' ) ) ); ?></h3>
        <?php
        $contact_shortcode = get_theme_mod( 'navid_contact_shortcode', '' );
        if ( ! empty( $contact_shortcode ) ) {
            echo do_shortcode( $contact_shortcode );
        } elseif ( is_active_sidebar( 'contact-form' ) ) {
            dynamic_sidebar( 'contact-form' );
        } else {
            echo '<p>' . esc_html__( 'Use the email above to send us your message. A contact form can be added from Appearance → Customize → Contact Page Settings.', 'navid' ) . '</p>';
        }
        ?>
      </div>
    </div>
  </div>
</section>
<?php

get_footer();
