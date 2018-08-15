<?php
/**
 * Main class used to parse folders and generate a hooks page.
 */
class Hooks {

	function __construct() {

		include( 'config.php' );

		$this->nav = $nav;
		$this->config = $config;

		if ( ! isset( $_GET['package'] ) ) {
			header( 'Location: https://hooks.wpbeaverbuilder.com/bb-plugin/' );
			exit();
		}
		// work out config
		$package = basename( $_GET['package'] );
		if ( ! isset( $this->config[ $package ] ) ) {
			die( 'no config' );
		}

		$this->package = $this->config[ $package ];
		$this->dir = 'repos/' . $package;

		// populate $this->php with file contents to parse later.
		$php = array();
		chdir( $this->dir );
		$files = $this->listdir( '.' );
		foreach ( $files as $key => $filename ) {
			if ( substr( $filename, -4 ) == '.php' ) {
				$this->php[ $filename ] = file_get_contents( $filename );
			}
		}
	}


	/**
	 * Get all the actions
	 */
	function get_actions() {

		$php = $this->php;
		$actions = '';
		foreach ( $php as $file => $data ) {

			if ( preg_match_all( '#do_action\s?\(\s?["|\']([^"|\']*)#', $data, $out ) ) {

				$tokens = token_get_all( $data );

				foreach ( $out[1] as $hook ) {

					$text = '';
					$link = '';
					$version = '';
					$alt = '';

					foreach ( $tokens as $token ) {
						if ( $token[0] == T_DOC_COMMENT ) {
							$check = sprintf( '#@see %s\s#', $hook );
							if ( preg_match( $check, $token[1] ) ) {
								$text = $token[1];
								break;
							}
						}
					}

					if ( $text ) {

						if ( preg_match( '#\@since\s([0-9a-z-\.]+)#', $text, $since ) ) {
							$version = $since[1];
							$text = str_replace( '* @since ' . $version , '', $text );
						}

						if ( preg_match( '#\@link\s([0-9a-z-\.\:\/]+)#', $text, $match ) ) {
							$link = $match[1];
							$text = str_replace( '* @link ' . $link , '', $text );
						}
						$text = str_replace( '* @see ' . $hook, '', $text );

						$text = ltrim( $text, '/**' );
						$text = ltrim( $text );
						$text = ltrim( $text, '*' );
						$text = ltrim( $text );

						$text = rtrim( $text, '*/' );
						$text = rtrim( $text );

						$text = str_replace( '*', '<br />', $text );
					}

					$grep = $this->grep( $hook, $file );
					$actions .= '<tr>';
					$actions .= '<td data-title="Name">' . $hook . '</td>';
					$actions .= sprintf( '<td data-title="Location">%s<br /><em>Line: %s</em></td>',
						ltrim( $file, './' ),
						$grep['line']
					);

					$this->actions[ $hook ] = array(
						'file' => ltrim( $file, './' ),
						'line' => $grep['line'],
					);

					$action = htmlspecialchars( ltrim( $grep['text'] ) );

					if ( $text ) {
						$alt .= $text;
					}

					if ( $version ) {
						$alt .= '<br />Since: ' . $version;
					}

					if ( $link ) {
						$alt .= '<br />See: ' . sprintf( '<a href="%s">External link</a>', $link );
					}

					if ( $alt ) {
						$alt = sprintf( '<br /><br /><em>%s</em>', $alt );
					}

					$actions .= sprintf( '<td data-title="Context"><code>%s</code>%s</td>', $action, $alt
					);
					$actions .= '</tr>';
					//$action_count++;
				}
			}
		}
		return $actions;
	}

	/**
	 * Get all the filters.
	 */
	function get_filters() {

		$php = $this->php;
		$actions = '';
		foreach ( $php as $file => $data ) {

			if ( preg_match_all( '#apply_filters\s?\(\s?["|\']([^"|\']*)#', $data, $out ) ) {

				$tokens = token_get_all( $data );

				foreach ( $out[1] as $hook ) {

					$text = '';
					$alt = '';
					$version = '';
					$since = '';
					$link = '';
					foreach ( $tokens as $token ) {
						if ( $token[0] == T_DOC_COMMENT ) {
							$check = sprintf( '#@see %s\s#', $hook );
							if ( preg_match( $check, $token[1] ) ) {
								$text = $token[1];
								break;
							}
						}
					}
					if ( $text ) {

						if ( preg_match( '#\@since\s([0-9a-z-\.]+)#', $text, $since ) ) {
							$version = $since[1];
							$text = str_replace( '* @since ' . $version , '', $text );
						}

						if ( preg_match( '#\@link\s([0-9a-z-\.\:\/]+)#', $text, $match ) ) {
							$link = $match[1];
							$text = str_replace( '* @link ' . $link , '', $text );
						}

						$text = str_replace( '* @see ' . $hook, '', $text );

						$text = ltrim( $text, '/**' );
						$text = ltrim( $text );
						$text = ltrim( $text, '*' );
						$text = ltrim( $text );

						$text = rtrim( $text, '*/' );
						$text = rtrim( $text );

						$text = str_replace( '*', '<br />', $text );
					}

					$grep = $this->grep( $hook, $file );
					$actions .= '<tr>';
					$actions .= '<td data-title="Name">' . $hook . '</td>';
					$actions .= sprintf( '<td data-title="Location">%s<br /><em>Line: %s</em></td>',
						ltrim( $file, './' ),
						$grep['line']
					);
					$this->filters[ $hook ] = array(
						'file' => ltrim( $file, './' ),
						'line' => $grep['line'],
					);
					$action = htmlspecialchars( ltrim( $grep['text'] ) );

					if ( $text ) {
						$alt .= $text;
					}

					if ( $version ) {
						$alt .= '<br />Since: ' . $version;
					}

					if ( $link ) {
						$alt .= '<br />See: ' . sprintf( '<a href="%s">External link</a>', $link );
					}

					if ( $alt ) {
						$alt = sprintf( '<br /><br /><em>%s</em>', $alt );
					}

					$actions .= sprintf( '<td data-title="Context"><code>%s</code>%s</td>', $action, $alt );
					$actions .= '</tr>';
				}
			}
		}
		return $actions;
	}

	/**
	 * Grep a file and find the line number and the contents of the line.
	 */
	function grep( $search, $file ) {

		$lines = file( $file, FILE_IGNORE_NEW_LINES );
		$line_index = 0;
		$bad_lines = '';
		foreach ( $lines as $this_line ) {

			if ( ( stristr( $this_line, '"' . $search . '"' ) || stristr( $this_line, "'" . $search . "'" ) ) && ( stristr( $this_line, 'do_action' ) || stristr( $this_line, 'apply_filters' ) ) ) {

				$line = (string) intval( $line_index ) + 1;

				return array(
					'line' => $line,
					'text' => $this_line,
				);
			}
			$line_index++;
		}
		return;
	}

	/**
	 * get a list of all files in a folder.
	 */
	function listdir( $dir ) {
		$files = array();
		$dir_iterator = new RecursiveDirectoryIterator( $dir );
		$iterator = new RecursiveIteratorIterator( $dir_iterator, RecursiveIteratorIterator::SELF_FIRST );

		foreach ( $iterator as $file ) {
			array_push( $files, $file->getPathname() );
		}
		return $files;
	}

}
