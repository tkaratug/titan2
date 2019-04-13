<?php
/*************************************************
 * Titan-2 Mini Framework
 * Image Manipulation Library
 *
 * Author   : Turan Karatuğ
 * Web      : http://www.titanphp.com
 * Docs     : http://kilavuz.titanphp.com
 * Github   : http://github.com/tkaratug/titan2
 * License  : MIT
 *
 *************************************************/

namespace System\Libs\Image;

class Image
{
    // File
    private $file;

    // Image
    private $image;

    // Temporary Image
    private $tempImage;

    // Image width
    private $width;

    // Image height
    private $height;

    // Image new width
    private $newWidth;

    // Image new height
    private $newHeight;

    // HTML Size
    private $htmlSize;

    // Image format
    private $format;

    // Image extension
    private $extension;

    // Image size
    private $size;

    // Basename
    private $basename;

    // Dirname
    private $dirname;

    // Crop coordinates
    private $cropCoordinates;

    // RGB color
    private $rgb = array(255, 255, 255);

    // Image quality
    private $quality = 100;

    // Error messages
    private $error = "";

    // Image Formats
    private $imageFormats = [
        'jpeg' => 2,
        'jpg' => 2,
        'gif' => 1,
        'png' => 3
    ];

    /**
     * Load image file
     *
     * @param string $file
     * @return $this|boolean
     */
    public function load($file)
    {
        $this->file = public_path($file);
        $this->_imageInfo();

        if (!$this->error)
            return $this;
        else
            return false;
    }

    /**
     * Load image from an URL
     *
     * @param string $url
     * @return $this|boolean
     */
    public function loadUrl($url)
    {
        $this->file = $url;
        $this->_fileInfo();

        if (!$this->format) {
            $this->error = lang('image', 'invalid_url');
            return false;
        } else {
            $this->_createImage();
            $this->_updateDimensions();
            return $this;
        }
    }

    /**
     * Get image info
     */
    private function _imageInfo()
    {
        if (is_file($this->file)) {
            $this->_fileInfo();
            if (!$this->_isImage()) {
                $this->error = lang('image', 'invalid_file', ['%s' => $this->file]);
            } else {
                $this->_createImage();
            }
        } else {
            $this->error = lang('image', 'not_accessible');
        }
    }

    /**
     * Get image dimensions
     *
     * @return $this
     */
    private function _dimensions()
    {
        list ($this->width, $this->height, $this->htmlSize, $this->format) = getimagesize($this->file);
        return $this;
    }

    /**
     * Update image dimensions
     *
     * @return $this
     */
    private function _updateDimensions()
    {
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);

        return $this;
    }

    /**
     * Get file info
     */
    private function _fileInfo()
    {
        $pathinfo = pathinfo($this->file);
        $this->_mimeType();
        $this->basename = $pathinfo['basename'];
        $this->dirname = $pathinfo['dirname'];
        $this->format = (isset($this->imageFormats[$this->extension]) ? $this->imageFormats[$this->extension] : null);
    }

    /**
     * Get mime type
     */
    private function _mimeType()
    {
        $size = getimagesize($this->file);
        $mimeType = $size['mime'];

        $mimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];

        if (isset($mimeTypes[$mimeType])) {
            $this->extension = $mimeTypes[$mimeType];
        } else {
            $this->error = lang('image', 'invalid_mime');
        }
    }

    /**
     * Check the file if it is image
     *
     * @return boolean
     */
    private function _isImage()
    {
        $this->_dimensions();

        if (!$this->format)
            return false;
        else
            return true;
    }

    /**
     * Create an empty image
     *
     * @param int $width
     * @param int $height
     * @param string $extension
     * @param boolean $alpha
     * @return $this|boolean
     */
    public function createEmptyImage($width, $height, $extension = 'jpg', $alpha = false)
    {
        if (!$width || !$height)
            return false;

        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($this->width, $this->height);

        if ($alpha) {
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
            $backgroundColor = imagecolorallocatealpha($this->image, $this->rgb[0], $this->rgb[1], $this->rgb[2], $alpha);
        } else {
            $backgroundColor = imagecolorallocate($this->image, $this->rgb[0], $this->rgb[1], $this->rgb[2]);
        }

        imagefill($this->image, 0, 0, $backgroundColor);
        $this->extension = $extension;
        return $this;
    }

    /**
     * Create image
     *
     * @return $this
     */
    private function _createImage()
    {
        $extension = ($this->extension == 'jpg') ? 'jpeg' : $this->extension;
        $functionName = "imagecreatefrom{$extension}";

        if (function_exists($functionName))
            $this->image = $functionName($this->file);
        else
            $this->error = lang('image', 'invalid_function');

        return $this;
    }

    /**
     * Set RGB color
     *
     * @param array $rgb
     * @return $this|boolean
     */
    public function setBgColor($rgb)
    {
        if (is_array($rgb)) {
            $this->rgb = $rgb;
            return $this;
        }

        if ($this->_hexToRgb($rgb))
            return $this;

        return false;
    }

    /**
     * Convert from Hex to RGB
     *
     * @param string $hexColor
     * @return $this|boolean
     */
    private function _hexToRgb($hexColor)
    {
        $hexColor = str_replace('#', '', $hexColor);

        if (strlen($hexColor) == 3)
            $hexColor .= $hexColor;

        if (strlen($hexColor) != 6)
            return false;

        $this->rgb = [
            hexdec(substr($hexColor, 0, 2)),
            hexdec(substr($hexColor, 2, 2)),
            hexdec(substr($hexColor, 4, 2))
        ];

        return $this;
    }

    /**
     * Set coordinates to crop
     *
     * @param int $x
     * @param int $y
     * @return $this|boolean
     */
    public function setCropCoordinates($x, $y)
    {
        $this->cropCoordinates = [$x, $y, $this->width, $this->height];
        return $this;
    }

    public function resize($newWidth = null, $newHeight = null, $method = null)
    {
        if (!$newWidth && !$newHeight) {
            $this->error = lang('image', 'new_dim_expected');
            return false;
        } else if (!is_resource($this->image)) {
            return false;
        }

        $this->newWidth = $newWidth;
        $this->newHeight = $newHeight;

        $this->_calculateNewDimensions();

        if ($method)
            $method = '_resizeWith' . ucfirst($method);

        if (!method_exists($this, $method))
            $method = "_resizeWithNoMethod";

        $this->$method()->_updateDimensions();

        return $this;
    }

    /**
     * Calculate new dimensions
     */
    private function _calculateNewDimensions()
    {
        $this->_checkForPercentages();

        if (!$this->newWidth)
            $this->newWidth = $this->width / ($this->height / $this->newHeight);
        else if (!$this->newHeight)
            $this->newHeight = $this->height / ($this->width / $this->newWidth);
    }

    /**
     * Calculate new percentages
     */
    private function _checkForPercentages()
    {
        if (strpos($this->newWidth, '%'))
            $this->newWidth = round($this->width * (preg_replace('/[^0-9]/', '', $this->newWidth) / 100));

        if (strpos($this->newHeight, '%'))
            $this->newHeight = round($this->height * (preg_replace('/[^0-9]/', '', $this->newHeight) / 100));
    }

    /**
     * Resize image without any method
     *
     * @return $this
     */
    private function _resizeWithNoMethod()
    {
        $this->tempImage = imagecreatetruecolor($this->newWidth, $this->newHeight);
        imagecopyresampled($this->tempImage, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);

        $this->image = $this->tempImage;

        return $this;
    }

    /**
     * Fill method
     */
    private function _fill()
    {
        imagefill($this->tempImage, 0, 0, imagecolorallocate($this->tempImage, $this->rgb[0], $this->rgb[1], $this->rgb[2]));
    }

    /**
     * Resize image with "Fill" method
     *
     * @return $this
     */
    private function _resizeWithFill()
    {
        $this->tempImage = imagecreatetruecolor($this->newWidth, $this->newHeight);
        $this->_fill();

        if (($this->width / $this->height) >= ($this->newWidth / $this->newHeight)) {
            $dif_w = $this->newWidth;
            $dif_h = $this->height * ($this->newWidth / $this->width);
            $dif_x = 0;
            $dif_y = round(($this->newHeight - $dif_h) / 2);
        } else {
            $dif_w = $this->width * ($this->newHeight / $this->height);
            $dif_h = $this->newHeight;
            $dif_x = round(($this->newWidth - $dif_w) / 2);
            $dif_y = 0;
        }

        imagecopyresampled($this->tempImage, $this->image, $dif_x, $dif_y, 0, 0,
            $dif_w, $dif_h, $this->width, $this->height);

        $this->image = $this->tempImage;

        return $this;
    }

    /**
     * Resize image with "Crop" method
     *
     * @return $this
     */
    private function _resizeWithCrop()
    {
        if (!is_array($this->cropCoordinates))
            $this->cropCoordinates = [0, 0, $this->width, $this->height];

        $this->tempImage = imagecreatetruecolor($this->newWidth, $this->newHeight);

        $this->_fill();

        imagecopyresampled($this->tempImage, $this->image, $this->cropCoordinates[0],
            $this->cropCoordinates[1], 0, 0, $this->cropCoordinates[2],
            $this->cropCoordinates[3], $this->width, $this->height);

        $this->image = $this->tempImage;

        return $this;
    }

    /**
     * Flip image
     *
     * @param string $orientation
     * @return $this|boolean
     */
    public function flip($orientation = 'horizontal')
    {
        $orientation = strtolower($orientation);

        if ($orientation != 'horizontal' && $orientation != 'vertical')
            return false;

        $w = imagesx($this->image);
        $h = imagesy($this->image);
        $this->tempImage = imagecreatetruecolor($w, $h);
        $method = '_flip' . ucfirst($orientation);

        $this->$method($w, $h);

        $this->image = $this->tempImage;

        return $this;
    }

    /**
     * Flip horizontal
     *
     * @param int $w
     * @param int $h
     * @return void
     */
    private function _flipHorizontal($w, $h)
    {
        for ($x = 0; $x < $w; $x++) {
            imagecopy($this->tempImage, $this->image, $x, 0, ($w - $x - 1), 0, 1, $h);
        }
    }

    /**
     * Flip vertical
     *
     * @param int $w
     * @param int $h
     * @return void
     */
    private function _flipVertical($w, $h)
    {
        for ($y = 0; $y < $h; $y++) {
            imagecopy($this->tempImage, $this->image, 0, $y, 0, ($h - $y - 1), $w, 1);
        }
    }

    /**
     * Rotate image
     *
     * @param int $degrees
     * @return $this
     */
    public function rotate($degrees)
    {
        $backgroundColor = imagecolorallocate($this->image, $this->rgb[0], $this->rgb[1], $this->rgb[2]);

        $this->image = imagerotate($this->image, $degrees, $backgroundColor);

        imagealphablending($this->image, true);
        imagesavealpha($this->image, true);

        $this->_updateDimensions();

        return $this;
    }

    /**
     * Set a text to write on image
     *
     * @param string $text
     * @param array $options
     * @return $this|boolean
     */
    public function text($text, $options = [])
    {
        if (!$text)
            return false;

        if (!isset($options['size']))
            $options['size'] = 5;

        if (isset($options['color']))
            $this->setBgColor($options['color']);

        $textColor = imagecolorallocate($this->image, $this->rgb[0], $this->rgb[1], $this->rgb[2]);
        $dimensions = $this->_textDimensions($text, $options);

        $options['x'] = isset($options['x']) ? $options['x'] : 0;
        $options['y'] = isset($options['y']) ? $options['y'] : 0;

        if (is_string($options['x']) && is_string($options['y']))
            list ($options['x'], $options['y']) = $this->_calculatePosition($options['x'], $options['y'], $dimensions['width'], $dimensions['height']);

        if (isset($options['background_color']) && $options['background_color'])
            $this->_textBackgroundColor($dimensions, $options);

        if (isset($options['truetype']) && $options['truetype'])
            $this->_addTrueTypeText($text, $textColor, $options);
        else
            imagestring($this->image, $options['size'], $options['x'], $options['y'], $text, $textColor);

        return $this;
    }

    /**
     * Set text dimensions
     *
     * @param string $text
     * @param array $options
     * @return void
     */
    private function _textDimensions($text, $options)
    {
        if (isset($options['truetype']) && $options['truetype']) {
            $textDimensions = imagettfbbox($options['size'], 0, $options['font'], $text);
            return [$textDimensions[4], $options['size']];
        }

        if ($options['size'] > 5)
            $options['size'] = 5;

        return [
            'width' => imagefontwidth($options['size']) * strlen($text),
            'height' => imagefontheight($options['size'])
        ];
    }

    /**
     * Calculate text position
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return array
     */
    private function _calculatePosition($x, $y, $width, $height)
    {
        switch ($y) {
            case 'top':
            default:
                $y = 0;
                break;
            case 'bottom':
                $y = $this->height - $height;
                break;
            case 'middle':
                switch ($x) {
                    case 'left':
                    case 'right':
                        $y = ($this->height / 2) - ($height / 2);
                        break;
                    case 'center':
                        $y = ($this->height - $height) / 2;
                        break;
                }
                break;
        }

        switch ($x) {
            case 'left':
            default:
                $x = 0;
                break;
            case 'center':
                $x = ($this->width - $width) / 2;
                break;
            case 'right':
                $x = $this->width - $width;
                break;
        }

        return [$x, $y];
    }

    /**
     * Set background color of the text
     *
     * @param array $dimensions
     * @param array $options
     * @return void
     */
    private function _textBackgroundColor($dimensions, $options)
    {
        $this->setBgColor($options['background_color']);

        $this->tempImage = imagecreatetruecolor($dimensions['width'], $dimensions['height']);
        $backgroundColor = imagecolorallocate($this->tempImage, $this->rgb[0], $this->rgb[1], $this->rgb[2]);

        imagefill($this->tempImage, 0, 0, $backgroundColor);
        imagecopy($this->image, $this->tempImage, $options['x'], $options['y'], 0, 0, $dimensions['width'], $dimensions['height']);
    }

    /**
     * Add true type text
     *
     * @param string $text
     * @param string $textColor
     * @param array $options
     * @return void
     */
    private function _addTrueTypeText($text, $textColor, $options)
    {
        imagettftext($this->image, $options['size'], 0, $options['x'], ($options['y'] + $options['size']), $textColor, $options['font'], $text);
    }

    /**
     * Merge images
     *
     * @param string $image
     * @param array $position
     * @param int $alpha
     * @return $this|boolean
     */
    public function merge($image, $position, $alpha = 100)
    {
        if (!file_exists($image)) {
            $this->error = lang('image', 'invalid_image');
            return false;
        }

        list ($w, $h) = getimagesize($image);

        if (is_string($position[0]) && is_string($position[1]))
            $position = $this->_calculatePosition($position[0], $position[1], $w, $h);

        $pathinfo = pathinfo($image);
        $extension = strtolower($pathinfo['extension']);
        $extension = ($extension == 'jpg' ? 'jpeg' : $extension);
        $functionName = 'imagecreatefrom' . $extension;

        if (function_exists($functionName))
            $imageToMerge = $functionName($image);
        else
            $this->error = lang('image', 'invalid_image_file');

        list ($x, $y) = $position;

        if (is_numeric($alpha) && (($alpha > 0) && ($alpha < 100)))
            imagecopymerge($this->image, $imageToMerge, $x, $y, 0, 0, $w, $h, $alpha);
        else
            imagecopy($this->image, $this->imageToMerge, $x, $y, 0, 0, $w, $h);

        return $this;
    }

    /**
     * Set image quality
     *
     * @param int $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     * Save created image file
     *
     * @param string $destination
     * @return boolean
     */
    public function save($destination)
    {
        if (!is_dir(dirname(public_path($destination)))) {
            $this->error = lang('image', 'invalid_destination');
            return false;
        }

        return $this->_outputImage($destination);
    }

    /**
     * Show created image
     *
     * @return boolean
     */
    public function show()
    {
        if (headers_sent()) {
            $this->error = lang('image', 'headers_sent');
            return false;
        }

        header("Content-type: image/{$this->extension}");
        $this->_outputImage();
        imagedestroy($this->image);
        exit;
    }

    /**
     * Output created image
     *
     * @param string $destination
     * @return boolean
     */
    private function _outputImage($destination = null)
    {
        $pathinfo = pathinfo($destination);
        $extension = (array_key_exists('extension', $pathinfo)) ? strtolower($pathinfo['extension']) : $this->extension;

        if ($extension == 'jpg' || $extension == 'jpeg') {
            imagejpeg($this->image, $destination, $this->quality);
        } else if ($extension == 'png') {
            imagepng($this->image, $destination);
        } else if ($extension == 'gif') {
            imagegif($this->image, $destination);
        } else {
            $this->_reset();
            return false;
        }

        $this->_reset();
    }

    /**
     * Reset class variables
     */
    private function _reset()
    {
        foreach (get_class_vars(get_class($this)) as $name => $default) {
            $this->$name = $default;
        }
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
