<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/

namespace extend;
use Exception;
/**
 * PHP class to resize and scale images
 * @url : https://github.com/gumlet/php-image-resize
 */
class ImageResize
{
    const CROPTOP = 1;
    const CROPCENTRE = 2;
    const CROPCENTER = 2;
    const CROPBOTTOM = 3;
    const CROPLEFT = 4;
    const CROPRIGHT = 5;
    const CROPTOPCENTER = 6;
    const IMG_FLIP_HORIZONTAL = 0;
    const IMG_FLIP_VERTICAL = 1;
    const IMG_FLIP_BOTH = 2;
    public $quality_jpg = 85;
    public $quality_webp = 85;
    public $quality_png = 6;
    public $quality_truecolor = true;
    public $interlace = 1;
    public $source_type;
    protected $source_image;
    protected $original_w;
    protected $original_h;
    protected $dest_x = 0;
    protected $dest_y = 0;
    protected $source_x;
    protected $source_y;
    protected $dest_w;
    protected $dest_h;
    protected $source_w;
    protected $source_h;
    protected $source_info;
    protected $filters = [];
    protected $msg='';
    /**
     * Create instance from a strng
     *
     * @param string $image_data
     * @return ImageResize
     * @throws Exception
     */
    public static function createFromString($image_data)
    {
        if (empty($image_data) || $image_data === null) {
            throw new Exception('image_data must not be empty');
        }
        $resize = new self('data://application/octet-stream;base64,' . base64_encode($image_data));
        return $resize;
    }
    /**
     * Add filter function for use right before save image to file.
     *
     * @param callable $filter
     * @return $this
     */
    public function addFilter(callable $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }
    /**
     * Apply filters.
     *
     * @param $image resource an image resource identifier
     * @param $filterType filter type and default value is IMG_FILTER_NEGATE
     */
    protected function applyFilter($image, $filterType = IMG_FILTER_NEGATE)
    {
        foreach ($this->filters as $function) {
            $function($image, $filterType);
        }
    }
    /**
     * Loads image source and its properties to the instanciated object
     */
    public function __construct()
    {
        if (!defined('IMAGETYPE_WEBP')) {
            define('IMAGETYPE_WEBP', 18);
        }
        if(((int)ini_get('gd.jpeg_ignore_warning')) !==1 ){
            ini_set ('gd.jpeg_ignore_warning', 1);
        }
    }

    /** ------------------------------------------------------------------
     * add
     * @return ImageResize
     *---------------------------------------------------------------------*/
    public function add(){
        return $this->resize($this->getSourceWidth(), $this->getSourceHeight());
    }

    /** ------------------------------------------------------------------
     * checkImage
     * @param string $filename
     * @return bool
     *---------------------------------------------------------------------*/
    public function checkImage($filename){
        if (empty($filename) || (substr($filename, 0, 5) !== 'data:' && !is_file($filename))) {
            $this->msg='File does not exist';
            return false;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (strstr(finfo_file($finfo, $filename), 'image') === false) {
            $this->msg='Unsupported file type';
            return false;
        }
        //if (!$image_info = getimagesize($filename, $this->source_info)) {
        $image_info = getimagesize($filename);
        //}
        if (!$image_info) {
            $this->msg='Could not read file';
            return false;
        }
        list(
            $this->original_w,
            $this->original_h,
            $this->source_type
            ) = $image_info;
        switch ($this->source_type) {
            case IMAGETYPE_GIF:
                $this->source_image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_JPEG:
                $this->source_image = $this->imageCreateJpegfromExif($filename);
                // set new width and height for image, maybe it has changed
                $this->original_w = imagesx($this->source_image);
                $this->original_h = imagesy($this->source_image);
                break;
            case IMAGETYPE_PNG:
                $this->source_image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_BMP:
                $jpg=$this->bmp2imge($filename);
                if($jpg===false) {
                    $this->msg='bmp can not change to image';
                    return false;
                }
                $newName=str_ireplace('.bmp','.jpg',$filename);
                if(imagejpeg($jpg,$newName)===false){
                    $this->msg='bmp can not save to jpg';
                    return false;
                }
                imagedestroy($jpg);
                $this->source_image = imagecreatefrompng($newName);
                unlink($filename);
                break;
            case IMAGETYPE_WEBP:
                if (version_compare(PHP_VERSION, '5.5.0', '<')) {
                    $this->msg='For WebP support PHP >= 5.5.0 is required';
                    return false;
                }
                if(!function_exists('imagecreatefromwebp')){
                    $this->msg='WebP not support ';
                    return false;
                }
                $this->source_image = imagecreatefromwebp($filename);
                break;
            default:
                $this->msg='Unsupported image type';
                return false;
        }
        if (!$this->source_image) {
            $this->msg='Could not load image';
            return false;
        }
        return true;
    }
    public function getMsg(){
        return $this->msg;
    }
    // http://stackoverflow.com/a/28819866
    public function imageCreateJpegfromExif($filename)
    {
        $img = imagecreatefromjpeg($filename);
        if (!function_exists('exif_read_data') || !isset($this->source_info['APP1'])  || strpos($this->source_info['APP1'], 'Exif') !== 0) {
            return $img;
        }
        try {
            $exif = @exif_read_data($filename);
        } catch (Exception $e) {
            $exif = null;
        }
        if (!$exif || !isset($exif['Orientation'])) {
            return $img;
        }
        $orientation = $exif['Orientation'];
        if ($orientation === 6 || $orientation === 5) {
            $img = imagerotate($img, 270, null);
        } elseif ($orientation === 3 || $orientation === 4) {
            $img = imagerotate($img, 180, null);
        } elseif ($orientation === 8 || $orientation === 7) {
            $img = imagerotate($img, 90, null);
        }
        if ($orientation === 5 || $orientation === 4 || $orientation === 7) {
            if(function_exists('imageflip')) {
                imageflip($img, IMG_FLIP_HORIZONTAL);
            } else {
                $this->imageFlip($img, IMG_FLIP_HORIZONTAL);
            }
        }
        return $img;
    }

    /*** ------------------------------------------------------------------
     * Saves new image
     *
     * @param string $filename
     * @param string $image_type
     * @param integer $quality
     * @param integer $permissions
     * @return static
     * @throws Exception
     *---------------------------------------------------------------------
     */
    public function save($filename, $image_type = null, $quality = null, $permissions = null)
    {
        $image_type = $image_type ?: $this->source_type;
        $quality = is_numeric($quality) ? (int) abs($quality) : null;
        switch ($image_type) {
            case IMAGETYPE_GIF:
                $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                $background = imagecolorallocatealpha($dest_image, 255, 255, 255, 1);
                imagecolortransparent($dest_image, $background);
                imagefill($dest_image, 0, 0, $background);
                imagesavealpha($dest_image, true);
                break;
            case IMAGETYPE_JPEG:
                $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                $background = imagecolorallocate($dest_image, 255, 255, 255);
                imagefilledrectangle($dest_image, 0, 0, $this->getDestWidth(), $this->getDestHeight(), $background);
                break;
            case IMAGETYPE_WEBP:
                if (version_compare(PHP_VERSION, '5.5.0', '<')) {
                    throw new Exception('For WebP support PHP >= 5.5.0 is required');
                }
                $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                $background = imagecolorallocate($dest_image, 255, 255, 255);
                imagefilledrectangle($dest_image, 0, 0, $this->getDestWidth(), $this->getDestHeight(), $background);
                break;
            case IMAGETYPE_PNG:
                if (!$this->quality_truecolor && !imageistruecolor($this->source_image)) {
                    $dest_image = imagecreate($this->getDestWidth(), $this->getDestHeight());
                    $background = imagecolorallocatealpha($dest_image, 255, 255, 255, 1);
                    imagecolortransparent($dest_image, $background);
                    imagefill($dest_image, 0, 0, $background);
                } else {
                    $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                }
                imagealphablending($dest_image, false);
                imagesavealpha($dest_image, true);
                break;
        }
        imageinterlace($dest_image, $this->interlace);

        imagegammacorrect($this->source_image, 2.2, 1.0);

        imagecopyresampled(
            $dest_image,
            $this->source_image,
            $this->dest_x,
            $this->dest_y,
            $this->source_x,
            $this->source_y,
            $this->getDestWidth(),
            $this->getDestHeight(),
            $this->source_w,
            $this->source_h
        );

        imagegammacorrect($dest_image, 1.0, 2.2);
        $this->applyFilter($dest_image);
        switch ($image_type) {
            case IMAGETYPE_GIF:
                imagegif($dest_image, $filename);
                break;
            case IMAGETYPE_JPEG:
                if ($quality === null || $quality > 100) {
                    $quality = $this->quality_jpg;
                }
                imagejpeg($dest_image, $filename, $quality);
                break;
            case IMAGETYPE_WEBP:
                if (version_compare(PHP_VERSION, '5.5.0', '<')) {
                    throw new Exception('For WebP support PHP >= 5.5.0 is required');
                }
                if ($quality === null) {
                    $quality = $this->quality_webp;
                }
                imagewebp($dest_image, $filename, $quality);
                break;
            case IMAGETYPE_PNG:
                if ($quality === null ) {
                    $quality = $this->quality_png;
                }
                if($quality > 9){
                    $quality = 9;
                }
                imagepng($dest_image, $filename, $quality);
                break;
        }
        if ($permissions) {
            chmod($filename, $permissions);
        }
        imagedestroy($dest_image);
        return $this;
    }
    /**
     * Convert the image to string
     *
     * @param int $image_type
     * @param int $quality
     * @return string
     */
    public function getImageAsString($image_type = null, $quality = null)
    {
        $string_temp = tempnam(sys_get_temp_dir(), '');
        $this->save($string_temp, $image_type, $quality);
        $string = file_get_contents($string_temp);
        unlink($string_temp);
        return $string;
    }
    /**
     * Convert the image to string with the current settings
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getImageAsString();
    }
    /**
     * Outputs image to browser
     * @param string $image_type
     * @param integer $quality
     */
    public function output($image_type = null, $quality = null)
    {
        $image_type = $image_type ?: $this->source_type;
        header('Content-Type: ' . image_type_to_mime_type($image_type));
        $this->save(null, $image_type, $quality);
    }
    /**
     * Resizes image according to the given short side (short side proportional)
     *
     * @param integer $max_short
     * @param boolean $allow_enlarge
     * @return static
     */
    public function resizeToShortSide($max_short, $allow_enlarge = false)
    {
        if ($this->getSourceHeight() < $this->getSourceWidth()) {
            $ratio = $max_short / $this->getSourceHeight();
            $long = $this->getSourceWidth() * $ratio;
            $this->resize($long, $max_short, $allow_enlarge);
        } else {
            $ratio = $max_short / $this->getSourceWidth();
            $long = $this->getSourceHeight() * $ratio;
            $this->resize($max_short, $long, $allow_enlarge);
        }
        return $this;
    }
    /**
     * Resizes image according to the given long side (short side proportional)
     *
     * @param integer $max_long
     * @param boolean $allow_enlarge
     * @return static
     */
    public function resizeToLongSide($max_long, $allow_enlarge = false)
    {
        if ($this->getSourceHeight() > $this->getSourceWidth()) {
            $ratio = $max_long / $this->getSourceHeight();
            $short = $this->getSourceWidth() * $ratio;
            $this->resize($short, $max_long, $allow_enlarge);
        } else {
            $ratio = $max_long / $this->getSourceWidth();
            $short = $this->getSourceHeight() * $ratio;
            $this->resize($max_long, $short, $allow_enlarge);
        }
        return $this;
    }
    /**
     * Resizes image according to the given height (width proportional)
     *
     * @param integer $height
     * @param boolean $allow_enlarge
     * @return static
     */
    public function resizeToHeight($height, $allow_enlarge = false)
    {
        $ratio = $height / $this->getSourceHeight();
        $width = $this->getSourceWidth() * $ratio;
        $this->resize($width, $height, $allow_enlarge);
        return $this;
    }
    /**
     * Resizes image according to the given width (height proportional)
     *
     * @param integer $width
     * @param boolean $allow_enlarge
     * @return static
     */
    public function resizeToWidth($width, $allow_enlarge = false)
    {
        $ratio  = $width / $this->getSourceWidth();
        $height = $this->getSourceHeight() * $ratio;
        $this->resize($width, $height, $allow_enlarge);
        return $this;
    }
    /**
     * Resizes image to best fit inside the given dimensions
     *
     * @param integer $max_width
     * @param integer $max_height
     * @param boolean $allow_enlarge
     * @return ImageResize $this
     */
    public function resizeToBestFit($max_width, $max_height, $allow_enlarge = false)
    {
        if ($this->getSourceWidth() <= $max_width && $this->getSourceHeight() <= $max_height && $allow_enlarge === false) {
            return $this;
        }
        $ratio  = $this->getSourceHeight() / $this->getSourceWidth();
        $width = $max_width;
        $height = $width * $ratio;
        if ($height > $max_height) {
            $height = $max_height;
            $width = $height / $ratio;
        }
        return $this->resize($width, $height, $allow_enlarge);
    }
    /**
     * Resizes image according to given scale (proportionally)
     *
     * @param integer|float $scale
     * @return static
     */
    public function scale($scale)
    {
        $width  = $this->getSourceWidth() * $scale / 100;
        $height = $this->getSourceHeight() * $scale / 100;
        $this->resize($width, $height, true);
        return $this;
    }
    /**
     * Resizes image according to the given width and height
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $allow_enlarge
     * @return $this
     */
    public function resize($width, $height, $allow_enlarge = false)
    {
        if (!$allow_enlarge) {
            // if the user hasn't explicitly allowed enlarging,
            // but either of the dimensions are larger then the original,
            // then just use original dimensions - this logic may need rethinking
            if ($width > $this->getSourceWidth() || $height > $this->getSourceHeight()) {
                $width  = $this->getSourceWidth();
                $height = $this->getSourceHeight();
            }
        }
        $this->source_x = 0;
        $this->source_y = 0;
        $this->dest_w = $width;
        $this->dest_h = $height;
        $this->source_w = $this->getSourceWidth();
        $this->source_h = $this->getSourceHeight();
        return $this;
    }
    /**
     * Crops image according to the given width, height and crop position
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $allow_enlarge
     * @param integer $position
     * @return static
     */
    public function crop($width, $height, $allow_enlarge = false, $position = self::CROPCENTER)
    {
        if (!$allow_enlarge) {
            // this logic is slightly different to resize(),
            // it will only reset dimensions to the original
            // if that particular dimenstion is larger
            if ($width > $this->getSourceWidth()) {
                $width  = $this->getSourceWidth();
            }
            if ($height > $this->getSourceHeight()) {
                $height = $this->getSourceHeight();
            }
        }
        $ratio_source = $this->getSourceWidth() / $this->getSourceHeight();
        $ratio_dest = $width / $height;
        if ($ratio_dest < $ratio_source) {
            $this->resizeToHeight($height, $allow_enlarge);
            $excess_width = ($this->getDestWidth() - $width) / $this->getDestWidth() * $this->getSourceWidth();
            $this->source_w = $this->getSourceWidth() - $excess_width;
            $this->source_x = $this->getCropPosition($excess_width, $position);
            $this->dest_w = $width;
        } else {
            $this->resizeToWidth($width, $allow_enlarge);
            $excess_height = ($this->getDestHeight() - $height) / $this->getDestHeight() * $this->getSourceHeight();
            $this->source_h = $this->getSourceHeight() - $excess_height;
            $this->source_y = $this->getCropPosition($excess_height, $position);
            $this->dest_h = $height;
        }
        return $this;
    }
    /**
     * Crops image according to the given width, height, x and y
     *
     * @param integer $width
     * @param integer $height
     * @param integer $x
     * @param integer $y
     * @return static
     */
    public function freecrop($width, $height, $x = false, $y = false)
    {
        if ($x === false || $y === false) {
            return $this->crop($width, $height);
        }
        $this->source_x = $x;
        $this->source_y = $y;
        if ($width > $this->getSourceWidth() - $x) {
            $this->source_w = $this->getSourceWidth() - $x;
        } else {
            $this->source_w = $width;
        }
        if ($height > $this->getSourceHeight() - $y) {
            $this->source_h = $this->getSourceHeight() - $y;
        } else {
            $this->source_h = $height;
        }
        $this->dest_w = $width;
        $this->dest_h = $height;
        return $this;
    }
    /**
     * Gets source width
     *
     * @return integer
     */
    public function getSourceWidth()
    {
        return $this->original_w;
    }
    /**
     * Gets source height
     *
     * @return integer
     */
    public function getSourceHeight()
    {
        return $this->original_h;
    }
    /**
     * Gets width of the destination image
     *
     * @return integer
     */
    public function getDestWidth()
    {
        return $this->dest_w;
    }
    /**
     * Gets height of the destination image
     * @return integer
     */
    public function getDestHeight()
    {
        return $this->dest_h;
    }
    /**
     * Gets crop position (X or Y) according to the given position
     *
     * @param integer $expectedSize
     * @param integer $position
     * @return float|integer
     */
    protected function getCropPosition($expectedSize, $position = self::CROPCENTER)
    {
        $size = 0;
        switch ($position) {
            case self::CROPBOTTOM:
            case self::CROPRIGHT:
                $size = $expectedSize;
                break;
            case self::CROPCENTER:
            case self::CROPCENTRE:
                $size = $expectedSize / 2;
                break;
            case self::CROPTOPCENTER:
                $size = $expectedSize / 4;
                break;
        }
        return $size;
    }
    /**
     *  Flips an image using a given mode if PHP version is lower than 5.5
     *
     * @param  resource $image
     * @param  integer  $mode
     * @return null
     */
    public function imageFlip($image, $mode)
    {
        switch($mode) {
            case self::IMG_FLIP_HORIZONTAL: {
                $max_x = imagesx($image) - 1;
                $half_x = $max_x / 2;
                $sy = imagesy($image);
                $temp_image = imageistruecolor($image)? imagecreatetruecolor(1, $sy): imagecreate(1, $sy);
                for ($x = 0; $x < $half_x; ++$x) {
                    imagecopy($temp_image, $image, 0, 0, $x, 0, 1, $sy);
                    imagecopy($image, $image, $x, 0, $max_x - $x, 0, 1, $sy);
                    imagecopy($image, $temp_image, $max_x - $x, 0, 0, 0, 1, $sy);
                }
                break;
            }
            case self::IMG_FLIP_VERTICAL: {
                $sx = imagesx($image);
                $max_y = imagesy($image) - 1;
                $half_y = $max_y / 2;
                $temp_image = imageistruecolor($image)? imagecreatetruecolor($sx, 1): imagecreate($sx, 1);
                for ($y = 0; $y < $half_y; ++$y) {
                    imagecopy($temp_image, $image, 0, 0, 0, $y, $sx, 1);
                    imagecopy($image, $image, 0, $y, 0, $max_y - $y, $sx, 1);
                    imagecopy($image, $temp_image, 0, $max_y - $y, 0, 0, $sx, 1);
                }
                break;
            }
            case self::IMG_FLIP_BOTH: {
                $sx = imagesx($image);
                $sy = imagesy($image);
                $temp_image = imagerotate($image, 180, 0);
                imagecopy($image, $temp_image, 0, 0, 0, 0, $sx, $sy);
                break;
            }
            default:
                return null;
        }
        imagedestroy($temp_image);
    }

    public function bmp2imge($filename, $maxWidth = 65535, $maxHeight = 65535)
    {
        if (!($fh = @fopen($filename, 'rb'))) {
            $this->msg='Can not open ' . $filename;
            return false;
        }
        // read file header
        $meta = unpack('vtype/Vfilesize/Vreserved/Voffset', fread($fh, 14));
        // check for bitmap
        if ($meta['type'] != 19778) {
            $this->msg=$filename . ' is not a valid bitmap!';
            return false;
        }
        // read image header
        $meta += unpack(
            'Vheadersize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vcolors/Vimportant',
            fread($fh, 40)
        );
        if ($meta['width'] > $maxWidth) {
            $meta['width'] = 65535;
        }
        if ($meta['height'] > $maxHeight) {
            $meta['height'] = 65535;
        }
        // read additional 16bit header
        if ($meta['bits'] == 16) {
            $meta += unpack('VrMask/VgMask/VbMask', fread($fh, 12));
        }
        // set bytes and padding
        $meta['bytes'] = $meta['bits'] / 8;
        $meta['decal'] = 4 - (4 * (($meta['width'] * $meta['bytes'] / 4) - floor($meta['width'] * $meta['bytes'] / 4)));
        if ($meta['decal'] == 4) {
            $meta['decal'] = 0;
        }
        // obtain imagesize
        if ($meta['imagesize'] < 1) {
            $meta['imagesize'] = $meta['filesize'] - $meta['offset'];
            // in rare cases filesize is equal to offset so we need to read physical size
            if ($meta['imagesize'] < 1) {
                $meta['imagesize'] = filesize($filename) - $meta['offset'];
                if ($meta['imagesize'] < 1) {
                    $this->msg='Can not obtain filesize of ' . $filename . '!';
                    return false;
                }
            }
        }
        // calculate colors
        $meta['colors'] = !$meta['colors'] ? pow(2, $meta['bits']) : $meta['colors'];
        // read color palette
        $palette = [];
        if ($meta['bits'] < 16) {
            switch ($meta['headersize']) {
                // BITMAPCOREHEADER
                // OS21XBITMAPHEADER
                case 12:
                    fseek($fh, 0x1a);
                    break;
                // BITMAPINFOHEADER
                case 40:
                    fseek($fh, 0x36);
                    break;
                // OS22XBITMAPHEADER
                case 64:
                    fseek($fh, 0x4e);
                    break;
                // BITMAPV4HEADER
                case 108:
                    fseek($fh, 0x7a);
                    break;
                // BITMAPV5HEADER
                case 124:
                    fseek($fh, 0x8a);
                    break;
                default:
                    // No default
                    break;
            }
            $palette = unpack('l' . $meta['colors'], fread($fh, $meta['colors'] * 4));
            // in rare cases the color value is signed
            if ($palette[1] < 0) {
                foreach ($palette as $i => $color) {
                    $palette[$i] = $color + 16777216;
                }
            }
        }
        // create gd image
        $im = imagecreatetruecolor($meta['width'], $meta['height']);
        fseek($fh, $meta['offset']);
        $data = fread($fh, $meta['imagesize']);
        $p = 0;
        $vide = chr(0);
        $y = $meta['height'] - 1;
        $error = $filename . ' has not enough data!';
        // loop through the image data beginning with the lower left corner
        while ($y >= 0) {
            $x = 0;
            while ($x < $meta['width']) {
                switch ($meta['bits']) {
                    case 32:
                        if (!($part = substr($data, $p, 4))) {
                            $this->msg=$error;
                            return false;
                        }
                        $color = unpack('C4', $part . $vide);
                        $color[1] = ($color[4] << 16) | ($color[3] << 8) | $color[2];
                        break;
                    case 24:
                        if (!($part = substr($data, $p, 3))) {
                            $this->msg=$error;
                            return false;
                        }
                        $color = unpack('V', $part . $vide);
                        break;
                    case 16:
                        if (!($part = substr($data, $p, 2))) {
                            $this->msg=$error;
                            return false;
                        }
                        $color = unpack('v', $part);
                        $color[1] = (($color[1] & 0xf800) >> 8)
                            * 65536 + (($color[1] & 0x07e0) >> 3)
                            * 256 + (($color[1] & 0x001f) << 3);
                        break;
                    case 8:
                        $color = unpack('n', $vide . substr($data, $p, 1));
                        $color[1] = $palette[$color[1] + 1];
                        break;
                    case 4:
                        $color = unpack('n', $vide . substr($data, floor($p), 1));
                        $color[1] = ($p * 2) % 2 == 0 ? $color[1] >> 4 : $color[1] & 0x0F;
                        $color[1] = $palette[$color[1] + 1];
                        break;
                    case 1:
                        $color = unpack('n', $vide . substr($data, floor($p), 1));
                        switch (($p * 8) % 8) {
                            case 0:
                                $color[1] = $color[1] >> 7;
                                break;
                            case 1:
                                $color[1] = ($color[1] & 0x40) >> 6;
                                break;
                            case 2:
                                $color[1] = ($color[1] & 0x20) >> 5;
                                break;
                            case 3:
                                $color[1] = ($color[1] & 0x10) >> 4;
                                break;
                            case 4:
                                $color[1] = ($color[1] & 0x8) >> 3;
                                break;
                            case 5:
                                $color[1] = ($color[1] & 0x4) >> 2;
                                break;
                            case 6:
                                $color[1] = ($color[1] & 0x2) >> 1;
                                break;
                            case 7:
                                $color[1] = ($color[1] & 0x1);
                                break;
                            default:
                                // No default
                                break;
                        }
                        $color[1] = $palette[$color[1] + 1];
                        break;
                    default:
                        $this->msg=$filename . ' has ' . $meta['bits'] . ' bits and this is not supported!';
                        return false;
                }
                imagesetpixel($im, $x, $y, $color[1]);
                $x++;
                $p += $meta['bytes'];
            }
            $y--;
            $p += $meta['decal'];
        }
        fclose($fh);
        return $im;
    }

    /** ------------------------------------------------------------------
     * 获取图片扩展名，只能读出这几种格式 jpeg|png|gif|webp|bmp 的图片
     * @param string $filename
     * @return string:图片不存在、无法读取图片信息以及不是（jpeg|png|gif|webp|bmp）的图片 返回空字符串，返则对应格式图片扩展名的字符串（前面带点符号）
     *--------------------------------------------------------------------*/
    static public function getImagExtendName($filename){
        if (empty($filename) || (substr($filename, 0, 5) !== 'data:' && !is_file($filename))) {
            return '';
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (strstr(finfo_file($finfo, $filename), 'image') === false) {
            return '';
        }
        $image_info = getimagesize($filename);
        if (!$image_info) {
            return '';
        }
        switch ($image_info[2]) {
            case IMAGETYPE_GIF:
                return '.gif';
            case IMAGETYPE_JPEG:
                return '.jpg';
            case IMAGETYPE_PNG:
                return '.png';
            case IMAGETYPE_BMP:
                return '.bmp';
            case IMAGETYPE_WEBP:
                return '.webp';
            default:
                return '';
        }
    }


}