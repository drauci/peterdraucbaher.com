jQuery( document ).ready( function( $ ) {
	'use strict';
	if ( window.parent.vc && window.parent.vc.events && vc.storage ) {
		
		window.parent.vc.edit_element_block_view.on( 'save', function() {
			if ( 'object' == typeof vc.atts.css_editor.css_params ) {
				this.model.attributes.params.css_params = Object.keys(vc.atts.css_editor.css_params).length ? json_encode( vc.atts.css_editor.css_params ) : '';
				if ( vc.atts.css_editor.custom_css_response ) {
					this.model.attributes.params.custom_css_response = vc.atts.css_editor.custom_css_response;
				}
				this.model.save( { params: this.model.attributes.params } );
			}
		} );

		window.parent.vc.edit_element_block_view.on( 'afterRender', function() {

			// Change visual mode to text mode
			if ( 'object' == typeof ( tinymce ) && tinymce.editors.content && $( '#post-body-content .composer-switch' ).hasClass( 'vc_backend-status' ) && 'tinymce' == getUserSetting( 'editor' ) ) {
				$( '#content-html' ).trigger( 'click' );
			}
			var $el = this.$el,
				widgets = ['porto_ultimate_heading', 'porto_buttons', 'porto_image_comparison', 'porto_interactive_banner', 'vc_custom_heading', 'vc_btn', 'porto_countdown', 'vc_single_image', 'porto_info_box'];
			if ( $.inArray( $el.attr( 'data-vc-shortcode' ), widgets ) >= 0 ) {
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

			$( 'body' ).on( 'click', '.porto-layout-onion-tabs > span', function (e) {
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

			// Add extra params
			if ( this.model.attributes.params.css_params ) {
				var extra_params = json_decode( this.model.attributes.params.css_params );
				if ( null !== extra_params && 'object' == typeof extra_params ) {
					Object.entries( extra_params ).forEach( ( [ selector, value ] ) =>  {
						$( '[name=' + selector + ']' ).val( value );
					} );
				}
			}

			if ( vc.atts && vc.atts.css_editor && vc.atts.css_editor.parse ) {
				var _parse = function(param) {
					var $cssInput = this.content().find('input.wpb_vc_param_value[name="' + param.param_name + '"]'),
						css = $cssInput.data("vcFieldManager") ? $cssInput.data("vcFieldManager").save() : '',
						responses = [ 'xxl', 'xl', 'lg', 'md', 'xs' ],
						wrap_class = '.vc_custom_' + Date.now();

					if ( css ) {
						wrap_class = substr( css, 0, strpos( css, '{' ) );
					}

					vc.atts.css_editor.css_params = {};

					vc.atts.css_editor.custom_css_response = '';

					responses.forEach( response => {
						vc.atts.css_editor.custom_css_response += portoCssEditorResponsive( wrap_class, this.content().find( '.porto-layout-tab-content > [data-width="' + response + '"]' ), response, param.param_name );
					} );
					if ( ! css && vc.atts.css_editor.custom_css_response ) {
						css = wrap_class + '{porto_vc_design_option}';
					}
					return css;
				}

				// if ( window.wpb_edit_form_loaded ) {
				// 	vc.atts.css_editor.parse = _parse;
				// } else {
					setTimeout(() => {
						vc.atts.css_editor.parse = _parse;
					}, 1250);
				// }
			}
		} );

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
								vc.atts.css_editor.css_params[selector] = val;
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

		function porto_wpb_dyanmic_content( dynamic_source, field_type, dynamic_content ) {
			dynamic_content.find( '*' ).remove();
			if ( '' != dynamic_source && 'meta_field' != dynamic_source && dynamic_content.length && !dynamic_content.hasClass( '.vc_dependent-hidden' ) && porto_wpb_vars[dynamic_source] ) {
				if ( porto_wpb_vars[dynamic_source][field_type] ) {
					var $contents = porto_wpb_vars[dynamic_source][field_type],
						keys = Object.keys( $contents ),
						attribute = dynamic_content.attr( 'data-option' ), selected_content = false;

					if ( keys.length ) {
						dynamic_content.append( '<option class="" value="">Select Source...</option>' );
						for ( let index = 0; index < keys.length; index++ ) {
							var selected = '';
							if ( keys[index] == attribute ) {
								selected = 'selected="selected"';
								selected_content = true;
							}
							dynamic_content.append( '<option class="' + keys[index] + '" value="' + keys[index] + '" ' + selected + '>' + $contents[keys[index]] + '</option>' );
						}
					}
					if ( selected_content ) {
						dynamic_content.val( attribute ).addClass( attribute );
					}
				}
			}
		}

		if ( typeof window.vc !== 'undefined' ) {
			$( document ).on( 'submit', '#post', function( e ) {
				var $saveas, $pubButton;
				if ( typeof e != 'undefined' && e.originalEvent && e.originalEvent.submitter && $( e.originalEvent.submitter ).attr( 'id' ) == 'save-post' ) {
					$saveas = $( '#save-post' );	
					$pubButton = $( '#publish1' );
				} else {
					$pubButton = $( '#publish' );
				}
				// After Post is published
				if ( /*'publish' == $( '#original_post_status' ).val() &&*/ typeof js_porto_admin_vars.wpb_backend_ajax !== 'undefined' && js_porto_admin_vars.wpb_backend_ajax == '1' ) {
					var __ = wp.i18n.__,
						$previewButton = $( '#post-preview' );
					if ( 'dopreview' == $( '#wp-preview' ).val() ) {
						if ( $pubButton.attr( 'name' ) == 'publish' ) {
							$( '#post_status' ).val( 'publish' );
							$( '#hidden_post_status' ).val( 'publish' );
							$( '#original_post_status' ).val( 'publish' );
						}
						$pubButton.html( __( 'Update', 'porto' ) ).removeClass( 'disabled' ).attr( 'value', 'Update' ).attr( 'name', 'save' );
						$pubButton.siblings( '.spinner' ).removeClass( 'is-active' );
						$previewButton.removeClass( 'disabled' );
					} else {
						// Stop Default Save 
						e.preventDefault();
						if ( typeof rankMathEditor != 'undefined' && rankMathEditor.assessor ) { // Rank math seo save
							let _smeta = rankMathEditor.assessor.saveMeta();
							let _sschemas = rankMathEditor.assessor.saveSchemas( _smeta );
							let _sredirection = rankMathEditor.assessor.saveRedirection( _sschemas );
							Promise.all([_smeta, _sschemas, _sredirection]).then((function() {}
							)).catch((function() {} ));
						}

						// Remove P tag
						var $content = $( '#content' );
						if ( $content.length ) {
							var content = $content.val().trim();

							if ( 0 == content.indexOf( '<p>[' ) ) {
								content = content.slice( 3, -4 );
								$content.val( content );
							} else if ( 0 == content.indexOf( '<p><span data-mce-type=' ) ) {
								// Backend Editor from Frontend Editor
								content = content.slice( 144, -143 );
								$content.val( content );
							}
						}

						$pubButton.html( __( 'Updating..', 'porto' ) ).attr( 'value', 'Updating..' );
						$( '#wpb-save-post' ).html( __( 'Loading..', 'porto' ) );
						var _ajaxData = '';
						if ( $pubButton.length ) {
							_ajaxData = $( '#publish' ).attr( 'name' ) + '=' + $( '#publish' ).attr( 'value' ) + '&' + $( '#post' ).serialize();
						} else if ( $saveas && $saveas.length ) {
							_ajaxData = $( '#save-post' ).attr( 'name' ) + '=' + $( '#save-post' ).attr( 'value' ) + '&' + $( '#post' ).serialize();
						}
						$.ajax( {
							url: js_porto_admin_vars.ajax_url.replace( 'admin-ajax', 'post' ),
							data: _ajaxData,
							method: 'post',
							success: function( response ) {
								var $alert = $( '<div class="vc_backend_message show-message success">' + __( 'Successfully Updated.', 'porto' ) + '</div>' );
								$( 'body' ).append( $alert );
								$( '#wpb-save-post' ).html( __( 'Update', 'porto' ) );
								if ( $pubButton.attr( 'name' ) == 'publish' ) {
									$( '#post_status' ).val( 'publish' );
									$( '#hidden_post_status' ).val( 'publish' );
									$( '#original_post_status' ).val( 'publish' );
								}
								$pubButton.html( __( 'Update', 'porto' ) ).removeClass( 'disabled' ).attr( 'value', 'Update' ).attr( 'name', 'save' );
								$pubButton.siblings( '.spinner' ).removeClass( 'is-active' );
								if ( $saveas && $saveas.length ) {
									$saveas.siblings( '.spinner' ).removeClass( 'is-active' );
									$saveas.removeClass( 'disabled' );
									$( '#publish' ).removeClass( 'disabled' );
								}
								$previewButton.removeClass( 'disabled' );
								$alert.fadeIn( 400 );
								var timerId = setTimeout( function() {
									$alert.fadeOut( 900, function() {
										$alert.remove();
									} );
								}, 3500 );
								$alert.on( 'click', function( e ) {
									clearTimeout( timerId );
									$alert.fadeOut( 900, function() {
										$alert.remove();
									} );
								} )
							}
						} ).fail( function( response ) {
							var $alert = $( '<div class="vc_backend_message show-message error">' + __( 'Updated Failed.', 'porto' ) + '</div>' );
							$( 'body' ).append( $alert );
							$alert.fadeIn( 400 );
							$( '#wpb-save-post' ).html( __( 'Update', 'porto' ) );
							if ( $pubButton.attr( 'name' ) == 'publish' ) {
								$( '#post_status' ).val( 'publish' );
								$( '#hidden_post_status' ).val( 'publish' );
								$( '#original_post_status' ).val( 'publish' );
							}
							$pubButton.html( __( 'Update', 'porto' ) ).removeClass( 'disabled' ).attr( 'value', 'Update' ).attr( 'name', 'save' );
							$pubButton.siblings( '.spinner' ).removeClass( 'is-active' );
							if ( $saveas && $saveas.length ) {
								$saveas.siblings( '.spinner' ).removeClass( 'is-active' );
								$saveas.removeClass( 'disabled' );
								$( '#publish' ).removeClass( 'disabled' );
							}
							$previewButton.removeClass( 'disabled' );
							var timerId = setTimeout( function() {
								$alert.fadeOut( 900, function() {
									$alert.remove();
								} );
							}, 3500 );
							$alert.on( 'click', function( e ) {
								clearTimeout( timerId );
								$alert.fadeOut( 900, function() {
									$alert.remove();
								} );
							} )
						} )
					}
				}
			} );
		}
	}
} );