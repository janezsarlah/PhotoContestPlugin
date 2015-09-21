<h1><strong>Photo Contest</strong></h1>

<input class="button-primary" type="submit" name="Activate" value="<?php esc_attr_e( 'Start contest' ); ?>" />
<?php submit_button(
	'Stop contest',
	$type = 'delete',
	$name = 'submit',
	$wrap = false,
	$other_attributes = NULL
); ?>

<div class="wrap">
	
	<form name="wp_photo_contest_form_1" method="post" action="">

		<div class="contest-subtitle">
		
			<h3>Contest title:</h3>
		
			<input type="text" name="subtitle" placeholder="Naslov" value="<?php echo $contest_subtitle; ?>" />
		
		</div>

		<div class="contest-description">

			<h3>Contest content:</h3>

			<textarea name="description" rows="8" cols="50" placeholder="Content"><?php echo $contest_description; ?></textarea>

		</div>

		<input style="margin-top:15px;" class="button-primary" type="submit" name="contest-data" value="Save" />

	</form>

</div>

<div class="wrap wpphotocontest-list-users" style="margin-top: 40px;">
	<h3>Uploads</h3>
	<table class="widefat">
		<thead>
			<tr>
				<th class="row-title"><?php esc_attr_e( 'Image', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Title', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Description', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Author name', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Email', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'No. of votes', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Action', 'wp_admin_style' ); ?></th>
			</tr>
		</thead>
		<tbody>

			<?php foreach ($uploads as $upload): ?>

				<tr class="alt">

					<td>
						<img src="<?php echo esc_attr_e( $upload->{ 'image_thumb_path' }, 'wp_admin_style' ); ?>" width="80", height="80" />
					</td>

					<td>
						<?php esc_attr_e( $upload->{ 'title' }, 'wp_admin_style' ); ?>
					</td>

					<td>
						<?php esc_attr_e( $upload->{ 'description' }, 'wp_admin_style' ); ?>
					</td>

					<td>
						<?php esc_attr_e( $upload->{ 'author_name' }, 'wp_admin_style' ); ?>
					</td>

					<td>
						<?php esc_attr_e( $upload->{ 'author_email' }, 'wp_admin_style' ); ?>
					</td>

					<td>
						<?php esc_attr_e( $upload->{ 'number_of_votes' }, 'wp_admin_style' ); ?>
					</td>

					<form name="wp_photo_contest_form" method="post" action="">

						<input type="hidden" name="attachment_id" value="<?php echo $upload->{ 'attachment_id' }; ?>">
						<input type="hidden" name="upload_status" value="<?php echo $upload->{ 'image_status' }; ?>">

						<?php if( $upload->{ 'image_status' } ): ?>

							<td>
								<input class="button-primary" type="submit" name="Example" value="<?php esc_attr_e( 'Skrij' ); ?>" />
							</td>

						<?php else: ?>

							<td>
								<input class="button-secondary" type="submit" name="Example" value="<?php esc_attr_e( 'Prikaži' ); ?>" />
							</td>

						<?php endif; ?>

					</form>
				</tr>

			<?php endforeach; ?>

		</tbody>
		<tfoot>
			<tr>
				<th class="row-title"><?php esc_attr_e( 'Image', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Title', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Description', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Author name', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Email', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'No. of votes', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Action', 'wp_admin_style' ); ?></th>
			</tr>
		</tfoot>
	</table>
</div>


<?php echo $attachment_id; ?>

<?php 

echo '<pre>';
var_dump( $uploads );
echo '</pre>';

 ?>