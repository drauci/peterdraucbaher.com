/**
 * The options cache of quick access widget in wpb frontend editor.
 * 
 * @since 2.6.0
 */
var portoQuickAccessCache = {};

jQuery( document ).ready( function( $ ) {
	'use strict';

	$( 'body' ).on( 'tabsbeforeactivate', '.wpb_tour_tabs_wrapper', function( e, ui ) {
		ui.oldTab.removeClass( 'active' );
		ui.newTab.addClass( 'active' );
	} );

	$( '.compose-mode .vc_controls-bc .vc_control-btn-append' ).each( function() {
		$( this ).insertAfter( $( this ).closest( '.vc_controls' ).find( '.vc_control-btn-prepend' ) );
	} );

	if ( window.parent.vc && window.parent.vc.events ) {
		/**
		 * Make quick access item.
		 * 
		 * @since 2.6.0
		 * @param {*} $qa_node 
		 * @param {*} qa_item 
		 * @param {*} widgetRect 
		 * @param {*} nodeRect 
		 */
		function make_qa_item( self, $qa_node, qa_item, widgetRect, nodeRect, nonPos = false ) {
			var elementDialog = self.$el, _temp = $( '<button aria-label="' + js_porto_vars.quick_access + '" title="' + js_porto_vars.quick_access + '" class="' + ( nonPos ? 'non-pos ' : '' ) + 'porto-qa-item position-absolute"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg><div class="region"></div></button>' );
			$qa_node.append( _temp.click( function( e ) {

				elementDialog.find( 'li.vc_edit-form-tab-control button' ).filter( function() {
					return $( this ).text() == qa_item.tab;
				} ).trigger( 'click' );
				if ( qa_item.class ) {
					var $panelSide = elementDialog.find( '.vc_ui-panel-content-container' );
					var $particularOption = elementDialog[0].querySelector( qa_item.class + ':not(.vc_dependent-hidden)' );
					if ( $particularOption ) {
						$panelSide.animate( { scrollTop: $particularOption.offsetTop } );
						$particularOption.classList.add( 'show-qa-option' );
						setTimeout( function() {
							if ( $particularOption ) {
								$particularOption.classList.remove( 'show-qa-option' );
							}
						}, 3000 );
					}
				}
			} ).css( { top: ( nodeRect.top - widgetRect.top - 15 ), left: ( nodeRect.left - widgetRect.left - 15 ) } ) );
			_temp.find( '.region' ).css( { width: nodeRect.width, height: nodeRect.height } );
		}

		/**
		 * Quick Access in WPB Frontned Editor.
		 * 
		 * @since 2.6.0
		 */
		( function quick_access( self ) {
			if ( !( typeof self == 'object' ) ) {
				return;
			}
			$( document.body ).on( 'mouseenter', '.vc_element', function() {
				if ( !( self.model && self.model.id ) ) {
					return;
				}
				var $this = $( this );
				if ( $this.attr( 'data-model-id' ) != self.model.id ) {
					return;
				}
				var widgetControls = self.model.settings.params;
				var widgetKey = self.model.settings.base;
				if ( !portoQuickAccessCache[widgetKey] && typeof widgetControls == 'object' ) {
					widgetControls.forEach( function( widgetControl ) {
						if ( widgetControl.qa_selector ) {
							if ( !portoQuickAccessCache[widgetKey] ) {
								portoQuickAccessCache[widgetKey] = [];
							}
							var controlInfo = {
								selector: widgetControl.qa_selector,
								tab: widgetControl.group ? widgetControl.group : wp.i18n.__( 'General', 'js_composer' ),
								class: '.vc_shortcode-param[data-vc-shortcode-param-name="' + widgetControl.param_name + '"]'
							};
							portoQuickAccessCache[widgetKey].push( controlInfo );
						}
					} );
				}
				if ( portoQuickAccessCache[widgetKey] ) {
					$this.append( '<div class="porto-quick-access"></div>' );
					var $qa_node = $this.find( '.porto-quick-access' ), widgetRect = this.getBoundingClientRect();
					var nonPosElements = [];
					portoQuickAccessCache[widgetKey].forEach( function( qa_item ) {
						var $el = $this.find( qa_item.selector );
						if ( $el.length ) {
							$el.each( function() {
								if ( $( this ).closest( 'ul.sub-menu' ).length ) {
									if ( $( this ).closest( 'ul.sub-menu' ).css( 'visibility' ) == 'hidden'  ) {
										return;
									}
								}
								var nodeRect = this.getBoundingClientRect(), hasNon = false;
								if ( ( nodeRect.width == 0 && nodeRect.height == 0 ) ) { // display: none;
									nonPosElements.push( [this, qa_item] );
									return;
								}
								if ( $( this ).closest( 'li.has-sub>.popup' ).length ) {
									nonPosElements.push( [this, qa_item] );
									hasNon = true;
								}
								// li.has-sub>.popup => display: block 
								make_qa_item( self, $qa_node, qa_item, widgetRect, nodeRect, hasNon );
							} );
						}
					} );
					if ( widgetKey == 'porto_sidebar_menu' || widgetKey == 'porto_hb_menu' ) {
						if ( nonPosElements.length ) {
							$this.on( 'mouseenter', '>ul>li.has-sub, .sidebar-menu>li.has-sub, .sidebar-menu .narrow .inner>.sub-menu>li.menu-item-has-children', function( e ) {
								widgetRect = $this.get( 0 ).getBoundingClientRect();
								var $related = $( e.relatedTarget );
								var $focusEl = $( this );
								if ( $related.closest( '.porto-qa-item.non-pos' ).length ) {
									if ( $focusEl.hasClass( 'menu-item-has-children sub' ) ) {
										$focusEl.find( '>.sub-menu' ).css( 'display', '' );
									}
									return;
								}
								if ( $focusEl.hasClass( 'menu-item-has-children sub' ) && $focusEl.find( '>.sub-menu' ).css( 'display' ) == 'block' && !$focusEl.is( ':hover' ) ) {
									return true;
								}
								nonPosElements.forEach( function( nonPosElement ) {
									if ( $focusEl.hasClass( 'menu-item-has-children sub' ) ) {
										if ( !$.contains( $focusEl.get( 0 ), nonPosElement[0] ) ) {
											return;
										}
										if ( !( nonPosElement[0].classList && nonPosElement[0].classList.contains( 'sub-menu' ) ) ) {
											return;
										}
									}
									var nodeRect = nonPosElement[0].getBoundingClientRect();
									if ( nodeRect.width == 0 && nodeRect.height == 0 ) { // display: none;
										return;
									}
									make_qa_item( self, $qa_node, nonPosElement[1], widgetRect, nodeRect, true );
								} );
							} ).on( 'mouseleave', '>ul>li.has-sub, .sidebar-menu>li.has-sub', function( e ) {
								var $related = $( e.relatedTarget );
								if ( $related.closest( '.porto-qa-item.non-pos' ).length ) {
									$( this ).addClass( 'open' ).find( '.popup' ).css( 'display', 'block' );
									$( this ).find( '.popup>.inner>.sub-menu>li>.sub-menu' ).css( 'display', 'block' );
									return;
								}
								$( this ).removeClass( 'open' );
								$( this ).find( '.popup>.inner>.sub-menu>li>.sub-menu' ).css( 'display', '' );
								var $nonPosNode = $this.find( '.porto-qa-item.non-pos' );
								if ( $nonPosNode.length ) {
									$nonPosNode.remove();
								}
							} );
						}
					}
				}
			} ).on( 'mouseleave', '.vc_element', function() {
				if ( !( self.model && self.model.id ) ) {
					return;
				}
				if ( $( this ).attr( 'data-model-id' ) != self.model.id ) {
					return;
				}
				var $quickAccess = $( this ).find( '.porto-quick-access' );
				if ( $quickAccess.length ) {
					$quickAccess.remove();
				}
				$( this ).off( 'mouseenter mouseleave' );
			} );
		} )( window.parent.vc.edit_element_block_view );

		/**
		 * Go to Type Builder in Posts Grid Widget.
		 * 
		 * @since 2.6.0
		 */
		( function goToBuilder() {
			$( document.body ).on( 'mouseenter', '[data-tb-id] .porto-tb-item', function() {
				var $this = $( this ), label = '';
				if ( $this.find( '[class*=porto-tb]' ).length == 0 ) {
					return;
				}
				if ( js_porto_vars && js_porto_vars.goto_type ) {
					label = js_porto_vars.goto_type;
				} else {
					label = 'Go To Type Builder';
				}
				$this.append( '<div class="overlay-slidetop"><a href="#" title="' + label + '" aria-label="' + label + '"><i class="fas fa-arrow-right"></i></a></div>' );
			} ).on( 'mouseleave', '[data-tb-id] .porto-tb-item', function() {
				$( this ).find( '.overlay-slidetop' ).remove();
			} );
			$( document.body ).on( 'click', '.porto-tb-item .overlay-slidetop a', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				theme.$selectEl = $( this ).closest( '.vc_element' );
				var _body = parent.document.body;
				var _jQuery = parent.jQuery;
				var src = js_porto_vars.ajax_url.slice( 0, -14 ) + 'post.php?post=' + $( this ).closest( '[data-tb-id]' ).attr( 'data-tb-id' ) + '&action=edit';
				var _modal = _jQuery( _body ).find( '.porto-tb-modal' );
				if ( !_modal.length ) {
					_jQuery( _body ).append( '<div class="porto-tb-modal"><iframe id="porto-tb-iframe" src="' + src + '"></iframe></div>' );
					_modal = _jQuery( _body ).find( '.porto-tb-modal' );
					_modal.find( '#porto-tb-iframe' ).on( 'load', function() {
						$( this.contentWindow.document.body ).addClass( 'porto-tb-preview' );
						_modal.show();
					} );
					_modal.click( function( e ) {
						// if ( e.which == 13 ) {
						$( this ).hide();
						// }
						if ( parent.vc.builder && typeof theme.$selectEl == 'object' && theme.$selectEl.length ) {
							parent.vc.builder.update( parent.vc.shortcodes._byId[theme.$selectEl.attr( 'data-model-id' )] );
							theme.$selectEl = '';
						}
					} );
				} else {
					if ( src != _modal.find( 'iframe' ).attr( 'src' ) ) {
						_modal.find( 'iframe' ).attr( 'src', src );
					} else {
						_modal.show();
					}
				}
			} );
		} )();

		/**
		 * Toolbar
		 * 
		 * @since 2.6.0
		 */
		( function() {

			var isCapture = 0;
			var _$ = parent.jQuery;
			var $toolbar = _$( '.porto-toolbar' );
			var vcBarHeight = _$( '#vc_navbar' ).height();
			if ( $toolbar.length == 0 ) {
				return;
			}
			_$( 'body' ).on( 'mousemove', function( e ) {
				if ( isCapture == 0 ) {
					return;
				}
				if ( e.buttons == 1 ) { // primary mouse button was pressed.
					isCapture = 2;
					$toolbar.css( { top: e.pageY, left: e.pageX } );
				}
			} ).on( 'mouseup', function( e ) {
				if ( isCapture != 2 ) {
					if ( e.target.classList && e.target.classList.contains( 'porto-toolbar-toggle' ) ) {
						$toolbar.toggleClass( 'switched' );
					}
				}
				isCapture = 0;
			} ).on( 'mousedown', function( e ) {
				if ( e.target.classList && e.target.classList.contains( 'porto-toolbar-toggle' ) ) {
					isCapture = 1;
				}
			} );

			$( 'body' ).on( 'mousemove', function( e ) {
				if ( isCapture == 0 ) {
					return;
				}
				if ( e.buttons == 1 ) {
					isCapture = 2;
					$toolbar.css( { top: e.clientY + vcBarHeight, left: e.screenX } );
				}
				if ( e.buttons == 0 && isCapture == 2 ) { // bubbling
					isCapture = 0;
					$toolbar.css( { top: e.clientY + vcBarHeight, left: e.screenX } );
				}
			} ).on( 'mouseup', function( e ) {
				if ( isCapture != 2 ) {
					if ( e.target.classList && e.target.classList.contains( 'porto-toolbar-toggle' ) ) {
						$toolbar.toggleClass( 'switched' );
					}
				}
				isCapture = 0;
			} );

			_$( '.go-to-page-css' ).on( 'click', function() {
				_$( '#vc_post-settings-button' ).trigger( 'click' );
			} );
			_$( '.go-to-builder-setting' ).on( 'click', function() {
				_$( '#porto-editor-area-button' ).trigger( 'click' );
			} );
		} )();

		window.parent.vc.events.on( 'shortcodes:add', function( model ) {
			var parent_id = model.attributes.parent_id;
			if ( !parent_id ) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get( parent_id );
			if ( parent && 'porto_carousel' == parent.attributes.shortcode ) {
				var $obj = $( '[data-model-id="' + parent.attributes.id + '"]' ).children( '.owl-carousel' );
				if ( $obj.length ) {
					$obj.removeData( '__carousel' );
					$obj.trigger( 'destroy.owl.carousel' );
				}
			}
		} );

		window.parent.vc.events.on( 'shortcodeView:ready', function( e ) {
			var shortcode = e.attributes.shortcode;
			if ( 'porto_scroll_progress' == shortcode ) {
				if ( $( 'script#porto-scroll-progress-js' ).length ) {
					$( document.body ).trigger( 'porto_init_scroll_progress', [e.view.$el] );
				} else {
					$( document.createElement( 'script' ) ).attr( 'id', 'porto-scroll-progress-js' ).appendTo( 'body' ).attr( 'src', js_porto_vars.ajax_loader_url.replace( '/images/ajax-loader@2x.gif', '/js/libs/porto-scroll-progress.min.js' ) ).on( 'load', function() {
						$( document.body ).trigger( 'porto_init_scroll_progress', [e.view.$el] );
					} );
				}
			} else if ( 'vc_row' == shortcode && e.attributes.params ) {
				if ( e.attributes.params.particles_effect && e.attributes.params.particles_img ) {
					e.view.$el.find( '.particles-wrapper:not(:first-child)' ).remove();

					if ( typeof particlesJS == 'undefined' ) {
						$( document.createElement( 'script' ) ).attr( 'id', 'particles-js' ).appendTo( 'body' ).attr( 'src', porto_wpb_vars.shortcodes_url + 'assets/js/particles.min.js' ).on( 'load', function() {
							$( document.createElement( 'script' ) ).attr( 'id', 'porto-particles-loader-js' ).appendTo( 'body' ).attr( 'src', porto_wpb_vars.shortcodes_url + 'assets/js/porto-particles-loader.min.js' ).on( 'load', function() {
								$( document.body ).trigger( 'porto_init_particles_effect', [e.view.$el] );
							} );
						} );
					} else {
						$( document.body ).trigger( 'porto_init_particles_effect', [e.view.$el] );
					}
				} else {
					e.view.$el.find( '.particles-wrapper' ).remove();
				}

				if ( e.attributes.params.scroll_parallax && $.fn.themeScrollParallax ) {
					var parallax_width = e.attributes.params.scroll_parallax_width ? e.attributes.params.scroll_parallax_width : 40;
					var opts = { cssValueStart: parseInt( parallax_width, 10 ) };
					opts['cssValueUnit'] = '%';
					$( e.view.$el ).find( '[data-plugin="scroll-parallax"]' ).themeScrollParallax( opts );
				}

				var $viewportWrapper = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"] >[data-inviewport-style]' );
				if ( $.fn.themePluginInViewportStyle && $viewportWrapper.length ) {
					if ( e.attributes.params.scroll_inviewport == 'true' && 'true' != e.attributes.params.scroll_bg_scale ) {
						$viewportWrapper.find( '.scale-expand' ).remove();
					}
				}

				var $splitWrapper = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"] >.mouse-hover-split' );
				if ( 'yes' == e.attributes.params.hover_split ) {
					if ( $splitWrapper.length && typeof $splitWrapper.data( '__mousehoversplit' ) == 'undefined' ) {
						$splitWrapper.themePluginHoverSplit();
					}
				} else {
					if ( typeof $splitWrapper.data( '__mousehoversplit' ) != 'undefined' ) {
						$splitWrapper.data( '__mousehoversplit' ).clearData();
					}
				}

				// refresh cursor spotlight
				var $row = $( '[data-model-id="' + e.attributes.id ),
					$cursorShape = $row.find( '[data-cursor-shape]' );
				if ( $row.length && $cursorShape.length ) {
					$row.find( '.cursor-shape' ).remove();
					$cursorShape.each( function() {
						var $this = $( this ),
							ins = $this.data( '__cursorEffect' );
						if ( ins ) {
							ins.destroy();
							$this.removeData( '__cursorEffect' );
							$this.themePluginCursorSpotlight();
						}
					} );
				}


				// Video Background
				if ( $.fn.themePluginVideoBackground ) {
					if ( $row.find( '>.vc_row>.video-overlay' ).length ) {
						$row.find( '>.vc_row>.video-overlay' ).prev().remove();
						$row.find( '>.vc_row>.video-overlay' ).remove();
						$row.find( '>.vc_row' ).removeData( '__videobackground' );
					}
					$row.find( '[data-plugin-video-background]:not(.manual)' ).each( function() {
						var $this = $( this ),
							opts;
		
						var pluginOptions = theme.getOptions( $this.data( 'plugin-options' ) );
						if ( pluginOptions )
							opts = pluginOptions;
		
						if ( $this.find( '.video-overlay' ).length ) {
							$this.find( '.video-overlay' ).prev().remove();
							$this.find( '.video-overlay' ).remove();
							$this.removeData( '__videobackground' );
						}
						$this.themePluginVideoBackground( opts );
					} );
				}

			} else if ( 'porto_cursor_effect' == shortcode && e.attributes.params && e.id ) {
				var $cursorWidget = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"]' ),
					$cursorShape = $cursorWidget.find( '>[data-cursor-shape]' );
				if ( 'true' == e.attributes.params.cursor_shape ) {
					var cursorsID = $cursorShape.data( 'plugin-options' ).id;
					if ( cursorsID ) {
						$( '.' + cursorsID ).remove();
					}
					if ( $.fn.themePluginCursorSpotlight ) {
						if ( 'undefined' != typeof gsap ) {
							$cursorShape.themePluginCursorSpotlight();
						} else {
							if ( !$( '#porto-gsap-js' ).length ) {
								var tag = document.createElement( 'script' );
								tag.id = 'porto-gsap-js';
								tag.src = "https://www.portotheme.com/wordpress/porto/wp-content/uploads/assets/vendor/gsap.min.js";
								var firstScriptTag = document.getElementsByTagName( 'script' )[0];
								firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
							}

							$( '#porto-gsap-js' ).on( 'load', function() {
								$cursorShape.themePluginCursorSpotlight();
							} );
						}
					}
					var ins = $( document.body ).data( '__cursorEffect' );
					if ( ins ) {
						ins.destroy();
						$( document.body ).removeData( '__cursorEffect' );
						if ( typeof window.porto_cursor_effects != 'undefined' ) {
							window.porto_cursor_effects.forEach( function( i, index ) {
								if ( i.model_id && e.id == i.model_id ) {
									window.porto_cursor_effects.splice( index, 1 );
									return false;
								}
							} );
						}
					}
				} else {
					var $cursorWrapper = $cursorWidget.closest( '.vc_row' );
					if ( $cursorWrapper.length ) {
						$cursorWrapper.find( '>.cursor-shape' ).remove();
					}
					var $shortcode_cls_obj = e.view.$el.find( '.shortcode-class' );
					if ( typeof window.porto_cursor_effects == 'undefined' ) {
						window.porto_cursor_effects = [];
					}
					window.porto_cursor_effects.forEach( function( i, index ) {
						if ( i.model_id && e.id == i.model_id ) {
							window.porto_cursor_effects.splice( index, 1 );
							return false;
						}
					} );

					var inner_icon = e.attributes.params.inner_icon;
					if ( 'simpleline' == e.attributes.params.icon_type ) {
						inner_icon = e.attributes.params.icon_simpleline;
					} else if ( 'porto' == e.attributes.params.icon_type ) {
						inner_icon = e.attributes.params.icon_porto;
					}
					window.porto_cursor_effects.push( { model_id: e.id, id: $shortcode_cls_obj.length ? $shortcode_cls_obj.text() : '', selector: e.attributes.params.selector ? e.attributes.params.selector.replace( '&gt;', '>' ) : '', hover_effect: e.attributes.params.hover_effect || 'plus', icon: inner_icon, cursor_w: e.attributes.params.cursor_w || '' } );
					$shortcode_cls_obj.remove();

					var ins = $( document.body ).data( '__cursorEffect' );
					if ( ins ) {
						ins.destroy();
						$( document.body ).removeData( '__cursorEffect' );

						if ( window.porto_cursor_effects.length && $.fn.themePluginCursorEffect ) {
							$( document.body ).themePluginCursorEffect();
						}
					}
				}
			} else if ( 'vc_pie' == shortcode && e.attributes.params && e.attributes.params.type && 'custom' == e.attributes.params.type ) {
				porto_init( e.view.$el );
			} else if ( 'porto_countdown' == shortcode && e.attributes.params ) {
				var $obj = e.view.$el;
				var $countdown_div = $obj.find( '.porto_countdown-div' );
				if ( $countdown_div.length ) {
					let cdate = new Date(), sdate = cdate.getTime() + parseFloat( $countdown_div.data( 'time-zone' ) ) * 3600 * 1000;
					sdate = new Date( sdate ).toISOString().replace( /(.*)(20[0-9]{2}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})(.*)/, '$2 $3' );
					$countdown_div.data( 'time-now', sdate.replace( /-/g, '/' ) );
				}
				$( document.body ).trigger( 'porto_init_countdown', [$obj] );
			} else if ( 'vc_single_image' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				// Visual Composer Image Zoom
				if ( $.fn.themeVcImageZoom ) {
					var $galleryParent = null;
					$obj.find( '.porto-vc-zoom:not(.manual)' ).each( function() {
						var $this = $( this ),
							opts,
							gallery = $this.attr( 'data-gallery' );

						if ( $this.data( '__zoom' ) ) {
							return;
						}

						var pluginOptions = $this.data( 'plugin-options' );
						if ( pluginOptions )
							opts = pluginOptions;

						if ( typeof opts == "undefined" ) {
							opts = {};
						}
						opts.container = $this.parent();

						if ( gallery == 'true' ) {
							var container = 'vc_row';

							if ( $this.attr( 'data-container' ) )
								container = $this.attr( 'data-container' );

							var $parent = $( $this.closest( '.' + container ).get( 0 ) );
							if ( $parent.length > 0 && $galleryParent != null && $galleryParent.is( $parent ) ) {
								return;
							} else if ( $parent.length > 0 ) {
								$galleryParent = $parent;
							}
							if ( $galleryParent != null && $galleryParent.length > 0 ) {
								opts.container = $galleryParent;
							}
						}

						$this.themeVcImageZoom( opts );
					} );
				}
			} else if ( 'porto_image_comparison' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				if ( $.fn.portoImageCompare && $obj.find( '.porto-image-comparison' ).length ) {
					$obj.find( '.porto-image-comparison' ).portoImageCompare();
				}
			} else if ( 'porto_blog' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				porto_init( $obj );
			} else if ( 'porto_content_box' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				var $icon = $obj.find( '.box-content>.icon-featured:not(:first-child)' );
				if ( $icon.length ) {
					$icon.remove();
				}
			} else if ( 'porto_sidebar_menu' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				if ( typeof theme.SidebarMenu != 'undefined' ) {
					theme.SidebarMenu.initialize( $obj.find( '.sidebar-menu:not(.side-menu-accordion)' ) );
					$( '.sidebar-menu.side-menu-accordion' ).themeAccordionMenu( { 'open_one': true } );
				}
			} else if ( ( 'porto_hb_menu' == shortcode || 'porto_hb_toggle' == shortcode ) && e.attributes.params ) {
				// var $obj = $( e.view.$el );
				// menu
				if ( typeof theme.MegaMenu !== 'undefined' ) {
					theme.MegaMenu.defaults.menu = $( '.mega-menu' );
					theme.MegaMenu.initialize();
					if ( typeof theme.SidebarMenu != 'undefined' ) {
						theme.SidebarMenu.defaults.menu = $( '.sidebar-menu:not(.side-menu-accordion)' );
						// theme.SidebarMenu.defaults.toggle = $( '.widget_sidebar_menu .widget-title .toggle' );
						theme.SidebarMenu.defaults.menu_toggle = $( '#main-toggle-menu .menu-title' );
						theme.SidebarMenu.initialize();
					}
				}
			} else if ( 'vc_column' == shortcode && e.attributes.params ) {
				if ( $.fn.themePluginHoverSplit ) {
					if ( 'yes' == e.attributes.params.split_layer ) {
						var $splitSlide = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"]' ),
							$splitWrapper = $splitSlide.closest( '.mouse-hover-split' );
						$splitSlide.addClass( 'split-slide' );
						var ins = $splitWrapper.data( '__mousehoversplit' );
						if ( ins ) {
							ins.clearData();
						}
						$splitWrapper.themePluginHoverSplit();
					}
				}
			} else if ( ( 'vc_custom_heading' == shortcode || 'porto_ultimate_heading' == shortcode ) && e.attributes.params ) {
				var $floatingImage = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"]' );
				if ( $floatingImage.length ) {
					var $floatingWrapper = $floatingImage.find( '.thumb-info-floating-element-wrapper[data-plugin-tfloating]' );
					// Text Hover Floating Image
					if ( $.fn.themePluginTIFloating && $floatingWrapper.length ) {
						var ins = $floatingWrapper.data( '__textelfloating' );
						if ( ins ) {
							ins.clearData();
						}
						$floatingWrapper.themePluginTIFloating();
					}
				}
				if ( $floatingImage.find( '[data-clone-element]' ).length ) {
					$floatingImage.find( '[data-clone-element]' ).each( function() {
						var $el = $(this)
						  , content = $el.html()
						  , qty = $el.attr( 'data-clone-element' );
						for ( let i = 0; i < qty; i++ ) {
							$el.html( $el.html() + content );
						}
					});
				}
				if ( $floatingImage.find( '.marquee .js-marquee' ).length == 0  && $floatingImage.find( '.marquee' ).length && $.isFunction( $.fn.marquee ) ) {
					$floatingImage.find( '.marquee' ).marquee( {
						duration: 5000,
						gap: 0,
						delayBeforeStart: 0,
						direction: 'left',
						duplicated: true
					} );
				}
			} else if ( ( 'porto_animation' == shortcode && e.attributes.params ) ) {
				if ( $.fn.themeMouseparallax ) {
					var $mouseParallax = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"] [data-plugin="mouse-parallax"]' );
					if ( $mouseParallax.length ) {
						$mouseParallax.each( function() {
							var $this = $( this ),
								opts;
							if ( $this.data( 'parallax' ) ) {
								return;
							}
							$this.children( '.layer' ).attr( 'data-depth', $this.attr( 'data-floating-depth' ) );
							var pluginOptions = $this.data( 'options' );
							if ( pluginOptions )
								opts = pluginOptions;
			
							$this.themeMouseparallax( opts );
						} );
					}
				}
			} else if ( ( 'porto_hscroller' == shortcode && e.attributes.params ) ) {
				if ( $.fn.themePluginHScroller ) {
					var $hScroller = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"] .horizontal-scroller-wrapper' ),
						ins = $hScroller.data( '__horizontalscroller' );
					if ( ins ) {
						ins.clearData();
					}
					// Horizontal Scroller
					$hScroller.themePluginHScroller();
				}
			} else if ( 'porto_ab_posts_grid' == shortcode || 'porto_tb_posts' == shortcode || 'porto_tb_archives' == shortcode ) {
				var $postWrap = $( e.view.$el ).find( '.porto-posts-grid' );
				if ( theme.InsertHoverImage && $postWrap.length ) {
					var $hoverImage = $postWrap.find( '.thumb-info-full' );
					if ( $hoverImage.length ) {
						$hoverImage.remove();
					}
					$postWrap.find( '[data-hoverlay-image]' ).each( function() {
						theme.InsertHoverImage( $( this ) );
					} );
				}
			} else if ( 'porto_single_product_image' == shortcode && e.attributes.params ) {
				theme.WooProductImageSlider.initialize();
			}
			if ( e.view.$el.children().hasClass( 'w-100' ) ) {
				e.view.$el.addClass( 'w-100' );
			}
			if ( 'porto_hb_logo' == shortcode ) {
				if ( e.view.$el.children().hasClass( 'text-center' ) ) {
					e.view.$el.addClass( 'text-center' );
				}
				if ( e.view.$el.children().hasClass( 'mx-auto' ) ) {
					e.view.$el.addClass( 'mx-auto' );
				}
			}
		} );

		window.parent.vc.events.on( 'shortcodeView:destroy', function( model ) {
			var parent_id = model.attributes.parent_id;
			if ( !parent_id ) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get( parent_id );
			if ( parent ) {
				if ( 'porto_carousel' == parent.attributes.shortcode ) {
					var $obj = $( '[data-model-id="' + parent.attributes.id + '"]' ).children( '.owl-carousel' );
					if ( $obj.length ) {
						$obj.removeData( '__carousel' );
						$obj.trigger( 'destroy.owl.carousel' );
						$obj.children( '.owl-item:empty' ).remove();
						$obj.themeCarousel( $obj.data( 'plugin-options' ) );
					}
				}
			}

			if ( 'porto_cursor_effect' == model.attributes.shortcode ) {
				if ( model.attributes.params.cursor_shape && 'true' == model.attributes.params.cursor_shape ) {
					var $parent = $( '[data-model-id="' + parent.attributes.id + '"]' ).closest( '.vc_row' );
					if ( $parent.length ) {
						$parent.find( '>.cursor-shape' ).remove();
					}
				} else if ( window.porto_cursor_effects && window.porto_cursor_effects.length ) {
					window.porto_cursor_effects.forEach( function( i, index ) {
						if ( i.model_id && model.id == i.model_id ) {
							window.porto_cursor_effects.splice( index, 1 );

							var ins = $( document.body ).data( '__cursorEffect' );
							if ( ins ) {
								ins.destroy();
								$( document.body ).removeData( '__cursorEffect' );

								if ( window.porto_cursor_effects.length && $.fn.themePluginCursorEffect ) {
									$( document.body ).themePluginCursorEffect();
								}
							}
							return false;
						}
					} );
				}
			}
		} );

		// After WPB option saved
		window.parent.vc.events.on( 'shortcodeView:updated', function( model ) {
			var $obj = $( '[data-model-id="' + model.id + '"]' );
			if ( 'porto_hb_switcher' == model.attributes.shortcode ) {
				$obj.find( '.porto-view-switcher' ).addClass( 'show' );
			} else if ( 'porto_hb_menu' == model.attributes.shortcode || 'porto_hb_toggle' == model.attributes.shortcode ) {
				var $activeTab = $( window.parent.document.body ).find( '.vc_ui-panel-window.vc_active .vc_edit-form-tab-control.vc_active' );
				if ( $activeTab.length > 0 ) {
					portoWpbShowMenuPopup( $obj, $activeTab.data( 'tab-index' ) );
				}
			} else if ( 'porto_hb_search_form' == model.attributes.shortcode ) {
				var $searchForm = $obj.find( '.searchform-popup' );
				if ( ( $searchForm.hasClass( 'search-popup' ) || ( $searchForm.hasClass( 'advanced-search-layout' ) && $( window ).width() < 992 ) ) && 2 == $( window.parent.document.body ).find( '.vc_ui-panel-window.vc_active .vc_edit-form-tab-control.vc_active' ).data( 'tab-index' ) ) {
					$searchForm.find( '>.search-toggle' ).addClass( 'opened' );
					$searchForm.addClass( 'show' );
					if ( $searchForm.find( '.search-layout-reveal' ).length > 0 ) {
						portoToggleReveal( $searchForm, true );
					}
				}
			}
		} );

		// Switch Design tab
		$( window.parent.document.body ).on( 'click', '.porto-layout-onion-tabs > span', function (e) {
			var $this = $(this);
			if ( ! $this.hasClass( 'active' ) ) {
				$this.siblings( '.active' ).removeClass( 'active' );
				$this.addClass( 'active' );
				var $tabTitle = $this.closest( '.porto-layout-onion-tabs' ),
					$tabContent = $tabTitle.siblings( '.porto-layout-tab-content' ),
					$vcCheckbox = $tabTitle.siblings( '.vc_settings' ).find( '.vc_checkbox' ),
					$activeContent = $tabContent.find( '.vc_layout-onion.active' ),
					$currentContent = $tabContent.find( '.vc_layout-onion[data-width="' + $this.data('width') + '"]' );
				
				$activeContent.fadeOut( 50, function () {
					$(this).removeClass('active');
					$currentContent.fadeIn( 50, function () {
						$(this).addClass('active');
					});
				});
				$vcCheckbox.show();
				if ( 'desktop' !== $this.data( 'width' ) ) {
					$vcCheckbox.hide();
				}
			}
		})

		/**
		 * Render again after replacing breakpoints
		 * 
		 * @since 7.2.0
		 */
		window.parent.vc.edit_element_block_view.on( 'save', function() {
			if ( 'object' == typeof window.parent.vc.atts.css_editor.css_params ) {
				this.model.attributes.params.css_params = Object.keys(window.parent.vc.atts.css_editor.css_params).length ? json_encode( window.parent.vc.atts.css_editor.css_params ) : '';
				if ( window.parent.vc.atts.css_editor.custom_css_response ) {
					this.model.attributes.params.custom_css_response = window.parent.vc.atts.css_editor.custom_css_response;

					window.parent.vc.atts.css_editor.custom_css_response = window.parent.vc.atts.css_editor.custom_css_response.replaceAll( '@media(max-width:porto-xxl', '@media(max-width:' + ( js_porto_vars.screen_xxl - 1 ) );
					window.parent.vc.atts.css_editor.custom_css_response = window.parent.vc.atts.css_editor.custom_css_response.replaceAll( '@media(max-width:porto-xl', '@media(max-width:' + ( js_porto_vars.screen_xl - 1 ) );

					window.vc_iframe.setCustomShortcodeCss( window.parent.vc.atts.css_editor.custom_css_response );
				}
				this.model.save( { params: this.model.attributes.params } );
			}
		} );

		window.parent.vc.edit_element_block_view.on( 'afterRender', function() {
			var $el = this.$el,
				widgets = ['porto_ultimate_heading', 'porto_buttons', 'porto_image_comparison', 'porto_interactive_banner', 'vc_custom_heading', 'vc_btn', 'porto_countdown', 'vc_single_image', 'porto_info_box'],
				shortcode = $el.attr( 'data-vc-shortcode' );
			if ( $.inArray( shortcode, widgets ) >= 0 ) {
				$el.find( 'select' ).each( function() {
					var $this = $( this ),
						el_class = $this.attr( 'class' ),
						index_last = el_class.indexOf( '_dynamic_source' );
					if ( index_last >= 0 ) {
						var index_first = el_class.lastIndexOf( ' ', index_last );
						if ( index_first == -1 ) {
							index_first = 0;
						}
						var field_name = el_class.substring( index_first, index_last ).trim(),
							field_index = field_name.indexOf( '_' ),
							field_type = '';
						if ( field_index > 0 ) {
							field_type = field_name.substring( 0, field_index );
						} else {
							field_type = field_name;
						}
						if ( field_type == 'field' || field_type == 'link' || field_type == 'image' ) {
							porto_wpb_dynamic_execute( $el, field_type, field_name );
						}
					}
				} );
			}

			var $obj = $( '[data-model-id="' + this.model.id + '"]' );
			if ( 'porto_hb_menu' === shortcode || 'porto_hb_toggle' === shortcode ) {
				this.on( 'tabChange', function () {
					portoWpbShowMenuPopup( $( '[data-model-id="' + this.model.id + '"]' ), this.active_tab_index );
				} );

				this.on( 'hide', function () {
					$( 'body' ).find( '#main-toggle-menu.show, #main-toggle-menu ul li.show, .main-menu>li.show' ).removeClass( 'show' );
				} );
			} else if ( 'porto_hb_switcher' === shortcode ) {
				$obj.find( '.porto-view-switcher' ).addClass( 'show' );
				this.on( 'hide', function () {
					$( 'body' ).find( '.porto-view-switcher.show' ).removeClass( 'show' );
				} );
			} else if ( 'porto_hb_search_form' === shortcode ) {
				this.on( 'tabChange', function () {
					var $searchForm = $( '[data-model-id="' + this.model.id + '"]' ).find( '.searchform-popup' );
					if ( $searchForm.hasClass( 'search-popup' ) || ( $searchForm.hasClass( 'advanced-search-layout' ) && $( window ).width() < 992 ) ) {
						if ( 2 == this.active_tab_index ) {
							if ( $searchForm.find( '.search-layout-reveal' ).length > 0 ) {
								portoToggleReveal( $searchForm, true );
							}
							$searchForm.find( '>.search-toggle' ).addClass( 'opened' );
							$searchForm.addClass( 'show' );
						} else if ( $searchForm.hasClass( 'show' ) ) {
							if ( $searchForm.find( '.search-layout-reveal' ).length > 0 ) {
								portoToggleReveal( $searchForm, false );
							}
							$searchForm.removeClass( 'show' );
							$searchForm.find( '>.search-toggle' ).removeClass( 'opened' );
						}
					}
				} );
				this.on( 'hide', function () {
					var $searchForm = $( 'body' ).find( '.searchform-popup.show' );
					if ( $searchForm.length ) {
						$searchForm.find( '.search-toggle.opened' ).removeClass( 'opened' );
						$searchForm.removeClass( 'show' );
						if ( $searchForm.find( '.search-layout-reveal' ).length > 0 ) {
							portoToggleReveal( $searchForm, false );
						}
					}
				} );
			}

			// Add extra params
			if ( this.model.attributes.params.css_params ) {
				var extra_params = json_decode( this.model.attributes.params.css_params );
				if ( null !== extra_params && 'object' == typeof extra_params ) {
					var $parentBody = $( window.parent.document.body );
					Object.entries( extra_params ).forEach( ( [ selector, value ] ) =>  {
						$parentBody.find( '[name=' + selector + ']' ).val( value );
					} );
				}
			}

			if ( window.parent.vc.atts && window.parent.vc.atts.css_editor && window.parent.vc.atts.css_editor.parse ) {
				var _parse = function(param) {
					var $cssInput = this.content().find('input.wpb_vc_param_value[name="' + param.param_name + '"]'),
						css = $cssInput.data("vcFieldManager") ? $cssInput.data("vcFieldManager").save() : '',
						responses = [ 'xxl', 'xl', 'lg', 'md', 'xs' ],
						wrap_class = '.vc_custom_' + Date.now();

					if ( css ) {
						wrap_class = substr( css, 0, strpos( css, '{' ) );
					}

					window.parent.vc.atts.css_editor.css_params = {};

					window.parent.vc.atts.css_editor.custom_css_response = '';

					responses.forEach( response => {
						window.parent.vc.atts.css_editor.custom_css_response += portoCssEditorResponsive( wrap_class, this.content().find( '.porto-layout-tab-content > [data-width="' + response + '"]' ), response, param.param_name );
					} );
					if ( ! css && window.parent.vc.atts.css_editor.custom_css_response ) {
						css = wrap_class + '{porto_vc_design_option}';
					}
					return css;
				}
				// if ( window.parent.wpb_edit_form_loaded ) {
				// 	window.parent.vc.atts.css_editor.parse = _parse;
				// } else {
					setTimeout(() => {
						window.parent.vc.atts.css_editor.parse = _parse;
					}, 1250);
				// }
			}
		} );

		function porto_wpb_dynamic_execute( $el, field_type, field_name ) {
			var $dynamic_source_object = $el.find( 'select.' + field_name + '_dynamic_source' ),
				dynamic_source = $dynamic_source_object.val(),
				$dynamic_content = $el.find( 'select.' + field_name + '_dynamic_content' ),
				$date_format_index = field_type == field_name ? 'field_date_format' : field_name + '_date_format';
			porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content );
			$dynamic_source_object.on( 'change', function() {
				dynamic_source = $( this ).val();
				if ( field_type == 'field' ) {
					porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.val(), [ 'post_date', 'post_modified' ], $date_format_index );
				}
				porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content );
			} );

			// Format date format
			if ( field_type == 'field' ) {
				porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.val(), [ 'post_date', 'post_modified' ], $date_format_index );
			}

			$dynamic_content.on( 'change', function() {
				if ( field_type == 'field' ) {
					porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.val(), [ 'post_date', 'post_modified' ], $date_format_index );
				}
			} );
		}

		function porto_wpb_dynamic_enable_subcontent( $el, dynamic_content_option, content_value, shortcode_param ) {
			var $sub_content = $el.find( '[data-vc-shortcode-param-name="' + shortcode_param + '"]' ),
				$sub_content_select = $el.find( '[name="' + shortcode_param + '"]' );
			if ( $sub_content.length ) {
				if ( content_value.indexOf( dynamic_content_option ) > -1 ) {
					if ( $sub_content.hasClass( 'vc_dependent-hidden' ) ) {
						$sub_content.removeClass( 'vc_dependent-hidden' );
						$sub_content_select.val( $sub_content_select.attr( 'value' ) );
					}
				} else {
					$sub_content.addClass( 'vc_dependent-hidden' );
					$sub_content_select.val( '' );
				}
			}
		}

		function porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content ) {
			$dynamic_content.find( '*' ).remove();
			if ( '' != dynamic_source && 'meta_field' != dynamic_source && $dynamic_content.length && !$dynamic_content.hasClass( '.vc_dependent-hidden' ) && porto_wpb_vars[dynamic_source] ) {
				if ( porto_wpb_vars[dynamic_source][field_type] ) {
					var $contents = porto_wpb_vars[dynamic_source][field_type],
						keys = Object.keys( $contents ),
						attribute = $dynamic_content.attr( 'data-option' ), selected_content = false,
						__ = wp.i18n.__;

					if ( keys.length ) {
						$dynamic_content.append( '<option class="" value="">' + __( 'Select Source...', 'porto' ) + '</option>' );
						for ( let index = 0; index < keys.length; index++ ) {
							var selected = '';
							if ( keys[index] == attribute ) {
								selected = 'selected="selected"';
								selected_content = true;
							}
							$dynamic_content.append( '<option class="' + keys[index] + '" value="' + keys[index] + '" ' + selected + '>' + $contents[keys[index]] + '</option>' );
						}
					}
					if ( selected_content ) {
						$dynamic_content.val( attribute ).addClass( attribute );
					}
				}
			}
		}

		/**
		 * Show menu Dropdown
		 * 
		 * @since 7.2.0
		 */
		function portoWpbShowMenuPopup( $menuWrap, tabActive ) {
			$menuWrap.find( '#main-toggle-menu.show, #main-toggle-menu ul li.show, .main-menu > li.has-sub.show' ).removeClass( 'show' );
			if ( $menuWrap.closest( '.vc_porto_hb_toggle' ).length > 0 ) {
				tabActive += 1;
			}
			if ( 3 == tabActive ) {
				// Top Level Menu
				$menuWrap.find('#main-toggle-menu').addClass( 'show' );
			} else if ( 4 == tabActive ) {
				// Menu Popup
				$menuWrap.find( '#main-toggle-menu' ).addClass( 'show' );
				$menuWrap.find( '#main-toggle-menu ul li.menu-item-has-children' ).eq(0).addClass( 'show' );
				$menuWrap.find( '.main-menu > li.has-sub' ).eq(0).addClass( 'show' );
			}
		}

		/**
		 * Show/Hide reveal search type
		 * 
		 * @since 7.2.0
		 */
		function portoToggleReveal( $element, isShow = true ) {
			while ( ! $element.parent().hasClass( 'vc_row' ) || $element.parent().hasClass( 'vc_inner' ) ) {
				$element = $element.parent();
				if ( isShow ) {
					$element.addClass( 'position-static' );
				} else {
					$element.removeClass( 'position-static' );
				}
			}
			if ( 'static' == $element.parent().css( 'position' ) ) {
				if ( isShow ) {
					$element.parent().addClass( 'position-relative' );
				} else {
					$element.parent().removeClass( 'position-relative' );
				}
			}
		}

		/**
		 * Get Responsive Styles in WPBakery Editor
		 * 
		 * @since 7.2.0
		 */
		function portoCssEditorResponsive( wrap_class, $wrap, response, param_name ) {
			var positions = ["top", "right", "bottom", "left"],
				layouts = ["margin", "border", "padding"];
			if ( $wrap.length > 0 ) {
				var css = '';
				layouts.forEach( layout => {
					positions.forEach( position => {
						var selector = param_name + '_' + layout + '_' + position + ( 'border' == layout ? '_width' : '' ) + '_' + response,
							val = $wrap.find( '[name=' + selector + ']' ).val();
						if ( val ) {
							val = val.replace(/\s+/, "").match(/^-?\d*(\.\d+){0,1}(%|in|cm|mm|em|rem|ex|pt|pc|px|vw|vh|vmin|vmax)$/) ? val : isNaN(parseFloat(val)) ? "" : parseFloat(val) + "px";
							if ( val ) {
								css += layout + '-' + position + ( 'border' == layout ? '-width' : '' ) + ':' + val + '!important;';
								window.parent.vc.atts.css_editor.css_params[selector] = val;
							}
						}
					} );
				} );
				if ( css ) {
					css = '@media(max-width:' + $wrap.data( 'size' ) + 'px){' + wrap_class + '{' + css + '}}';
					return css;
				}
			}
			return '';
		}
	}
	$( '.porto-block .wpb_column[class*="col-sm-"]' ).each( function() {
		let $this = $( this );
		if ( this.classList ) {
			this.classList.forEach( function( itemClass ) {
				if ( itemClass.indexOf( 'col-sm-' ) == 0 ) {
					$this.removeClass( itemClass );
				}
			} );
		}
	});

} );