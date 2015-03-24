<?php
/*
Plugin Name: DW Feature news
Description: Display featured news
Version: 0.2

Branched from HT widget
*/

class htFeatureNews extends WP_Widget {
  function htFeatureNews() {
    parent::WP_Widget(false, 'DW Feature news', array('description' => 'Display feature news stories'));
  }

  function widget($args, $instance) {
    extract( $args );
    $title = apply_filters('widget_title', $instance['title']);
    $largeitems = intval($instance['largeitems']);
    $mediumitems = intval($instance['mediumitems']);
    $thumbnailitems = intval($instance['thumbnailitems']);
    $listitems = intval($instance['listitems']);

    $containerclasses = $instance['containerclasses'];

    $newstype = $instance['newstype'];

    global $post;
    echo $before_widget;
    echo '<div class="ht-feature-news clearfix '.$containerclasses.'">';
    if ( $title ) echo $before_title . $title . $after_title;

    //forumalate grid of news stories and formats
    $totalstories =  $largeitems + $mediumitems + $thumbnailitems + $listitems;

    $newsgrid=array();

    for ($i = 1; $i <= $totalstories; $i++) {
			if ($i <= $largeitems) {
				$newsgrid[] = "L";
			}
			elseif ($i <= $largeitems + $mediumitems) {
				$newsgrid[] = "M";
			}
			elseif ($i <= $largeitems + $mediumitems + $thumbnailitems) {
				$newsgrid[] = "T";
			}
			elseif ($i <= $largeitems + $mediumitems + $thumbnailitems + $listitems) {
				$newsgrid[] = "Li";
			}
		}

    $siteurl = site_url();

    // Get stored story IDs (set in customizer)
    $featured_story1 = get_option('featured_story1');
    $featured_story2 = get_option('featured_story2');
    $need_to_know_story1 = get_option('need_to_know_story1');
    $need_to_know_story2 = get_option('need_to_know_story2');
    $need_to_know_story3 = get_option('need_to_know_story3');

    /**
     * Array of stories to be hidden in main news widget
     * @var array
     */
    $hidden_stories = array(
      $featured_story1,
      $featured_story2,
      $need_to_know_story1,
      $need_to_know_story2,
      $need_to_know_story3
    );

    //display remaining stories
    $cquery = array(
			'orderby' => 'post_date',
      'order' => 'DESC',
      'post_type' => 'news',
      'posts_per_page' => $totalstories,
      'meta_query'=>array(array(
        'key'=>'news_listing_type',
        'value'=>$newstype,
      )),
      'post__not_in' => $hidden_stories
    );

		$news =new WP_Query($cquery);
		if ($news->post_count==0){
			echo "Nothing to show.";
		}

    $k=-1;
		while ($news->have_posts()) {
			$news->the_post();

			$k++;

			if ($k >= $totalstories){
				break;
			}

      if ($newsgrid[$k]=="L"){
        $container_class = 'news-large';
      }
      elseif ($newsgrid[$k]=="M"){
        $container_class = 'news-medium';
      }
      elseif ($newsgrid[$k]=="T"){
        $container_class = 'news-thumbnail';
      }

      echo'<div class="news-item '.$container_class.'">';

			$thistitle = get_the_title($news->ID);
			$thisURL=get_permalink($news->ID);

			if ($newsgrid[$k]=="L"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $news->ID ), 'newshead' );
				if ($image_uri!="" && $videostill==''){
					echo "<a href='{$thisURL}'><img src='{$image_uri[0]}' alt='".govintranetpress_custom_title($slot)."' /></a>";
				}
			}

			if ($newsgrid[$k]=="M"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $news->ID ), 'medium' );
				if ($image_uri!="" && $videostill==''){
					echo "<a href='{$thisURL}'><img src='{$image_uri[0]}' alt='".govintranetpress_custom_title($slot)."' /></a>";
				}
			}

			if ($newsgrid[$k]=="T"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $news->ID ), 'thumbnail' );
				if ($image_uri!="" && $videostill==''){
					echo "<a href='{$thisURL}'><img src='{$image_uri[0]}' alt='".govintranetpress_custom_title($slot)."' /></a>";
				}
			}

			$thisdate= get_the_date();
			$thisexcerpt= get_the_excerpt();
			$thisdate=date("j M Y",strtotime($thisdate));


			if ($newsgrid[$k]=="T"){
				echo "<div class='media'>".$image_url;
			}

			if ($newsgrid[$k]=="Li"){
				echo "<p><span class='news_date'>".$thisdate."";
				echo " <a class='more' href='{$thisURL}' title='{$thistitle}'>Read more</a></span></p>";
			} elseif($newsgrid[$k]!="T"){
				echo "<p class='news-date-wrapper'><span class='news_date'>".$thisdate."</span></p>";
			}

			echo "<h3 class='noborder'><a class='' href='".$thisURL."'>".$thistitle."</a></h3>";

			echo "<div class='media-body'>";

      if($newsgrid[$k]=="T"){
				echo "<p class='news-date-wrapper'><span class='news_date'>".$thisdate."</span></p>";
			}

			if ($newsgrid[$k]!="Li"){
        echo '<p class="excerpt">'.$thisexcerpt.'</p>';
				echo "<p class='news_date'>";
				echo "<a class='more' href='{$thisURL}' title='{$thistitle}'>Read more</a></p>";
			}

				echo "</div>";

			if ($newsgrid[$k]=="T"){
				echo "</div>";
			}

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
    $instance['largeitems'] = strip_tags($new_instance['largeitems']);
    $instance['mediumitems'] = strip_tags($new_instance['mediumitems']);
    $instance['thumbnailitems'] = strip_tags($new_instance['thumbnailitems']);
    $instance['listitems'] = strip_tags($new_instance['listitems']);
    $instance['containerclasses'] = strip_tags($new_instance['containerclasses']);
    $instance['newstype'] = strip_tags($new_instance['newstype']);
    return $instance;
  }

  function form($instance) {
    $title = esc_attr($instance['title']);
    $largeitems = esc_attr($instance['largeitems']);
    $mediumitems = esc_attr($instance['mediumitems']);
    $thumbnailitems = esc_attr($instance['thumbnailitems']);
    $listitems = esc_attr($instance['listitems']);
    $containerclasses = esc_attr($instance['containerclasses']);
    $newstype = esc_attr($instance['newstype']);
    ?>
     <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
      <label>Number of stories</label><br>
      <label for="<?php echo $this->get_field_id('largeitems'); ?>"><?php _e('Large'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('largeitems'); ?>" name="<?php echo $this->get_field_name('largeitems'); ?>" type="text" value="<?php echo $largeitems; ?>" /><br><br>

      <label for="<?php echo $this->get_field_id('mediumitems'); ?>"><?php _e('Medium'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('mediumitems'); ?>" name="<?php echo $this->get_field_name('mediumitems'); ?>" type="text" value="<?php echo $mediumitems; ?>" /><br><br>

      <label for="<?php echo $this->get_field_id('thumbnailitems'); ?>"><?php _e('Thumbnail'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('thumbnailitems'); ?>" name="<?php echo $this->get_field_name('thumbnailitems'); ?>" type="text" value="<?php echo $thumbnailitems; ?>" /><br><br>

      <label for="<?php echo $this->get_field_id('listitems'); ?>"><?php _e('List format (no photos)'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('listitems'); ?>" name="<?php echo $this->get_field_name('listitems'); ?>" type="text" value="<?php echo $listitems; ?>" /><br><br>

      <label for="<?php echo $this->get_field_id('containerclasses'); ?>"><?php _e('Extra container classes'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('containerclasses'); ?>" name="<?php echo $this->get_field_name('containerclasses'); ?>" type="text" value="<?php echo $containerclasses; ?>" /><br><br>

      <label for="<?php echo $this->get_field_id('newstype'); ?>"><?php _e('News type'); ?></label>
      <select class="widefat" id="<?php echo $this->get_field_id('newstype'); ?>" name="<?php echo $this->get_field_name('newstype'); ?>">
          <option value='0' <?=$newstype==0?'selected':'' ?>>Regular</option>
          <option value='1' <?=$newstype==1?'selected':'' ?>>Need to know</option>
          <option value='2' <?=$newstype==2?'selected':'' ?>>Featured</option>
      </select>

    </p>

    <?php
  }

}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureNews");'));

include_once('custom_featured_widget.php');

?>
