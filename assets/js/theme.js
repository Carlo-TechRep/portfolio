/**
 * Navid Theme — Main JavaScript
 */
(function() {
  'use strict';

  // ── Scroll progress bar ──────────────────────────
  const progress = document.getElementById('scrollProgress');
  if (progress) {
    window.addEventListener('scroll', () => {
      const s = window.scrollY;
      const t = document.documentElement.scrollHeight - window.innerHeight;
      progress.style.width = t > 0 ? (s / t * 100) + '%' : '0%';
    }, { passive: true });
  }

  // ── Header scroll state ──────────────────────────
  const header = document.getElementById('siteHeader');
  const heroSection = document.querySelector('.hero-section');
  const topBar = document.querySelector('.top-bar');
  const hasTopbar = document.body.classList.contains('has-topbar');
  const isTransparent = document.body.classList.contains('has-transparent-header');
  let topbarH = 0;

  if (topBar) {
    topbarH = topBar.offsetHeight;
    document.documentElement.style.setProperty('--topbar-h', topbarH + 'px');
  }

  if (header) {
    const updateHeaderH = () => {
      document.documentElement.style.setProperty('--header-h', header.offsetHeight + 'px');
    };
    updateHeaderH();
    window.addEventListener('resize', () => {
      updateHeaderH();
      if (topBar) {
        topbarH = topBar.offsetHeight;
        document.documentElement.style.setProperty('--topbar-h', topbarH + 'px');
      }
    });
    if ('ResizeObserver' in window) {
      new ResizeObserver(updateHeaderH).observe(header);
    }

    // If homepage + topbar: offset fixed header below topbar initially
    if (isTransparent && hasTopbar) {
      header.style.top = topbarH + 'px';
    }
  }

  window.addEventListener('scroll', () => {
    const y = window.scrollY;
    if (header) {
      header.classList.toggle('scrolled', y > 40);
      // On homepage with topbar: slide header to top:0 once scrolled past topbar
      if (isTransparent && hasTopbar) {
        header.style.top = Math.max(0, topbarH - y) + 'px';
      }
    }
  }, { passive: true });

  // ── Parallax ─────────────────────────────────────
  const parallaxEls = document.querySelectorAll('[data-parallax]');
  let pticking = false;

  function updateParallax() {
    parallaxEls.forEach(el => {
      const rate = parseFloat(el.dataset.parallax) || 0.1;
      const rect = el.getBoundingClientRect();
      const center = rect.top + rect.height / 2;
      const offset = (center - window.innerHeight / 2) * rate;
      el.style.transform = `translateY(${offset}px) scale(${1 + rate * 0.5})`;
    });
    pticking = false;
  }

  if (parallaxEls.length) {
    window.addEventListener('scroll', () => {
      if (!pticking) { requestAnimationFrame(updateParallax); pticking = true; }
    }, { passive: true });
  }

  // ── Intersection Observer: fade-up + clip-reveal ─
  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.anim-up, .scale-in').forEach(el => obs.observe(el));

    const clipObs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('revealed'); clipObs.unobserve(e.target); }
      });
    }, { threshold: 0.2 });

    document.querySelectorAll('.clip-reveal').forEach(el => clipObs.observe(el));
  }

  // ── Topic chips ──────────────────────────────────
  // Keep the team portrait centered when the About navigation link is used.
  function scrollToTeamPortrait(smooth) {
    var portrait = document.querySelector('.navid-team-portrait');
    if (!portrait) return;

    var portraitCenter = window.scrollY + portrait.getBoundingClientRect().top + (portrait.offsetHeight / 2);
    // Leave room above the portrait for the About Us heading and introduction.
    var targetPosition = Math.max(0, portraitCenter - (window.innerHeight / 2) - 210);
    window.scrollTo({ top: targetPosition, behavior: smooth ? 'smooth' : 'auto' });
  }

  document.querySelectorAll('a[href$="#about"]').forEach(function(link) {
    link.addEventListener('click', function(event) {
      if (!document.querySelector('.navid-team-portrait')) return;
      event.preventDefault();
      window.history.replaceState(null, '', '#about');
      scrollToTeamPortrait(true);
    });
  });

  if (window.location.hash === '#about') {
    window.setTimeout(function() { scrollToTeamPortrait(false); }, 0);
  }

  // Return from a member bio to the already-open team gallery without reloading it.
  document.querySelectorAll('.team-resume-return').forEach(function(button) {
    button.addEventListener('click', function() {
      if (window.history.length > 1) {
        window.history.back();
      } else if (button.dataset.returnFallback) {
        window.location.assign(button.dataset.returnFallback);
      }
    });
  });

  // Open one hobby image at a time on click.
  document.querySelectorAll('.team-resume-hobby-images').forEach(function(gallery) {
    gallery.querySelectorAll('img').forEach(function(image) {
      image.addEventListener('click', function() {
        var wasActive = image.classList.contains('is-active');
        gallery.querySelectorAll('img').forEach(function(item) { item.classList.remove('is-active'); });
        if (!wasActive) {
          image.classList.add('is-active');
        }
      });
    });
  });

  document.querySelectorAll('.topic-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.topic-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
    });
  });

  // ── Mobile menu toggle + focus trap ──────────────
  const menuBtn = document.querySelector('.hamburger');
  const navMenu = document.querySelector('.nav-menu');
  if (menuBtn && navMenu) {
    const siteHeader = document.querySelector('.site-header');
    const primaryNav = document.querySelector('.primary-nav');
    const menuLabel = menuBtn.getAttribute('aria-label') || 'Menu';
    menuBtn.setAttribute('aria-expanded', 'false');

    const openMobileMenu = () => {
      menuBtn.setAttribute('aria-expanded', 'true');
      if (siteHeader) siteHeader.classList.add('mobile-menu-open');

      // Focus first menu link when opened
      const firstLink = navMenu.querySelector('a');
      if (firstLink) {
        setTimeout(() => firstLink.focus(), 50);
      }
    };

    const closeMobileMenu = () => {
      menuBtn.setAttribute('aria-expanded', 'false');
      if (siteHeader) siteHeader.classList.remove('mobile-menu-open');
      menuBtn.focus();
    };

    menuBtn.addEventListener('click', () => {
      const isExpanded = menuBtn.getAttribute('aria-expanded') === 'true';
      if (isExpanded) {
        closeMobileMenu();
      } else {
        openMobileMenu();
      }
    });

    // Focus trap + Escape key for mobile menu
    if (primaryNav) {
      const trapContainer = siteHeader || document;
      trapContainer.addEventListener('keydown', (e) => {
        // Only trap when menu is open
        if (menuBtn.getAttribute('aria-expanded') !== 'true') return;

        // Close on Escape
        if (e.key === 'Escape') {
          closeMobileMenu();
          return;
        }

        // Focus trap on Tab
        if (e.key !== 'Tab') return;

        const menuFocusable = primaryNav.querySelectorAll(
          'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        if (!menuFocusable.length) return;

        const firstMenuItem = menuFocusable[0];
        const lastMenuItem = menuFocusable[menuFocusable.length - 1];

        if (e.shiftKey) {
          // Shift+Tab on first menu item → jump to hamburger (close)
          if (document.activeElement === firstMenuItem) {
            menuBtn.focus();
            e.preventDefault();
          }
          // Shift+Tab on hamburger → jump to last menu item
          else if (document.activeElement === menuBtn) {
            lastMenuItem.focus();
            e.preventDefault();
          }
        } else {
          // Tab on last menu item → jump to hamburger (close)
          if (document.activeElement === lastMenuItem) {
            menuBtn.focus();
            e.preventDefault();
          }
          // Tab on hamburger → jump to first menu item
          else if (document.activeElement === menuBtn) {
            firstMenuItem.focus();
            e.preventDefault();
          }
        }
      });
    }
  }

  // ── Dropdown Toggle ──────────────────────────────
  document.querySelectorAll('.dropdown-toggle').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const li = btn.closest('li');
      const isExpanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', !isExpanded);
      li.classList.toggle('toggled-on');
    });
  });

  // ── Newsletter form ──────────────────────────────
  document.querySelectorAll('.newsletter-form, .subscribe-form').forEach(form => {
    form.addEventListener('submit', e => {
      const action = (form.getAttribute('action') || '').trim();
      // If action is default '#' or empty or points to the current page, run the visual demo
      if (!action || action === '#' || action === window.location.href || action === window.location.pathname) {
        e.preventDefault();
        const btn = form.querySelector('button');
        if (btn) {
          btn.textContent = "You're In ✓";
          btn.style.background = 'var(--sage-pale)';
        }
      }
      // Otherwise, let the browser submit naturally to Mailchimp/ConvertKit/etc.!
    });
  });

  // ── Dark Mode Toggle ──────────────────────────────
  const darkModeToggle = document.querySelector('.js-dark-mode-toggle');
  if (darkModeToggle) {
    const sunIcon = darkModeToggle.querySelector('.sun-icon');
    const moonIcon = darkModeToggle.querySelector('.moon-icon');
    
    // Check local storage or Customizer default
    const currentTheme = localStorage.getItem('navid_theme');
    const defaultMode = document.body.getAttribute('data-default-mode') || 'light';

    if (currentTheme) {
        // User explicitly toggled before
        if (currentTheme === 'dark-mode') {
            document.body.classList.add('dark-mode');
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
        } else {
            document.body.classList.remove('dark-mode');
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'block';
        }
    } else {
        // No local storage, fallback to Customizer default
        if (defaultMode === 'dark') {
            // PHP already added 'dark-mode' class, just update icons
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
        } else if (defaultMode === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('dark-mode');
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
        } else {
            // Light mode
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'block';
        }
    }

    darkModeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        let theme = 'light';
        if (document.body.classList.contains('dark-mode')) {
            theme = 'dark-mode';
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
        } else {
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'block';
        }
        localStorage.setItem('navid_theme', theme);
    });
  }

  // ── Accessible Search Overlay ─────────────────────
  const searchToggle = document.querySelector('.js-search-toggle');
  const searchOverlay = document.getElementById('searchOverlay');
  const searchClose = document.querySelector('.js-search-close');
  const searchInput = document.querySelector('.search-overlay .search-field');

  if (searchToggle && searchOverlay && searchClose) {
    const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    const focusableContent = searchOverlay.querySelectorAll(focusableElements);
    const firstFocusable = focusableContent[0];
    const lastFocusable = focusableContent[focusableContent.length - 1];

    const openSearch = () => {
      searchOverlay.classList.add('active');
      searchToggle.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
      setTimeout(() => { if (searchInput) searchInput.focus(); }, 100);
    };

    const closeSearch = () => {
      searchOverlay.classList.remove('active');
      searchToggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
      searchToggle.focus();
    };

    searchToggle.addEventListener('click', openSearch);
    searchClose.addEventListener('click', closeSearch);

    searchOverlay.addEventListener('click', e => {
      if (e.target === searchOverlay) closeSearch();
    });

    searchOverlay.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeSearch();
      else if (e.key === 'Tab') {
        if (e.shiftKey) {
          if (document.activeElement === firstFocusable) {
            lastFocusable.focus();
            e.preventDefault();
          }
        } else {
          if (document.activeElement === lastFocusable) {
            firstFocusable.focus();
            e.preventDefault();
          }
        }
      }
    });
  }
  // ── Hero Ticker (Vertical Rolling List) ────────
  var ticker = document.querySelector('.hero-ticker');
  if (ticker) {
    var track = ticker.querySelector('.hero-ticker-track');
    var items = ticker.querySelectorAll('.hero-ticker-item');
    var prevBtn = document.querySelector('.hero-ticker-prev');
    var nextBtn = document.querySelector('.hero-ticker-next');
    var progressBar = ticker.querySelector('.hero-ticker-progress-bar');
    var progressWrap = ticker.querySelector('.hero-ticker-progress');
    var visible = parseInt(ticker.dataset.visible, 10) || 3;
    var intervalAttr = ticker.dataset.interval;
    var interval = intervalAttr !== undefined ? parseInt(intervalAttr, 10) : 3500;
    var total = items.length;
    var current = 0;
    var timer = null;
    var isPaused = false;
    var needsSlider = total > visible;

    // Set CSS custom property for progress bar animation
    if (interval > 0) {
      ticker.style.setProperty('--ticker-interval', interval + 'ms');
    } else {
      if (progressBar) progressBar.style.display = 'none'; // Hide progress bar if no autoplay
    }

    // Calculate the height of one item (including padding/borders)
    function getItemHeight() {
      if (!items.length) return 0;
      var style = getComputedStyle(items[0]);
      return items[0].offsetHeight + parseFloat(style.marginTop || 0) + parseFloat(style.marginBottom || 0);
    }

    // Set the window height to show exactly `visible` items
    function setWindowHeight() {
      var win = ticker.querySelector('.hero-ticker-window');
      if (win && items.length) {
        var isHorizontal = document.body.classList.contains('hero-content-pos-center');
        var h = 0;
        for (var i = 0; i < Math.min(visible, total); i++) {
          if (isHorizontal) {
            h += items[i].offsetWidth;
          } else {
            h += items[i].offsetHeight;
          }
        }
        if (isHorizontal) {
          win.style.height = 'auto';
        } else {
          win.style.height = h + 'px';
        }
      }
    }

    function scrollTo(idx) {
      current = ((idx % total) + total) % total;
      var isHorizontal = document.body.classList.contains('hero-content-pos-center');
      var offset = 0;
      for (var i = 0; i < current; i++) {
        if (isHorizontal) {
          offset += items[i].offsetWidth;
        } else {
          offset += items[i].offsetHeight;
        }
      }
      if (isHorizontal) {
        track.style.transform = 'translateX(-' + offset + 'px)';
      } else {
        track.style.transform = 'translateY(-' + offset + 'px)';
      }
      resetProgressBar();
    }

    function next() {
      // If we've scrolled far enough that fewer than `visible` remain, wrap
      if (current + 1 > total - visible) {
        scrollTo(0);
      } else {
        scrollTo(current + 1);
      }
    }

    function prev() {
      if (current <= 0) {
        scrollTo(total - visible);
      } else {
        scrollTo(current - 1);
      }
    }

    function resetProgressBar() {
      if (progressBar && needsSlider) {
        // Force reset animation
        progressBar.style.animation = 'none';
        progressBar.offsetHeight; // trigger reflow
        progressBar.style.animation = '';
      }
    }

    function resetTimer() {
      clearInterval(timer);
      if (!isPaused && needsSlider && interval > 0) {
        timer = setInterval(next, interval);
      }
    }

    // ── Static segmented bar for ≤3 posts ──
    if (!needsSlider) {
      // Mark ticker as static (no auto-play)
      ticker.classList.add('is-static');

      // Set progress bar to show segments: 1 post = 33%, 2 = 66%, 3 = 100%
      if (progressBar) {
        var fillPercent = Math.round((total / visible) * 100);
        progressBar.style.animation = 'none';
        progressBar.style.width = fillPercent + '%';
        progressBar.style.transition = 'width 0.8s cubic-bezier(0.16, 1, 0.3, 1)';
      }

      // Build segment dots on the progress track
      if (progressWrap) {
        for (var s = 0; s < visible; s++) {
          var dot = document.createElement('span');
          dot.className = 'progress-segment-dot';
          dot.style.left = ((s + 1) / visible * 100) + '%';
          if (s < total) {
            dot.classList.add('filled');
          }
          progressWrap.appendChild(dot);
        }
      }

      // Hide nav buttons when not needed
      if (prevBtn) prevBtn.style.display = 'none';
      if (nextBtn) nextBtn.style.display = 'none';

    } else {
      // ── Animated slider for >3 posts ──

      // Initialize
      setWindowHeight();
      window.addEventListener('resize', setWindowHeight);

      // Start auto-play if interval is > 0
      if (interval > 0) {
        timer = setInterval(next, interval);
      } else {
        ticker.classList.add('is-paused'); // visually pause
      }

      // Nav buttons
      if (nextBtn) nextBtn.addEventListener('click', function() { next(); resetTimer(); });
      if (prevBtn) prevBtn.addEventListener('click', function() { prev(); resetTimer(); });

      // Pause on hover
      var sidebarCol = ticker.closest('.hero-sidebar-col');
      if (sidebarCol) {
        sidebarCol.addEventListener('mouseenter', function() {
          isPaused = true;
          ticker.classList.add('is-paused');
          clearInterval(timer);
        });
        sidebarCol.addEventListener('mouseleave', function() {
          isPaused = false;
          ticker.classList.remove('is-paused');
          resetTimer();
          resetProgressBar();
        });
      }

      // Keyboard accessibility
      ticker.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown' || e.key === 'ArrowRight') { next(); resetTimer(); e.preventDefault(); }
        if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') { prev(); resetTimer(); e.preventDefault(); }
      });
    }

    // Always set window height
    setWindowHeight();
    window.addEventListener('resize', setWindowHeight);
  }

  // Preloader Logic
  window.addEventListener('load', function() {
    var preloader = document.getElementById('navidPreloader');
    if (preloader) {
      setTimeout(function() {
        document.body.classList.add('site-revealed');
        setTimeout(function() {
          document.body.classList.add('hero-floating');
        }, 1600);
        preloader.classList.add('is-hidden');
        setTimeout(function() {
          preloader.style.display = 'none';
        }, preloader.classList.contains('navid-puzzle-preloader') ? 1850 : 900);
      }, 1800);
    }
  });

  // Team ID cards: click or keyboard flip.
  document.querySelectorAll('.team-id-card').forEach(function(card) {
    function revealCard() {
      card.classList.add('is-visible');
    }
    if ('IntersectionObserver' in window) {
      var cardObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            revealCard();
            cardObserver.unobserve(card);
          }
        });
      }, { threshold: 0.15 });
      cardObserver.observe(card);
    } else {
      revealCard();
    }
    function toggleCard() {
      var isFlipped = card.classList.toggle('is-flipped');
      card.setAttribute('aria-pressed', isFlipped ? 'true' : 'false');
    }
    card.addEventListener('click', toggleCard);
    card.addEventListener('keydown', function(event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        toggleCard();
      }
    });
  });

  // Team life-story modal.
  var storyModal = document.getElementById('teamStoryModal');
  if (storyModal) {
    var storyTitle = document.getElementById('teamStoryModalTitle');
    var storyRole = document.getElementById('teamStoryModalRole');
    var storyText = document.getElementById('teamStoryModalText');
    var storyTrigger = null;

    function closeStoryModal() {
      storyModal.classList.remove('is-open');
      storyModal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('team-story-modal-open');
      if (storyTrigger) {
        storyTrigger.focus();
      }
    }

    document.querySelectorAll('.team-story-button').forEach(function(button) {
      button.addEventListener('click', function(event) {
        event.stopPropagation();
        storyTrigger = button;
        storyTitle.textContent = button.dataset.name || '';
        storyRole.textContent = button.dataset.role || '';
        storyText.textContent = button.dataset.story || '';
        storyModal.classList.add('is-open');
        storyModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('team-story-modal-open');
        storyModal.querySelector('.team-story-modal__close').focus();
      });
    });

    storyModal.querySelectorAll('[data-team-modal-close]').forEach(function(closeButton) {
      closeButton.addEventListener('click', closeStoryModal);
    });

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && storyModal.classList.contains('is-open')) {
        closeStoryModal();
      }
    });
  }

  // Scroll-linked falling reveal for the four Home team cards.
  var homeTeamSection = document.querySelector('.navid-home-team-section');
  // Reveal the team cards as the section is pinned during scrolling.
  var enablePinnedTeamReveal = true;
  if (enablePinnedTeamReveal && homeTeamSection && window.gsap && window.ScrollTrigger) {
    var fallingTeamCards = Array.prototype.slice.call(homeTeamSection.querySelectorAll('.team-card-fall'));
    var teamStage = homeTeamSection.querySelector('.navid-team-scroll-stage');

    if (fallingTeamCards.length === 4 && teamStage) {
      gsap.registerPlugin(ScrollTrigger);

      var teamMedia = gsap.matchMedia();
      teamMedia.add({
        desktop: '(min-width: 901px)',
        reduce: '(prefers-reduced-motion: reduce)'
      }, function(context) {
        var shouldAnimate = context.conditions.desktop && !context.conditions.reduce;

        if (!shouldAnimate) {
          gsap.set(fallingTeamCards, { clearProps: 'all' });
          fallingTeamCards.forEach(function(card) {
            card.setAttribute('tabindex', '0');
          });
          return;
        }

        homeTeamSection.classList.add('team-scroll-ready');
        gsap.set(fallingTeamCards, {
          y: -300,
          autoAlpha: 0,
          filter: 'blur(5px)'
        });

        var timeline = gsap.timeline({
          scrollTrigger: {
            trigger: homeTeamSection,
            start: 'top top',
            end: '+=300%',
            // Pin the complete section so its background travels with the
            // cards instead of leaving the cards over the hero underneath.
            pin: homeTeamSection,
            scrub: 1,
            anticipatePin: 1,
            invalidateOnRefresh: true
          }
        });

        fallingTeamCards.forEach(function(card) {
          timeline.to(card, {
            y: 0,
            autoAlpha: 1,
            filter: 'blur(0px)',
            duration: 1,
            ease: 'power2.out',
            onStart: function() { card.setAttribute('tabindex', '0'); }
          });
        });

        return function() {
          homeTeamSection.classList.remove('team-scroll-ready');
          gsap.set(fallingTeamCards, { clearProps: 'all' });
        };
      });
    }
  } else if (homeTeamSection) {
    // Do not hide cards if a vendor file is unavailable.
    homeTeamSection.classList.remove('team-scroll-ready');
  }

  // Services portfolio: reveal each profile card as it enters the viewport.
  var servicesSection = document.querySelector('.navid-services-section');
  if (servicesSection && window.gsap && window.ScrollTrigger && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    var serviceCards = servicesSection.querySelectorAll('.navid-service-card');
    if (serviceCards.length) {
      gsap.registerPlugin(ScrollTrigger);
      var serviceCardGroups = [
        Array.prototype.slice.call(serviceCards, 0, 2),
        Array.prototype.slice.call(serviceCards, 2)
      ];
      var serviceContent = Array.prototype.slice.call(servicesSection.querySelectorAll('.navid-service-photo, .navid-service-card-content'));
      var serviceContentGroups = [serviceContent.slice(0, 4), serviceContent.slice(4)];
      var servicesReveal = gsap.timeline({
        scrollTrigger: {
          trigger: servicesSection,
          start: 'top 82%',
          toggleActions: 'play none none reverse'
        }
      });

      function addServiceGroup(cards, content, position) {
        servicesReveal.from(cards, {
          y: 64,
          autoAlpha: 0,
          filter: 'blur(4px)',
          duration: 0.9,
          ease: 'power3.out',
          stagger: 0.12
        }, position).from(content, {
        y: 20,
        autoAlpha: 0,
        duration: 0.65,
        ease: 'power2.out',
        stagger: 0.08
        }, '<0.18');
      }

      addServiceGroup(serviceCardGroups[0], serviceContentGroups[0], 0);
      addServiceGroup(serviceCardGroups[1], serviceContentGroups[1], '+=0.25');

      servicesSection.querySelectorAll('.navid-service-photo').forEach(function(photo) {
        photo.addEventListener('mouseenter', function() {
          gsap.to(photo, { scale: 1.08, duration: 0.35, ease: 'power2.out', overwrite: 'auto' });
        });
        photo.addEventListener('mouseleave', function() {
          gsap.to(photo, { scale: 1, duration: 0.35, ease: 'power2.out', overwrite: 'auto' });
        });
      });
    }
  }

  // Move the navigation underline as the visitor reaches the one-page sections.
  var onePageMenu = document.querySelector('.nav-menu');
  var onePageSections = document.querySelectorAll('#about, #contact');
  if (onePageMenu && onePageSections.length) {

    function setActiveSection(sectionId) {
      var activeItem = null;
      onePageMenu.querySelectorAll('li').forEach(function(item) {
        var link = item.querySelector('a');
        if (!link) return;
        var hash = link.hash;
        var isActive = hash === '#' + sectionId;
        item.classList.toggle('is-scroll-active', isActive);
        if (isActive) activeItem = item;
      });
      onePageMenu.classList.toggle('has-scroll-active', !!activeItem);
    }

    function updateOnePageNavigation() {
      var scrollMarker = window.scrollY + 150;
      var currentSection = null;

      onePageSections.forEach(function(section) {
        if (section.offsetTop <= scrollMarker) {
          currentSection = section.id;
        }
      });

      if (currentSection) {
        setActiveSection(currentSection);
      } else {
        onePageMenu.classList.remove('has-scroll-active');
        onePageMenu.querySelectorAll('.is-scroll-active').forEach(function(item) {
          item.classList.remove('is-scroll-active');
        });
      }
    }
    window.addEventListener('scroll', updateOnePageNavigation, { passive: true });
    window.addEventListener('resize', updateOnePageNavigation);
    updateOnePageNavigation();
  }

})();
