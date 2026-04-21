( function( theme, $ ) {
    // init Youtube video api
	var $youtube_videos = $( '.porto-video-social.video-youtube' );
	if ( $youtube_videos.length ) {
		window.onYouTubeIframeAPIReady = function() {
			$youtube_videos.each( function() {
				var $this = $( this ),
					$wrap = $this.parent( '.video-wrapper' ),
					item_id = $this.attr( 'id' ),
					youtube_id = $this.data( 'video' ),
					is_loop = $this.data( 'loop' ),
					enable_audio = $this.data( 'audio' ),
					autoplay = 1,
					controls = 0;
				if ( '0' === $this.data( 'autoplay' ) ) {
					autoplay = 0;
				}
				if ( $this.data( 'controls' ) ) {
					controls = parseInt( $this.data( 'controls' ) );
				}
				var options = {
					//height: '100%',
					videoId: youtube_id,
					playerVars: {
						'autoplay': autoplay,
						'controls': controls,
						'modestbranding': 1,
						'rel': 0,
						'playsinline': 1,
						'showinfo': 0,
						'loop': is_loop
					},
					events: {
						onReady: function( t ) {
							if ( $wrap.length ) {
								$wrap.themeFitVideo();
							}
							if ( 0 === parseInt( enable_audio ) && t && t.target && t.target.mute ) {
								t.target.mute();
							}
						}
					}
				};
				if ( ! $this.hasClass( 'fit-video' ) && ( ! $wrap.length || ! $wrap.hasClass( 'fit-video' ) ) ) {
					options.width = '100%';
				}
				new YT.Player( item_id, options );
			} );
		};

		if ( $( 'script[src*="www.youtube.com/iframe_api"]' ).length ) {
			setTimeout( onYouTubeIframeAPIReady, 350 );
		} else {
			var tag = document.createElement( 'script' );
			tag.src = "//www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName( 'script' )[0];
			firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
		}
	}

	// init Vimeo video api
	var $vimeo_videos = $( '.porto-video-social.video-vimeo' );
	if ( $vimeo_videos.length ) {
		var portoVimeoInit = function() {
			$vimeo_videos.each( function() {
				var $this = $( this ),
					$wrap = $this.parent( '.fit-video' ),
					item_id = $this.attr( 'id' ),
					video_id = $this.data( 'video' ),
					is_loop = $this.data( 'loop' ),
					enable_audio = $this.data( 'audio' ),
					autoplay = true;
				if ( '0' === $this.data( 'autoplay' ) ) {
					autoplay = false;
				}
				var player = new Vimeo.Player( item_id, {
					id: video_id,
					loop: 1 === parseInt( is_loop ) ? true : false,
					autoplay: autoplay,
					transparent: false,
					background: true,
					muted: 0 === parseInt( enable_audio ) ? true : false,
					events: {
						onReady: function( t ) {
							if ( $wrap.length ) {
								$wrap.themeFitVideo();
							}
							if ( 0 === parseInt( enable_audio ) && t && t.target && t.target.mute ) {
								t.target.mute();
							}
						}
					}
				} );
				if ( 0 === parseInt( enable_audio ) ) {
					player.setVolume( 0 );
				}
				if ( $wrap.length ) {
					player.ready().then( function() {
						$wrap.themeFitVideo();
					} );
				}
			} );
		};

		if ( $( 'script[src="https://player.vimeo.com/api/player.js"]' ).length ) {
			setTimeout( portoVimeoInit, 350 );
		} else {
			var tag = document.createElement( 'script' );
			tag.addEventListener( 'load', function( event ) {
				setTimeout( portoVimeoInit, 50 );
			} );
			tag.src = "https://player.vimeo.com/api/player.js";
			var firstScriptTag = document.getElementsByTagName( 'script' )[0];
			firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
		}
	}
} ).apply( this, [window.theme, jQuery] );