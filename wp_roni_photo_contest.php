<?php

/*
*  Plugin name: Custom Photo Contest
*  Plugin URI: http://github.com/janezsarlah/wp-plugin-pc/
*  Description: Lets users upload photos and vote on them. The photo with the most votes wins a prize.
*  Version: 1.0
*  Author: <a href="http://janez.theveloper.si" target="_blank">Janez Šarlah</a>
*  Author URI: http://janez.theveloper.si
*  Licence: GPL2
*/

session_start();

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'lib/MediaUpload.php';
require_once 'lib/ResizeImage.php';

date_default_timezone_set('Europe/Ljubljana');


// Check if class exists
if ( !class_exists('PhotoContest') ) {
  class PhotoContest {

    public $upload_dir;
    public $errors = [];

    public function __construct() {
      $this->upload_dir = wp_upload_dir();
      $this->initialize_options();
      return;
    }

    /*
    * Initialize the plugin
    */
    public function initialize_options() {
      add_option( 'wp_roni_photo_contest_same_voted', '1' );
      add_option( 'wp_roni_photo_contest_tracking', 'ipaddress' );
      add_option( 'wp_roni_photo_contest_custom_css', '.gallery { border: 5px solid red; }' );
      add_option( 'wp_roni_photo_contest_subtitle', 'Contest title' );
      add_option( 'wp_roni_photo_contest_description', 'Contest description.' );

      wp_insert_term('Roni gallery', 'category', array(
        'slug' => 'roni-gallery'
      ));

      // Database object
      global $wpdb;

      // Database table names
      $image_table_name = $wpdb->prefix . 'roni_images';
      $votes_table_name = $wpdb->prefix . 'roni_votes';

      // If table roni_images doesn't exist create it
      $query = "show tables like '" . $image_table_name . "';";
      if(!$wpdb -> get_var($query))	{
        $query = "create table " . $image_table_name . " ( ";
        $query .= "`images_id` int not null auto_increment, ";
        $query .= "`attachment_id` int(11) not null default '0', ";
        $query .= "`title` varchar(30) not null default '',";
        $query .= "`description` varchar(100) not null default '',";
        $query .= "`image_path` varchar(1000) not null, ";
        $query .= "`thumbnaul_path` varchar(1000) not null, ";
        $query .= "`author_name` varchar(50) not null, ";
        $query .= "`author_email` varchar(50) not null, ";
        $query .= "`number_of_votes` int not null default '0', ";
        $query .= "`image_status` int not null default '0', ";
        $query .= "`created` datetime not null default '00-00-0000 00:00:00', ";
        $query .= "PRIMARY KEY (`images_id`)";
        $query .= ") ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=UTF8 COLLATE=utf8_general_ci;";

        $wpdb -> query($query);
    	}

      // If table roni_votes doesn't exist create it
      $query = "show tables like '" . $votes_table_name . "';";
      if(!$wpdb->get_var( $query ))	{
        $query = "create table " . $votes_table_name . " ( ";
        $query .= "`id` int not null auto_increment, ";
        $query .= "`ip_address` varchar(100) not null, ";
        $query .= "`attachment_id` int not null default '0', ";
        $query .= "`created` datetime not null default '00-00-0000 00:00:00',";
        $query .= "PRIMARY KEY (`id`)";
        $query .= ") ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=UTF8 COLLATE=utf8_general_ci;";

        $wpdb->query($query);
    	}
    }

    /*
    * Create shortcode for photo contest front end
    * Used for image upload 
    */
    public function wp_roni_photo_contest_shortcode( $attr, $content = null ) {
      global $post;
   
      $data = [];
      
      // Checks if there was a POST request
      if( $_POST ) {

        if( empty( $_POST['author-name'] ) ) {
          $this->errors[] = 'Pozabili ste vnesti svoje ime!';
        }

        if( empty( $_POST['author-email'] ) ) {
          $this->errors[] = 'Pozabili ste vnesti svoj e-mail!';
        } 

        /* 
        * TODO: Email already exists
        * This check is implemented bot not used
        */ 
        /*if( emailExists( filter_var( $_POST['author_email'], FILTER_SANITIZE_EMAIL ) ) ) {
          $this->errors[] = 'Ta email je že obstaja';
        }*/

        if( $_FILES['file-upload']['error'] == 4 ) {
          $this->errors[] = 'Pozabili ste vstaviti sliko!';
        } 

        if( $_FILES['file-upload']['size'] > 1000000 ) {
          $this->errors[] = 'Slike je prevelika!. Naložite manjšo sliko! Maksimalno 1MB.';
        }

        $image_type = strtolower( substr( strrchr( $_FILES['file-upload']['type'], '/' ), 1 ) );

        if( $image_type != 'jpg' && $image_type != 'jpeg' && $image_type != 'png' && $image_type != 'gif' && $image_type != '' ) {
          $this->errors[] = 'Format ' . '.' . $image_type . ' ni podprt. Podprti so samo .jpg, .jpeg, .png in .gif!';
        }  

        
        if( !empty( $this->errors ) ) {
          $_SESSION['errors'] = $this->errors;
        } else {
          $name = $_POST['author-name'];
          $email = $_POST['author-email'];
          $title = $_POST['image-title'];
          $description = $_POST['image-description'];

          $upload = new MediaUpload;
          $file = $upload->saveUpload( $field_name = 'file-upload' );

          $original_path = $this->upload_dir['baseurl'] . '/' . _wp_relative_upload_path( $file['file'] );
          
          $resize_image = new ResizeImage( $original_path );
          $resize_image->resizeImage( 350, 350, 'crop' );

          $crop_path = 'wp-content/uploads/tekmovanje' . $this->upload_dir['subdir'] . '/' . $file['file_info']['filename'] . '-350x350' . '.' . $file['file_info']['extension'];

          $resize_image->saveImage( $crop_path, 100 );
         
          $data['attachment_id'] = $file['attachment_id'];
          $data['title'] = $title;
          $data['description'] = $description;
          $data['attachment_id'] = $file['attachment_id'];
          $data['image_path'] = $original_path;
          $data['image_thumb_path'] = $this->upload_dir['baseurl'] . '/' . _wp_relative_upload_path( $file['file_info']['dirname'] ) . '/' . $file['file_info']['filename'] . '-350x350' . '.' . $file['file_info']['extension'];
          $data['author_name'] = $name;
          $data['author_email'] = $email;
          $data['number_of_votes'] = 0;
          $data['created'] = date('d.m.Y G:i:s');

          $save_data = $this->saveToDatabse($data);

          # TODO: Database error handler not implemented yet 

          $_SESSION['success'] = 'Slika je bila uspešno naložena!';

          # TOO: Create post for every image upload 
          $my_post = array(
            'post_title'    => 'Nov vnos',
            'post_content'  => 'This is my post.',
            'post_status'   => 'publish',
            'post_author'   => 1
          );

          //wp_insert_post( $my_post );
          
        } 
      }

      $images_data = $this->getImages();
      $contest_subtitle = get_option( 'wp_roni_photo_contest_subtitle' );
      $contest_description = get_option( 'wp_roni_photo_contest_description' );

      // Get the front end ( form, and images grid )
      ob_start();
      require( 'inc/front-end.php' );
      $content = ob_get_clean();

      session_destroy();

      return $content;
    }


    /*
    * Verify that the email doesn't already exist
    */
    private function emailExists( $email ) {
      global $wpdb;
      $query = "select author_email from " . $wpdb->prefix . "roni_images where author_name='" . $email . "';";
      if( empty($wpdb->query( $query ) ) ) {
        return true;
      } else {
        return false;
      }
    }


    /*
    * Update tables author
    */
    private function saveToDatabse($data) {
      global $wpdb;
      $query = "insert into `" . $wpdb->prefix . "roni_images` (`attachment_id`, `title`, `description`, `image_path`, `image_thumb_path`, `author_name`, `author_email`, `number_of_votes`, `created`) values ('" . $data['attachment_id'] . "', '" . $data['title'] . "' , '" . $data['description'] . "', '" . $data['image_path'] . "', '" . $data['image_thumb_path'] . "', '" . $data['author_name'] . "', '" . $data['author_email'] . "', '" . $data['number_of_votes'] . "', '" . date("Y-m-d H:i:s") . "'); ";

      if ( $wpdb -> query( $query ) ) {
        return true;
      } else {
        return false;
      }
    }


    /*
    * Get everything from the table roni_images
    */
    private function getImages() {
      global $wpdb;
      $query = "select * from `" . $wpdb->prefix . "roni_images` where image_status = 1;";
      $results = $wpdb->get_results( $query );
      if( !empty( $results ) ) {
        return $results;
      }

      return false;
    }


    /*
    * Get everything from the table roni_images
    */
    private function getImagesForAdmin() {
      global $wpdb;
      $query = "select * from `" . $wpdb->prefix . "roni_images`;";
      $results = $wpdb->get_results( $query );
      if( !empty( $results ) ) {
        return $results;
      }

      return false;
    }


    /*
    * Saves new voter IP Address
    */
    private function saveVote( $ipaddress, $attachment_id ) {
      global $wpdb;
      $query = "insert into " . $wpdb->prefix . "roni_votes (`ip_address`, `attachment_id`, `created`) values ('" . $ipaddress . "', '" . $attachment_id . "', '" . date("Y-m-d H:i:s") . "') ";
      if( $wpdb->query( $query ) ) {
        return true;
      } else {
        return false;
      }
    }


    /*
    * Updates image data ( adds +1 vote to voted image )
    */
    private function updateImage( $attachment_id ) {
      global $wpdb;
      $query = "select number_of_votes from " . $wpdb->prefix . "roni_images where attachment_id='" . $attachment_id . "';";
      $num = ( $wpdb->get_var( $query ) ) + 1;
      
      $query = "update " . $wpdb->prefix . "roni_images set number_of_votes='" . $num . "' where attachment_id='" . $attachment_id . "';";
      if( $wpdb->query( $query ) ) {
        return true;
      } else {
        return false;
      }
    }


    /*
    * Checks if a voter with this IP already voted
    */
    private function checkIfVoterVoted( $ipaddress, $attachment_id ) {
      global $wpdb;
      $query = "select count(`id`) from " . $wpdb->prefix . "roni_votes where ip_address='" . $ipaddress . "' and attachment_id='" . $attachment_id . "';";
      return $wpdb->get_var( $query );
    }


    /*
    * Update a photo status
    */
    private function updateStatus( $attachment_id, $upload_status ) {
      global $wpdb;
      $upload_status = ( $upload_status == 1 ? 0 : 1 );     
      
      $query = "update " . $wpdb->prefix . "roni_images set image_status='" . $upload_status . "' where attachment_id='" . $attachment_id . "';";
      if( $wpdb->query( $query ) ) {
        return true;
      } else {
        return false;
      }
    }


    /*
    * For debuging purposes
    */
    private function debug($var = null) {
      if( $var ) {
        echo '<pre>';
        print_r( $var );
        echo '</pre>';
      }
    }


    /*
    * Ajax request
    * For voting
    */
    public function wp_roni_photo_contest_footer() {
      ?>

      <script type="text/javascript">
        var votingajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

        function galleryVote(attachment_id) {
          jQuery('#image-vote-' + attachment_id).hide(); 
          jQuery('span#gallery-vote-loading-' + attachment_id).show();
          
          jQuery.post( votingajaxurl + "?action=imagevote", { attachment_id:attachment_id }, function( response ) {
      
            jQuery('span#gallery-vote-loading-' + attachment_id).hide();
            jQuery('#image-vote-' + attachment_id).show(); 

            var data = JSON.parse(response);
            
            if( data.success == true ) {
              jQuery('#image-vote-' + attachment_id).text( data.count );
            } else {
              alert( data.error );
            }

          });
        }

      </script>

      <?php
    }

    /*
    * Activate when all plugin are loaded
    */
    public function wp_roni_photo_contest_plugins_loaded() {
      remove_shortcode( 'photo_contest' );
      add_shortcode( 'photo_contest', array( $this, 'wp_roni_photo_contest_shortcode' ) );
    }


    /*
    * When the plugin is deactivated it dropes tables roni_votes and roni_images
    * Function is deactivated! Uncomment to activate!
    *
    * TODO: DROP TABLE not yet implementet
    */
    public function wp_roni_photo_contest_drop_plugin_tables() {
      global $wpdb;
      delete_option( 'wp_roni_photo_contest_same_voted' );
      delete_option( 'wp_roni_photo_contest_tracking' );
      delete_option( 'wp_roni_photo_contest_custom_css' );
      //$quer = "drop table if exist " . $wpdb->prefix . "roni_votes;";
      //$wpdb->query( $quer );
    }


    /*
    * Ajax handler
    */
    public function wp_roni_photo_contest_vote() {
      global $wpdb;

      $ipaddress = $_SERVER['REMOTE_ADDR'];
      $max_same = get_option('wp_roni_photo_contest_same_voted');
      $traking = get_option('wp_roni_photo_contest_tracking');

      $error = false;
      $success = false;

      $traking = 'cookie';


      if( !empty($_POST) && !empty($_POST['attachment_id'])) {
        $attachment_id = $_POST['attachment_id'];

        switch ( $traking ) {
          case 'ipaddress':
            
            $vote_exists = $this->checkIfVoterVoted( $ipaddress, $attachment_id );  

            if( empty( $vote_exists ) || $vote_exists < $max_same ) {

              $save_voter = $this->saveVote( $ipaddress, $attachment_id );

              $update_image_data = $this->updateImage( $attachment_id );

              if( $save_voter ) {
                $success = true;
              } else {
                $error = 'Vaš glas ni bilo mogoče shraniti! Ponovno naložite srtran in poskusite še enkrat.';
              }

              if( $update_image_data ) {
                $success = true;
              } else {
                $error = 'Vaš glas ni bilo mogoče shraniti! Ponovno naložite srtran in poskusite še enkrat.'; 
              }

            } else {
              $error = sprintf( 'Za to sliko ste že glasovali!' );
            }

            break;
          
          case 'cookie':
          default:
            
            $vote_exists = ( empty( $_COOKIE['gallery_vote_image_'.$attachment_id] ) ) ? 0 : $_COOKIE['gallery_vote_image_'.$attachment_id];

            if( empty( $vote_exists ) || $vote_exists < $max_same ) {

              $save_voter = $this->saveVote( $ipaddress, $attachment_id );

              $update_image_data = $this->updateImage( $attachment_id );

              if( $save_voter ) {
                $success = true;

                setcookie( 'gallery_vote_image_' . $attachment_id , ( $vote_exists + 1 ), ( time() + 60 * 60 * 24 * 30 ) );

              } else {
                $error = 'Vaš glas ni bilo mogoče shraniti! Ponovno naložite srtran in poskusite še enkrat.';
              }

              if( $update_image_data ) {
                $success = true;
              } else {
                $error = 'Vaš glas ni bilo mogoče shraniti! Ponovno naložite srtran in poskusite še enkrat.'; 
              }
            } else {
              $error = sprintf( 'Za to sliko ste že glasovali!' );
            }

            break;
        }

      } else {
        $error = 'Dobeni podatki niso bili navedeni! Obrnite se na lastnika spletne strani!';
      }


      $query = "select number_of_votes from " . $wpdb->prefix . "roni_images where attachment_id='" . $attachment_id . "';";
      $count = $wpdb->get_var( $query );

      if( empty( $error ) ) {
        $data = array(
          'success' => true,
          'count'   => $count
        );
      } else {
        $data = array(
          'success' => false,
          'error' => $error,
          'count'   => $count
        );
      }

      echo json_encode( $data );

      die();
    }


    /*
    * Where the plugin options are going to be accessible
    */
    public function wp_roni_photo_contest_admin_menu() {
      add_menu_page( 'Tekmovanje', 'Tekmovanje', 'manage_options', 'contest-options', array( $this, 'admin' ) );
    }


    /*
    * Admin options
    */
    public function admin() {
      if( !current_user_can( 'manage_options' ) )
        wp_die( 'You do not have permission to access this page!' );

      $attachment_id = 0;
      $upload_status = 0;
      if( $_POST ) {
        if( isset( $_POST['attachment_id'] ) && isset( $_POST['upload_status'] ) ) {
          $attachment_id = $_POST['attachment_id']; 
          $upload_status = $_POST['upload_status']; 

          $update_image_data = $this->updateStatus( $attachment_id, $upload_status );

          if( !$update_image_data ) {
            echo "Nastala je napaka. Kontaktirajte razvijalca vmesnika!";
          }
        }

        if( isset( $_POST['subtitle'] ) ) {
          $subtitle = $_POST['subtitle'];
          update_option( 'wp_roni_photo_contest_subtitle', $subtitle );
        }

        if( isset( $_POST['description'] ) ) {
          $description = $_POST['description'];
          update_option( 'wp_roni_photo_contest_description', $description );
        }
      }

      $uploads = $this->getImagesForAdmin();
      $contest_subtitle = get_option( 'wp_roni_photo_contest_subtitle' );
      $contest_description = get_option( 'wp_roni_photo_contest_description' );

      require( 'inc/menu-page-wrapper.php' );
    }

    /*
    * Load styles and scripts
    */
    function wp_roni_photo_contest_frontend_scripts_and_styles() {
      wp_enqueue_style( 'wpphotocontest_frontend_bootstrap_css', plugins_url( 'css/bootstrap.min.css', __FILE__ ) );
      wp_enqueue_style( 'wpphotocontest_frontend_font_awesome_css', plugins_url( 'css/font-awesome.min.css', __FILE__ ) );
      wp_enqueue_style( 'wpphotocontest_frontend_lightbox_css', plugins_url( 'css/lightbox.css', __FILE__ ) );
      wp_enqueue_style( 'wpphotocontest_frontend_custom_css', plugins_url( 'css/styles.css', __FILE__ ) );
      wp_enqueue_script( 'wpphotocontest_frontend_custom_script', plugins_url( 'wp_roni_photo_contest/js/scripts.js' ), array('jquery'), '', true);
      wp_enqueue_script( 'wpphotocontest_frontend_lightbox_script', plugins_url( 'wp_roni_photo_contest/js/lightbox.js' ), array('jquery'), '', true);
    } 
  }

  /*
  * Declare an instance of PhotoContest class
  */
  $PhotoContest = new PhotoContest();
  add_action('plugins_loaded', array($PhotoContest, 'wp_roni_photo_contest_plugins_loaded'), 1, 1);
  add_action( 'wp_enqueue_scripts', array($PhotoContest, 'wp_roni_photo_contest_frontend_scripts_and_styles'), 10, 1 );

  add_action('wp_head', array($PhotoContest, 'wp_roni_photo_contest_footer'), 10, 1 );
  add_action('admin_menu', array($PhotoContest, 'wp_roni_photo_contest_admin_menu'), 10, 1);
  add_action('wp_ajax_imagevote', array($PhotoContest, 'wp_roni_photo_contest_vote'), 10, 1);
  add_action('wp_ajax_nopriv_imagevote', array($PhotoContest, 'wp_roni_photo_contest_vote'), 10, 1);

  //register_deactivation_hook( __FILE__, array($PhotoContest, 'wp_roni_photo_contest_drop_plugin_tables') );

}
