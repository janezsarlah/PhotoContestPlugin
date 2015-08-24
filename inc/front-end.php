
<?php $errors = isset( $_SESSION['errors'] ) ? $_SESSION['errors'] : []; ?>
<?php $success = isset( $_SESSION['success'] ) ? $_SESSION['success'] : []; ?>

<div class="row">
  <div class="col-md-12">
    <?php if( !empty( $success ) ): ?>
      <div class="alert alert-success" role="alert">
        <span class="fa fa-check"></span>
        <?php echo $success; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php if( !empty( $errors ) ): ?>
	<div class="row">
		<div class="col-md-12">
		  <?php foreach ( $errors as $error ): ?>
		  	<div class="alert alert-danger" role="alert">
		  		<span class="fa fa-exclamation"></span>
		  		<?php echo $error; ?>
		  	</div>
		  <?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>


<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-sm-6">
      <h3>Navodila</h3>
      <p>
         Sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
      </p>
    </div>
    <div class="col-md-4 col-sm-6">
      <div id="form" class="form-wrapper">
        <h3>Naložite sliko</h3>
        <form id="login" action=""  method="post" enctype="multipart/form-data">
          <div class="form-group">
            <input type="text" class="form-control" name="author-name" id="name" placeholder="Ime">
          </div>
          <div class="form-group">
            <input type="email" class="form-control" name="author-email" id="email" placeholder="Email">
          </div>
          <div class="form-group">
            <label class="file-label" for="upload-image"><p style="color:#555;"><i class="fa fa-upload" style="margin-right:10px;"></i>Naložite sliko...</p></label>
            <input style="display: none;" type="file" id="upload-image" name="file-upload" />
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="image-title" id="name" placeholder="Naslov">
          </div>
          <div class="form-group">
            <textarea class="form-control" name="image-description" rows="3" id="description" placeholder="Opis"></textarea>
          </div>
          <input type="submit" name="submit" id="submit-form" class="btn btn-default" value="Pošlji" />
        </form>
      </div>
    </div>
  </div>




  <div class="row">

    <?php if( !empty( $images_data ) ): ?>

      <?php foreach ($images_data as $image): ?>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

          <div class="image-container">

            <div class="border">

              <a href="<?php echo $image->image_path; ?>" data-lightbox="image" data-title="<?php echo $image->title; ?>"><img class="" src="<?php echo $image->image_thumb_path; ?>"/></a>
              
              <div class="clear"></div>
              <div class="text-contaner">

                <div class="row">
                  <div class="col-md-7 col-lg-7 col-sm-7 col-xs-7 image-author">

                    <div class="author">
                      <i class="fa fa-user"></i>
                      <?php 
                        $author_name = explode( ' ', $image->author_name );
                        echo $author_name[0];
                      ?>
                    </div>
                    
                  </div>
                  
                  <div class="col-md-5 col-lg-5 col-sm-5 col-xs-5 image-votes">
                  
                    <div class="icon">
                      <a class='inline' title="Glasuj" href="#" onclick="galleryVote('<?php echo $image->attachment_id; ?>'); return false;">
                        <i class="fa fa-heart"></i>                        
                      </a>
                    </div>
                  
                    <div class="votes" id="image-vote-<?php echo $image->attachment_id; ?>">
                      <?php echo $image->number_of_votes; ?>
                    </div>

                    <div class="vote-loader">
                      <span style="display: none;" id="gallery-vote-loading-<?php echo $image->attachment_id; ?>"><img src="<?php echo plugins_url() . '/wp_roni_photo_contest/img/loading_vote.gif'; ?>" /></span>
                    </div>
                  
                  </div>

                </div>
                  
               </div>
            
            </div>

          </div>
            
        </div>

      <?php endforeach; ?>

    <?php endif; ?>

  </div>

</div>


