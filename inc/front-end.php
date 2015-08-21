
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




<div class="row">
  <div class="col-md-8">
    <h3>Lorem ipsum dolor</h3>
    <p>
       Sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </p>
  </div>
  <div class="col-md-4">
    <div id="form" class="form-wrapper">
      <h3>Upload image</h3>
      <form id="login" action=""  method="post" enctype="multipart/form-data">
        <div class="form-group">
          <input type="text" class="form-control" name="author-name" id="name" placeholder="Name">
        </div>
        <div class="form-group">
          <input type="email" class="form-control" name="author-email" id="email" placeholder="Email">
        </div>
        <div class="form-group">
          <input type="file" id="upload-image" name="file-upload" />
        </div>
        <div class="form-group">
          <input type="text" class="form-control" name="image-title" id="name" placeholder="Title">
        </div>
        <div class="form-group">
          <textarea class="form-control" name="image-description" rows="3" id="description" placeholder="Description"></textarea>
        </div>
        <input type="submit" name="submit" id="submit-form" class="btn btn-default" value="Pošlji" />
      </form>
    </div>
  </div>
</div>




<div class="row">

  <?php if( !empty( $images_data ) ): ?>

    <?php foreach ($images_data as $image): ?>

      <div class="col-lg-4 col-md-4 col-sm-6">

        <img src="<?php echo $image->image_thumb_path; ?>"/>
        <div class="clear"></div>
        <div class="image-author">
          <div class="icon">
            <i class="fa fa-user"></i>
          </div>
          <div class="author">
            <?php echo $image->author_name ?>
          </div>
        </div>
        <div class="clear"></div>
        <div class="image-votes">
          <div class="icon">
            <a class='inline' title="Glasuj" href="#" onclick="galleryVote('<?php echo $image->attachment_id; ?>'); return false;">
              <i class="fa fa-heart"></i>
              <span style="display: none;" id="gallery-vote-loading-<?php echo $image->attachment_id; ?>"><img src="<?php echo plugins_url() . '/wp_roni_photo_contest/img/loading.gif'; ?>" /></span>
            </a>
          </div>
          <div class="votes" id="image-vote-<?php echo $image->attachment_id; ?>">
            <?php echo $image->number_of_votes; ?>
          </div>
          <div class="icon">
            <i class="fa fa-upload"></i>
          </div>
          <div class="date">
            <?php echo $image->created; ?>
          </div>
        </div>

      </div>

    <?php endforeach; ?>

  <?php endif; ?>

</div>
