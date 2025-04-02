<?php get_header(); ?>

<div class="site-wrapper">

  <!-- ===================== Top Navigation & Branding ===================== -->
  <div class="site-top-menu">
    <div class="top-menu-inner">
      <?php if (!get_option('show_header_box', 1)): ?>
        <div class="site-title-description-inline">
          <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
          <span class="site-description"><?php bloginfo('description'); ?></span>
        </div>
      <?php endif; ?>

      <nav class="main-nav">
        <?php wp_nav_menu(array('theme_location' => 'primary')); ?>
      </nav>
    </div>
  </div>

  <!-- ===================== Site Header with Avatar ===================== -->
  <?php if (get_option('show_header_box', 1)): ?>
    <header class="site-header">
      <div class="header-left">
        <div class="avatar">
          <?php echo get_avatar(get_option('admin_email'), 96); ?>
        </div>
      </div>
      <div class="header-right">
        <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
        <p class="site-description"><?php bloginfo('description'); ?></p>
      </div>
    </header>
  <?php endif; ?>

  <!-- ===================== Main Content ===================== -->
  <main class="site-content">

    <!-- ========== Page: 404 Not Found ========== -->
    <?php if (is_404()) : ?>
      <article class="post error">
        <h1 class="post-title">۴۰۴ - صفحه پیدا نشد</h1>
        <p>متأسفانه صفحه‌ای که به دنبال آن هستید وجود ندارد یا حذف شده است.</p>
        <p><a href="<?php echo home_url(); ?>" class="more-link">بازگشت به صفحه اصلی</a></p>
      </article>

    <!-- ========== Page: Search Results ========== -->
    <?php elseif (is_search()) : ?>
      <section class="search-results">
        <h1 class="search-title">نتایج جستجو برای: «<?php echo get_search_query(); ?>»</h1>

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
          <article class="post search-post">
            <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="post-excerpt"><?php the_excerpt(); ?></div>
            <a class="more-link" href="<?php the_permalink(); ?>">ادامه مطلب</a>
          </article>
        <?php endwhile; ?>

          <div id="pagination" class="custom-pagination">
            <?php echo paginate_links([
              'prev_text' => '← قبلی',
              'next_text' => 'بعدی →',
              'type'      => 'list',
            ]); ?>
          </div>

        <?php else : ?>
          <article class="post error">
            <h2>نتیجه‌ای برای جستجوی شما یافت نشد.</h2>
            <p>لطفاً عبارت دیگری را امتحان کنید.</p>
          </article>
        <?php endif; ?>
      </section>

    <!-- ========== Page: Single Post ========== -->
    <?php elseif (is_single()) : ?>
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article class="post">
          <?php if (has_post_thumbnail()) : ?>
            <div class="featured-thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('featured-thumb'); ?></a></div>
          <?php endif; ?>

          <div class="post-header">
            <div class="post-header-main">
              <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <div class="post-date"><?php echo get_the_date(); ?></div>
            </div>
            <?php if (comments_open()) : ?>
              <span class="comments-link">
                <?php echo (get_comments_number() == 0) ? 'بدون دیدگاه' : get_comments_number() . ' دیدگاه'; ?>
              </span>
            <?php endif; ?>
          </div>

          <div class="the-content"><?php the_content(); ?><?php wp_link_pages(); ?></div>

          <div class="meta">
            <div class="category"><?php echo get_the_category_list(); ?></div>
            <div class="tags"><?php echo get_the_tag_list('', ' '); ?></div>
          </div>

          <?php if (comments_open() || get_comments_number()) comments_template('', true); ?>
        </article>
      <?php endwhile; endif; ?>

    <!-- ========== Page: Static Page ========== -->
    <?php elseif (is_page()) : ?>
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article class="post">
          <h1 class="post-title"><?php the_title(); ?></h1>
          <div class="the-content"><?php the_content(); ?><?php wp_link_pages(); ?></div>
        </article>
      <?php endwhile; endif; ?>

    <!-- ========== Page: Home / Archive / Tag / Category ========== -->
    <?php else : ?>

      <!-- === Archive/Tag/Category Title === -->
      <?php if (is_tag()) : ?>
        <header class="archive-header">
          <h1 class="archive-title">مطالب با برچسب: «<?php single_tag_title(); ?>»</h1>
          <?php if (tag_description()) : ?><div class="tag-description"><?php echo tag_description(); ?></div><?php endif; ?>
        </header>
      <?php elseif (is_category()) : ?>
        <header class="archive-header"><h1 class="archive-title">دسته: «<?php single_cat_title(); ?>»</h1></header>
      <?php elseif (is_archive()) : ?>
        <header class="archive-header"><h1 class="archive-title"><?php the_archive_title(); ?></h1></header>
      <?php endif; ?>

      <!-- === Featured Post: Only On Homepage === -->
      <?php if (is_home()) : ?>
        <?php
        $sticky_posts = get_option('sticky_posts');
        $featured_query = new WP_Query([
          'posts_per_page' => 1,
          'post_status' => 'publish',
          'post__in' => $sticky_posts,
          'ignore_sticky_posts' => false,
        ]);
        if (!$featured_query->have_posts()) {
          $featured_query = new WP_Query([
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
          ]);
        }
        $do_not_duplicate = [];
        if ($featured_query->have_posts()) :
          while ($featured_query->have_posts()) : $featured_query->the_post();
            $do_not_duplicate[] = get_the_ID();
        ?>
            <article class="post featured-post">
              <div class="featured-ribbon">آخرین نوشته در آی‌معین دات کام</div>
              <?php if (has_post_thumbnail()) : ?>
                <div class="featured-thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('featured-thumb'); ?></a></div>
              <?php endif; ?>
              <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <div class="post-excerpt"><?php the_excerpt(); ?></div>
              <a class="more-link" href="<?php the_permalink(); ?>">بیشتر بخوانید</a>
            </article>
        <?php endwhile; wp_reset_postdata(); endif; ?>
      <?php else : ?>
        <?php $do_not_duplicate = []; ?>
      <?php endif; ?>

      <!-- === Post List Loop === -->
      <?php
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      if (is_home()) {
        $query_to_use = new WP_Query([
          'post__not_in' => $do_not_duplicate,
          'paged' => $paged,
          'post_status' => 'publish',
          'posts_per_page' => get_option('posts_per_page'),
          'ignore_sticky_posts' => true,
        ]);
      } else {
        $query_to_use = $wp_query;
      }
      ?>

      <?php if ($query_to_use->have_posts()) : ?>
        <?php while ($query_to_use->have_posts()) : $query_to_use->the_post(); ?>
          <article class="post">
            <div class="post-inner">
              <div class="post-content">
                <div class="post-header">
                  <div class="post-header-main">
                    <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="post-date"><?php echo get_the_date(); ?></div>
                  </div>
                  <?php if (comments_open()) : ?>
                    <span class="comments-link">
                      <?php echo (get_comments_number() == 0) ? 'بدون دیدگاه' : get_comments_number() . ' دیدگاه'; ?>
                    </span>
                  <?php endif; ?>
                </div>
                <div class="post-excerpt"><?php the_excerpt(); ?></div>
                <?php if (get_option('show_read_more', true)) : ?>
                  <a class="more-link" href="<?php the_permalink(); ?>">ادامه مطلب</a>
                <?php endif; ?>
              </div>
              <div class="post-thumbnail">
                <?php if (has_post_thumbnail()) : ?>
                  <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endwhile; ?>

        <div id="pagination" class="custom-pagination">
          <?php echo paginate_links([
            'prev_text' => 'قبلی →',
            'next_text' => '← بعدی',
            'type' => 'list',
          ]); ?>
        </div>

      <?php else : ?>
        <article class="post error"><h2>هیچ مطلبی برای نمایش وجود ندارد.</h2></article>
      <?php endif; ?>

    <?php endif; ?>

  </main>

  <!-- ===================== Footer ===================== -->
  <footer class="site-footer">
    <div class="footer-widgets">
      <div class="footer-col"><?php if (is_active_sidebar('footer-1')) dynamic_sidebar('footer-1'); ?></div>
      <div class="footer-col"><?php if (is_active_sidebar('footer-2')) dynamic_sidebar('footer-2'); ?></div>
      <div class="footer-col"><?php if (is_active_sidebar('footer-3')) dynamic_sidebar('footer-3'); ?></div>
    </div>
    <div class="footer-bottom">
      <div class="footer-custom-widget"><?php if (is_active_sidebar('footer-bottom-widget')) dynamic_sidebar('footer-bottom-widget'); ?></div>
      <div class="copyright-text">
  <p>
    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?> – همه حقوق محفوظ است.
    | طراحی شده با <a href="https://github.com/iMoein/iminimal" target="_blank" rel="noopener nofollow" title="قالب وردپرس آی‌مینیمال">آی‌مینیمال</a>
  </p>
</div>

    </div>
  </footer>

</div>

<?php get_footer(); ?>
