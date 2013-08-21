<?php
/*
Plugin Name: SoloTodo Paginator
*/

function my_wp_link_page( $i ) {
  global $post, $wp_rewrite;

  if ( 1 == $i ) {
    $url = get_permalink();
  } else {
    if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
      $url = add_query_arg( 'page', $i, get_permalink() );
    elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
      $url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
    else
      $url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
  }

  return esc_url( $url );
}

function solotodo_append_paginator($content) {
  global $post, $page, $numpages;

  if (!is_single()) {
    return $content;
  }
  
  $subject = $post->post_content;
  $pattern = '/<h1>(?P<name>.*)<\/h1>/';
  $num_matches = preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
  $matches = $matches['name'];
            
  $content .= '<div id="solotodo_pagination_container">';

  $content .= '<div>';
  if ($page > 1) {
      $content .= '<a style="float: left" href="' . my_wp_link_page($page - 1) .'">« ' . $matches[$page - 2][0] . '</a>';
  }
  
  if ($page < $numpages && $numpages > 1) {
      $content .= '<a style="float:right; text-align: right;" href="' . my_wp_link_page($page + 1) . '">' . $matches[$page][0] . ' »</a>';
  }
  $content .= '</div>';

  if ($num_matches > 1) {
      $content .=  '<select class="custom_paginator">';
      foreach ($matches as $i => $value) {
          $content .=  '<option value="' . my_wp_link_page($i + 1) . '"';
          if ($i == $page - 1) {
              $content .=  ' selected="selected"';
          }
          $content .=  '>' . ($i + 1) . ' - ' . $value[0] . '</option>';
      }
      $content .=  '</select>';
  }

  $content .= '</div>';
  
  return $content;
}

function solotodo_append_header_data() {
  wp_register_style('solotodo-pagination-style', plugins_url('/css/solotodo-pagination-style.css', __FILE__ ));
  wp_enqueue_style('solotodo-pagination-style');

  wp_enqueue_script('jquery');

  wp_register_script('solotodo-pagination-script', plugins_url('/js/solotodo-pagination-script.js', __FILE__ ));
  wp_enqueue_script('solotodo-pagination-script');
}

add_filter('the_content', 'solotodo_append_paginator');
add_action('wp_enqueue_scripts', 'solotodo_append_header_data');

?>
