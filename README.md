# Beaver Builder Hooks

This is the code we used to build https://hooks.wpbeaverbuilder.com/ 

Its released as-is you are free to do what you like with it :)

# How To Use
Here is an example from the BB pugin source as an example:


```php
 // Custom Layout CSS
 if ( 'published' == $node_status ) {
	 $css .= FLBuilderModel::get_layout_settings()->css;
 }

/**
 * Use this filter to modify the CSS that is compiled and cached for each builder layout.
 * @see fl_builder_render_css
 * @link https://kb.wpbeaverbuilder.com/article/117-plugin-filter-reference
 * @since 1.10
 */
 $css = apply_filters( 'fl_builder_render_css', $css, $nodes, $global_settings, $include_global );

 // Minify the CSS.
 if ( ! self::is_debug() ) {
  	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
	}
```
