<?php
/*
 * 
 * WordPress widget that displays custom news in the MOJ intranet theme
 * 
 **/

class CustomFeatureNews extends WP_Widget {
  function CustomFeatureNews() {
    parent::WP_Widget(false, 'DW Custom Featured news', array('description' => 'Display features news stories selected in the customizer'));
  }

  function widget($args, $instance) {
    extract( $args );
    $title = apply_filters('widget_title', $instance['title']);
    $containerclasses = $instance['containerclasses'];

    global $post;
    echo $before_widget;
    echo '<div class="ht-feature-news clearfix '.$containerclasses.'">';
    if ( $title ) echo $before_title . $title . $after_title;

    //forumalate grid of news stories and formats
    $newsgrid=array();

    for ($i = 1; $i <= 2; $i++) {
      $newsgrid[] = "M";
    }

    $siteurl = site_url();

    // Get stored story IDs (set in customizer)
    $featured_story1 = get_option('featured_story1');
    $featured_story2 = get_option('featured_story2');

    //display remaining stories
    $cquery = array(
      'orderby'         => 'post__in',
      'order'           => 'ASC',
      'post_type'       => 'news',
      'posts_per_page'  => 2,
      'post__in'        => array($featured_story1,$featured_story2)
    );

    $news =new WP_Query($cquery);
    if ($news->post_count==0){
      echo "Nothing to show.";
    }

    $k=-1;
    while ($news->have_posts()) {
      $news->the_post();

      $k++;

      if ($k >= 2){
        break;
      }

      $container_class = 'news-medium';

      echo'<div class="news-item '.$container_class.'">';

      $thistitle = get_the_title($news->ID);
      $thisURL=get_permalink($news->ID);

      $image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $news->ID ), 'medium' );
      if ($image_uri!="" && $videostill==''){
        echo "<a href='{$thisURL}'><img src='{$image_uri[0]}' alt='".govintranetpress_custom_title($slot)."' /></a>";
      }

      $thisdate= get_the_date();
      $thisexcerpt= get_the_excerpt();
      $thisdate=date("j M Y",strtotime($thisdate));

      echo "<p class='news-date-wrapper'><span class='news_date'>".$thisdate."</span></p>";

      echo "<h3 class='noborder'><a class='' href='".$thisURL."'>".$thistitle."</a></h3>";

      echo "<div class='media-body'>";

      echo '<p class="excerpt">'.$thisexcerpt.'</p>';

      echo "</div>";

      echo'</div>';
    }

    ?>

      <div class="category-block more-in-news">
        <p>
          <a title='More in news' class="small" href="<?php echo $siteurl; ?>/newspage/">See all</a>
        </p>
      </div>

    </div>

    <?php
    wp_reset_query();
    ?>

    <?php echo $after_widget;
  }

  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['containerclasses'] = strip_tags($new_instance['containerclasses']);
    return $instance;
  }

  function form($instance) {
    $title = esc_attr($instance['title']);
    $containerclasses = esc_attr($instance['containerclasses']);
    ?>
     <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

      <label for="<?php echo $this->get_field_id('containerclasses'); ?>"><?php _e('Extra container classes'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('containerclasses'); ?>" name="<?php echo $this->get_field_name('containerclasses'); ?>" type="text" value="<?php echo $containerclasses; ?>" /><br><br>
    </p>

    <?php
  }

}

add_action('widgets_init', create_function('', 'return register_widget("CustomFeatureNews");'));
