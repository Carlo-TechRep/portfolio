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

/*
 * One-page navigation sections. These are deliberately rendered on the front
 * page so the About and Contact navigation items can scroll here instead of
 * loading a separate page.
 */
$about_page   = get_page_by_path( 'about' );
$contact_page = get_page_by_path( 'contact' );
$team_members = array(
    array( 'role' => 'Project Manager', 'name' => 'Wilan Jomart J. Satur', 'tagline' => 'Leading with Vision, Managing with Purpose, and Turning Every Challenge into an Opportunity for Success.', 'id' => '2023-1-00000', 'address' => 'Luyong Bonbon, Opol, Misamis Oriental', 'image' => 'Wilan Jomart J. Satur/profile.png', 'service_image' => 'Wilan Jomart J. Satur/project-manager.png' ),
    array( 'role' => 'System Analyst', 'name' => 'Jhon Phillip V. Polison', 'tagline' => 'Understanding Complex Problems, Discovering Smarter Possibilities, and Designing Solutions That Make a Difference.', 'id' => '2023-1-05034', 'address' => 'Amoros, City of El Salvador', 'image' => 'Jhon Phillip V. Polison/profile.png', 'service_image' => 'Jhon Phillip V. Polison/system-analyst.png' ),
    array( 'role' => 'Programmer', 'name' => 'Carlo A. Nacua', 'tagline' => 'Transforming Creative Ideas into Powerful Code, Building Innovative Solutions, and Bringing Technology to Life.', 'id' => '2023-1-04974', 'address' => 'Amoros, City of El Salvador', 'image' => 'Carlo A. Nacua/profile.png', 'service_image' => 'Carlo A. Nacua/programmer.png' ),
    array( 'role' => 'Technical Writer', 'name' => 'Jay Vee C. Bahian', 'tagline' => 'Turning Complex Technical Concepts into Clear Communication, Connecting Innovation with Understanding, and Making Knowledge Accessible to Everyone.', 'id' => '2023-1-05380', 'address' => 'Cogon, City of El Salvador', 'image' => 'Jay Vee C. Bahian/profile.png', 'service_image' => 'Jay Vee C. Bahian/technical-writer.png' ),
);
?>

<section class="navid-one-page-section navid-home-team-section" id="home-team" aria-labelledby="home-team-heading">
  <div class="container navid-team-scroll-stage">
    <div class="navid-one-page-heading">
      <h2 id="home-team-heading"><?php esc_html_e( 'The People Behind ', 'navid' ); ?><span class="navid-team-heading-vision"><?php esc_html_e( 'TechVision', 'navid' ); ?></span></h2>
    </div>
    <div class="team-id-grid navid-home-team-grid">
      <?php foreach ( $team_members as $member_index => $member ) : ?>
        <?php $image_path = get_template_directory() . '/assets/images/team/' . $member['image']; ?>
        <article class="team-id-card team-card-fall" style="--team-card-index: <?php echo esc_attr( $member_index ); ?>" tabindex="0" role="button" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( __( 'Show ID details for %s', 'navid' ), $member['name'] ) ); ?>">
          <div class="team-id-card-inner">
            <div class="team-id-face team-id-front">
              <span class="team-id-role"><?php echo esc_html( $member['role'] ); ?></span>
              <?php if ( file_exists( $image_path ) ) : ?>
                <img class="team-id-photo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/team/' . $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy" />
              <?php else : ?>
                <div class="team-id-mark" aria-hidden="true">TV</div>
              <?php endif; ?>
              <h3><?php echo esc_html( $member['name'] ); ?></h3>
              <span class="team-id-hint"><?php esc_html_e( 'Click to view ID', 'navid' ); ?></span>
            </div>
            <div class="team-id-face team-id-back">
              <span class="team-id-role"><?php echo esc_html( $member['role'] ); ?></span>
              <dl class="team-id-details">
                <div><dt><?php esc_html_e( 'Name', 'navid' ); ?></dt><dd><?php echo esc_html( $member['name'] ); ?></dd></div>
                <div><dt><?php esc_html_e( 'ID Number', 'navid' ); ?></dt><dd><?php echo esc_html( $member['id'] ); ?></dd></div>
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
          'A successful project begins with a clear vision and strong leadership. As the Project Manager, I organize tasks, set achievable goals, manage timelines, and guide the team toward a shared purpose. Through clear communication, teamwork, and strategic planning, I help turn every idea into a meaningful achievement.',
          'Every successful system begins with a deep understanding of the problem it aims to solve. As the System Analyst, I identify user needs, analyze system requirements, and transform complex challenges into practical and innovative solutions. I connect technology with real-world needs to create efficient and reliable systems.',
          'Behind every functional system is the dedication to turn ideas into reality through programming. As the Programmer, I develop reliable applications, write efficient code, solve technical challenges, and improve system performance. With creativity and logical thinking, I build useful solutions that improve user experiences.',
          'Technology becomes more valuable when people can understand and use it effectively. As the Technical Writer, I create clear, accurate, and well-organized documentation for system processes, features, and functionality. I bridge technical complexity and user understanding so every innovation is easy to explore.',
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
      <h2 id="about-heading"><?php echo esc_html( $about_page ? $about_page->post_title : __( 'Meet the Team', 'navid' ) ); ?></h2>
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
    <div class="navid-team-roles" role="list">
      <span role="listitem"><?php esc_html_e( 'Project Management', 'navid' ); ?></span>
      <span role="listitem"><?php esc_html_e( 'Data Analytics', 'navid' ); ?></span>
      <span role="listitem"><?php esc_html_e( 'Programming', 'navid' ); ?></span>
      <span role="listitem"><?php esc_html_e( 'Technical Writing', 'navid' ); ?></span>
    </div>
  </div>
</section>

<section class="navid-one-page-section navid-contact-section" id="contact" aria-labelledby="contact-heading">
  <div class="container">
    <div class="navid-one-page-heading">
      <span><?php esc_html_e( 'Contact', 'navid' ); ?></span>
      <h2 id="contact-heading"><?php echo esc_html( $contact_page ? $contact_page->post_title : __( 'Get in Touch', 'navid' ) ); ?></h2>
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
