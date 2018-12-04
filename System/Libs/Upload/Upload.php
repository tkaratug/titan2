<?php
/*************************************************
* Titan-2 Mini Framework
* File Upload Library
*
* Author   : Turan Karatuğ
* Web      : http://www.titanphp.com
* Docs     : http://kilavuz.titanphp.com
* Github   : http://github.com/tkaratug/titan2
* License  : MIT
*
*************************************************/
namespace System\Libs\Upload;

class Upload
{
	// File field
	private $file 			= [];

	// Allowed mime types
	private $allowedTypes 	= [];

	// Max image width
	private $maxWidth 		= 0;

	// Max image height
	private $maxHeight 		= 0;

	// Max file size (kb)
	private $maxSize 		= 0;

	// Upload path
	private $uploadPath 	= ROOT_DIR . '/Public/upload';

	// Filename
	private $filename 		= null;

	// Error messages
	private $error 			= '';

	/**
	* Initialize upload process
	*
	* @param array $config
	* @return $this
	*/
	public function init($config = [])
	{
		if (array_key_exists('allowed_types', $config))
			$this->allowedTypes = $config['allowed_types'];

		if (array_key_exists('max_width', $config))
			$this->maxWidth     = $config['max_width'];

		if (array_key_exists('max_height', $config))
			$this->maxHeight    = $config['max_height'];

		if (array_key_exists('max_size', $config))
			$this->maxSize      = $config['max_size'];

		if (array_key_exists('upload_path', $config))
			$this->uploadPath   = $config['upload_path'];

		return $this;
	}

	/**
	* Start upload process
	*
	* @param array $field
	* @return boolean
	*/
	public function handle()
	{
		if ($this->_checkAllowedTypes() && $this->_checkMaxSize() && $this->_checkUploadPath()) {
			if (is_uploaded_file($this->file['tmp_name'])) {

				// Filename
				if (is_null($this->filename))
					$this->filename = $this->file['name'];

				if (move_uploaded_file($this->file['tmp_name'], $this->uploadPath . '/' . $this->filename)) {
					return true;
				} else {
					$this->error = lang('upload', 'upload_error');
					return false;
				}

			} else
				return false;
		}
	}

	/**
	* Set file source
	*
	* @param array $files
	* @return $this
	*/
	public function file($file = [])
	{
		$this->file = $file;
		return $this;
	}

	/**
	* Set allowed mime types
	*
	* @param array $types
	* @return $this
	*/
	public function allowedTypes($types = [])
	{
		$this->allowedTypes = $types;
		return $this;
	}

	/**
	* Set max. image width
	* @param int $width
	* @return $this
	*/
	public function maxWidth($width)
	{
		$this->maxWidth = $width;
		return $this;
	}

	/**
	* Set max. image height
	*
	* @param int $height
	* @return $this
	*/
	public function maxHeight($height)
	{
		$this->maxHeight = $height;
		return $this;
	}

	/**
	* Set max. file size (kb)
	*
	* @param int $size
	* @return $this
	*/
	public function maxSize($size)
	{
		$this->maxSize = $size;
		return $this;
	}

	/**
	* Set upload path
	*
	* @param string $path
	* @return $this
	*/
	public function uploadPath($path)
	{
		$this->uploadPath = $path;
		return $this;
	}

	public function filename($name)
	{
		$this->filename = $name;
		return $this;
	}

	/**
	* Check allowed mime types
	*
	* @return boolean
	*/
	private function _checkAllowedTypes()
	{
		if (count($this->allowedTypes) > 0) {

			$filepath  = pathinfo($this->file['name']);
			$extension = $filepath['extension'];

			if (!in_array($extension, $this->allowedTypes)) {
				$this->error = lang('upload', 'file_type_error');
				return false;
			} else {
				if (in_array($extension, ['jpg', 'png', 'gif'])) {
					if ($this->maxWidth > 0 || $this->maxHeight > 0) {
						list($width, $height) = getimagesize($this->file['tmp_name']);
						
						if ($width > $this->maxWidth || $height > $this->maxHeight) {
							$this->error = lang('upload', 'max_dimension_error', ['%s' => $this->maxWidth, '%t' => $this->maxHeight]);
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	* Check max. file size
	*
	* @return boolean
	*/
	private function _checkMaxSize()
	{
		if ($this->maxSize > 0) {
			if ($this->file['size'] > ($this->maxSize * 1024)) {
				$this->error = lang('upload', 'max_size_error', $this->maxSize);
				return false;
			}
		}

		return true;
	}

	/**
	* Check upload path
	*
	* @return boolean
	*/
	private function _checkUploadPath()
	{
		if (!file_exists($this->uploadPath)) {
			$this->error = lang('upload', 'wrong_upload_path_error', $this->uploadPath);
			return false;
		}

        if (!is_writable($this->uploadPath)) {
            $this->error = lang('upload', 'permission_error');
            return false;
        }

		return true;
	}

	/**
	* Get error messages
	*
	* @return string
	*/
	public function errorMessage()
	{
		return $this->error;
	}

}
