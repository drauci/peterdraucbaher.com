<div class="wrap">
	<h1 class="screen-reader-text"><?php esc_html_e( 'Patcher', 'porto' ); ?></h1>
</div>
<div class="wrap porto-wrap porto-patch-layout">
	<?php
		porto_get_template_part(
			'inc/admin/admin_pages/header',
			null,
			array(
				'active_item' => 'patcher',
				'title'       => esc_html__( 'Patcher', 'porto' ),
				'subtitle'    => esc_html__( 'With this, you can apply fixes to your site between Porto releases and update partially.', 'porto' ),
			)
		);
		?>
	<main style="display: block" id="main">
		<?php
		if ( false === $atts || is_string( $atts)  ) {
			?>
				<div class="porto-important-note note-error"><span><?php esc_html_e( 'The Porto patches server could not be reached.', 'porto' ); ?></span></div>
			<?php
		} 
		if ( is_string( $atts) || is_array( $atts) ) {
			if ( is_array( $atts) ) {
				$show_patches = ! empty( $atts ) && ( ! empty( $atts['update'] ) || ! empty( $atts['delete'] ) );
				?>
				<div class="porto-patcher-wrap">
				<?php
				if ( $show_patches ) {
					?>
					<div class="porto-table-wrap">
					<table class="porto-table" id="patcher-table">
						<thead>
							<tr>
								<th><h4><?php esc_html_e( 'Patches Path', 'porto' ); ?></h4></th>
								<th><h4><?php esc_html_e( 'Patch Action', 'porto' ); ?></h4></th>
							</tr>
						</thead>
						<tbody id="patcher-tbody">
						<?php
						foreach ( $atts as $action => $patches ) {
							if ( 'update' == $action && ! empty( $patches ) ) {
								foreach ( $patches as $path => $value ) {
									?>
										<tr class="updated" data-path="update-<?php echo esc_attr( $path ); ?>">
										<td><p><?php echo esc_html( $path ); ?></p></td>
										<td><p class="update-notice"><?php esc_html_e( 'Should update', 'porto' ); ?></p></td>
										</tr>
									<?php
								}
							} elseif ( 'delete' == $action && ! empty( $patches ) ) {
								foreach ( $patches as $path => $target ) {
									?>
										<tr class="delete" data-path="delete-<?php echo esc_attr( $path ); ?>">
										<td><p><?php echo esc_html( $path ); ?></p></td>
										<td><p class="delete-notice"><?php esc_html_e( 'Should delete', 'porto' ); ?></p></td>
										</tr>
									<?php
								}
							}
						}
						?>
						</tbody>
					</table>
					</div>
					<?php
				} elseif ( isset( $atts['theme_version'] ) && isset( $atts['func_version'] ) ) {
					?>
					<div class="porto-important-note"><span><?php printf( esc_html__( 'Your Theme version is %1$s and Functionality version is %2$s. Currently there are no patches available.', 'porto' ), esc_html( $atts['theme_version'] ), esc_html( $atts['func_version'] ) ); ?></span></div>
				<?php } ?>
					<div class="porto-patcher-changelog-wrapper">
						<h4><?php esc_html_e( 'Changelog', 'porto' ); ?></h4>
						<?php
						if ( ! empty( $atts['changelog'] ) ) {
							$legacy_patches = $this->get_applied_patches();
							if ( empty( $legacy_patches['changelog'] ) ) {
								$patched_dates = array();    
							} else {
								$patched_dates = array_keys( $legacy_patches['changelog'] );
							}
							foreach( $atts['changelog'] as $date => $logs ) {
							?>
							<div class="porto-patcher-log">
								<?php if ( false !== array_search( $date, $patched_dates ) ) { ?>
									<svg width="20px" version="1.1" viewBox="0 0 32 32" xml:space="preserve">
										<path style="fill:#009900;" d="M16,0C7.164,0,0,7.164,0,16s7.164,16,16,16s16-7.164,16-16S24.836,0,16,0z M13.52,23.383
													L6.158,16.02l2.828-2.828l4.533,4.535l9.617-9.617l2.828,2.828L13.52,23.383z"/>
									</svg>
								<?php } else { ?>
									<svg width="20px" viewBox="0 0 62 62" enable-background="new 0 0 62 62">
										<circle cx="32" cy="32" r="30" fill="#fff"/>
										<path d="M32,2C15.432,2,2,15.432,2,32s13.432,30,30,30s30-13.432,30-30S48.568,2,32,2z M32,49L16,33.695h10.857V15h10.285v18.695H48L32,49z" fill="#0088cc"/>
									</svg>
								<?php } ?>
								<label class="porto-patcher-version-date"><?php echo esc_html( $date ); ?></label>
								<ul>
									<?php foreach( $logs as $log ) { ?>
									<li><?php echo esc_html( $log ); ?></li>
									<?php } ?>
								</ul>
							</div>
							<?php 
							}
						} 
						?>
					</div>
				</div>
				<?php
			}
			?>
		<div class="action-footer">
			<a href="<?php echo admin_url( 'admin.php?page=porto-patcher&action=refresh' ); ?>" class="btn btn-primary" id="patch-refresh"><?php esc_html_e( 'Refresh Patches', 'porto' ); ?></a>
			<?php if ( is_array( $atts) && $show_patches ) : ?>
				<a href="#" class="btn btn-outline" id="patch-apply"><?php esc_html_e( 'Apply Patches', 'porto' ); ?></a>
			<?php endif; ?>
		</div>
		<?php } ?>
	</main>
</div>
