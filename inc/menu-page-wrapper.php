<h1><strong>Photo Contest</strong></h1>

<input class="button-primary" type="submit" name="Activate" value="<?php esc_attr_e( 'Start contest' ); ?>" />
<?php submit_button(
	'Stop contest',
	$type = 'delete',
	$name = 'submit',
	$wrap = false,
	$other_attributes = NULL
); ?>


<div class="wrap wpphotocontest-list-users">
	<h3>Uploads</h3>
	<table class="widefat">
		<thead>
			<tr>
				<th class="row-title"><?php esc_attr_e( 'Image', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Name', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Title', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Description', 'wp_admin_style' ); ?></th>
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

						<input type="hidden" name="upload_approved" value="<?php echo $upload->{ 'attachment_id' }; ?>">

						<?php if( $upload->{ 'image_status' } ): ?>

							<td>
								<input class="button-primary" type="submit" name="Example" value="<?php esc_attr_e( 'Visible' ); ?>" />
							</td>

						<?php else: ?>

							<td>
								<input class="button-primary" type="submit" name="Example" value="<?php esc_attr_e( 'Hidden' ); ?>" />
							</td>

						<?php endif; ?>

					</form>
				</tr>

			<?php endforeach; ?>

		</tbody>
		<tfoot>
			<tr>
				<th class="row-title"><?php esc_attr_e( 'Image', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Name', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Title', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Description', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Email', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'No. of votes', 'wp_admin_style' ); ?></th>
				<th class="row-title"><?php esc_attr_e( 'Action', 'wp_admin_style' ); ?></th>
			</tr>
		</tfoot>
	</table>
</div>


<?php echo $approve_id; ?>

<?php 

echo '<pre>';
var_dump( $uploads );
echo '</pre>';

 ?>