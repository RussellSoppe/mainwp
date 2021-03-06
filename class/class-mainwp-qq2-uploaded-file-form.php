<?php
namespace MainWP\Dashboard;

/**
 * MainWP_QQ2_Uploaded_File_Form
 *
 * DO NOT TOUCH - part of http://github.com/valums/file-uploader ! (@see js/fileuploader.js)
 * Handle file uploads via regular form post (uses the $_FILES array)
 */

class MainWP_QQ2_Uploaded_File_Form {
	/**
	 * Save the file to the specified path
	 *
	 * @return boolean TRUE on success
	 */
	public function save( $path ) {
		$wpFileSystem = MainWP_Utility::get_wp_file_system();

		if ( $wpFileSystem != null ) {
			$path  = str_replace( MainWP_Utility::get_base_dir(), '', $path );
			$moved = $wpFileSystem->put_contents( $path, file_get_contents( $_FILES['qqfile']['tmp_name'] ) );
		} else {
			$moved = move_uploaded_file( $_FILES['qqfile']['tmp_name'], $path );
		}

		if ( ! $moved ) {
			return false;
		}

		return true;
	}

	public function get_name() {
		return $_FILES['qqfile']['name'];
	}

	public function get_size() {
		return $_FILES['qqfile']['size'];
	}
}
