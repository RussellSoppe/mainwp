<?php
namespace MainWP\Dashboard;

/**
 * MainWP_QQ2_Uploaded_File_Xhr
 *
 * DO NOT TOUCH - part of http://github.com/valums/file-uploader ! (@see js/fileuploader.js)
 * Handle file uploads via XMLHttpRequest
 */

class MainWP_QQ2_Uploaded_File_Xhr {
	/**
	 * Save the file to the specified path
	 *
	 * @return boolean TRUE on success
	 */
	public function save( $path ) {
		$input    = fopen( 'php://input', 'r' );
		$temp     = tmpfile();
		$realSize = stream_copy_to_stream( $input, $temp );
		fclose( $input );

		if ( $realSize != $this->get_size() ) {
			return false;
		}

		$hasWPFileSystem = MainWP_Utility::get_wp_file_system();
		/** @global WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		if ( $hasWPFileSystem && ! empty( $wp_filesystem ) ) {
			if ( ! is_dir( dirname( dirname( dirname( $path ) ) ) ) ) {
				if ( ! $wp_filesystem->mkdir( dirname( dirname( dirname( $path ) ) ) ) ) {
					throw new \Exception( 'Unable to create the MainWP bulk upload directory, please check your system configuration.' );
				}
			}

			if ( ! is_dir( dirname( dirname( $path ) ) ) ) {
				if ( ! $wp_filesystem->mkdir( dirname( dirname( $path ) ) ) ) {
					throw new \Exception( 'Unable to create the MainWP bulk upload directory, please check your system configuration.' );
				}
			}

			if ( ! is_dir( dirname( $path ) ) ) {
				if ( ! $wp_filesystem->mkdir( dirname( $path ) ) ) {
					throw new \Exception( 'Unable to create the MainWP bulk upload directory, please check your system configuration.' );
				}
			}

			fseek( $temp, 0, SEEK_SET );
			$wp_filesystem->put_contents( $path, stream_get_contents( $temp ) );
		} else {
			if ( ! is_dir( dirname( $path ) ) ) {
				mkdir( dirname( $path ), 0777, true );
			}

			$target = fopen( $path, 'w' );
			fseek( $temp, 0, SEEK_SET );
			if ( stream_copy_to_stream( $temp, $target ) <= 0 ) {
				return false;
			}
			fclose( $target );
		}

		if ( ! file_exists( $path ) ) {
			throw new \Exception( 'Unable to save the file to the MainWP upload directory, please check your system configuration.' );
		}

		return true;
	}

	public function get_name() {
		return $_GET['qqfile'];
	}

	public function get_size() {
		if ( isset( $_SERVER['CONTENT_LENGTH'] ) ) {
			return (int) $_SERVER['CONTENT_LENGTH'];
		} else {
			throw new \Exception( 'Getting content length is not supported.' );
		}
	}
}
