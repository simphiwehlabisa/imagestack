<?php
//  Make working with images simple and fun
//  Developed and maintained by Simphiwe Hlabisa <https://github.com/simphiwehlabisa>.
//  Source: https://github.com/simphiwehlabisa/imagestack

namespace Imagestack;

class Imagestack
{

  const
    ERR_FILE_NOT_FOUND = 1,
    ERR_GD_NOT_ENABLED = 2,
    ERR_INVALID_DATA_URI = 3,
    ERR_INVALID_IMAGE = 4,
    ERR_UNSUPPORTED_FORMAT = 5,
    ERR_WEBP_NOT_ENABLED = 6,
    ERR_WRITE = 7,
    ERR_CROP_VALUES = 8;

  protected $image;
  protected $mimeType;
  protected $exif; //

  //
  // Creates a new Imagestack object.
  //
  //  $image (string) - An image file or a data URI to load.
  //
  public function __construct($image = null)
  {

    if (extension_loaded('gd')) { // Check for the required GD extension
      ini_set('gd.jpeg_ignore_warning', 1); // Ignore JPEG warnings that cause imagecreatefromjpeg() to fail
    } else {
      throw new \Exception('Required extension GD is not loaded.', self::ERR_GD_NOT_ENABLED);
    }

    // Load an image through the constructor
    if (preg_match('/^data:(.*?);/', $image)) {
      $this->fromHttpFile($image);
    } elseif ($image) {
      $this->fromLocalFile($image);
    }
  }

  //
  // Destroys the image resource
  //
  public function __destruct()
  {
    if ($this->image !== null && get_resource_type($this->image) === 'gd') {
      imagedestroy($this->image);
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////
  // Loaders
  //////////////////////////////////////////////////////////////////////////////////////////////////

  //
  // Loads an image from a data URI.
  //
  //  $uri* (string) - A data URI.
  //
  // Returns a Imagestack object.
  //
  public function fromHttpFile($uri)
  {
    // Basic formatting check
    preg_match('/^data:(.*?);/', $uri, $matches);
    if (!count($matches)) {
      throw new \Exception("Invalid data URI. use Uniform Resource Identifier not Uniform Resource Locator try this image instead \r\n data:image/jpeg;base64,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", self::ERR_INVALID_DATA_URI);
    }

    // Determine mime type
    $this->mimeType = $matches[1];
    if (!preg_match('/^image\/(gif|jpeg|png)$/', $this->mimeType)) {
      throw new \Exception(
        'Unsupported format: ' . $this->mimeType,
        self::ERR_UNSUPPORTED_FORMAT
      );
    }

    // Get image data
    $uri = base64_decode(preg_replace('/^data:(.*?);base64,/', '', $uri));
    $this->image = imagecreatefromstring($uri);
    if (!$this->image) {
      throw new \Exception("Invalid image data.", self::ERR_INVALID_IMAGE);
    }

    return $this;
  }

  //
  // Loads an image from a file.
  //
  //  $file* (string) - The image file to load.
  //
  // Returns a Imagestack object.
  //
  public function fromLocalFile($file)
  {
    // Check if the file exists and is readable. We're using fopen() instead of file_exists()
    // because not all URL wrappers support the latter.
    $handle = @fopen($file, 'r');
    if ($handle === false) {
      throw new \Exception("File not found: ${file}", self::ERR_FILE_NOT_FOUND);
    }
    fclose($handle);

    // Get image info
    $info = getimagesize($file);
    if ($info === false) {
      throw new \Exception("Invalid image file: ${file}", self::ERR_INVALID_IMAGE);
    }
    $this->mimeType = $info['mime'];

    // Create image object from file
    switch ($this->mimeType) {
      case 'image/jpeg':
        $this->image = imagecreatefromjpeg($file);
        break;
      case 'image/png':
        $this->image = imagecreatefrompng($file);
        break;
      case 'image/webp':
        $this->image = imagecreatefromwebp($file);
        break;
      case 'image/bmp':
      case 'image/x-ms-bmp':
      case 'image/x-windows-bmp':
        $this->image = imagecreatefrombmp($file);
        break;
    }
    if (!$this->image) {
      throw new \Exception("Unsupported format: " . $this->mimeType, self::ERR_UNSUPPORTED_FORMAT);
    }

    // Convert pallete images to true color images
    imagepalettetotruecolor($this->image);

    // Load exif data from JPEG images
    if ($this->mimeType === 'image/jpeg' && function_exists('exif_read_data')) {
      $this->exif = @exif_read_data($file);
    }

    return $this;
  }


  //////////////////////////////////////////////////////////////////////////////////////////////////
  // Savers
  //////////////////////////////////////////////////////////////////////////////////////////////////

  protected function generate($mimeType = null, $quality = 100)
  {
    // Format defaults to the original mime type
    $mimeType = $mimeType ?: $this->mimeType;

    // Ensure quality is a valid integer
    if ($quality === null) $quality = 100;
    $quality = self::keepWithin((int) $quality, 0, 100);

    // Capture output
    ob_start();

    // Generate the image
    switch ($mimeType) {
      case 'image/gif':
        imagesavealpha($this->image, true);
        imagegif($this->image, null);
        break;
      case 'image/jpeg':
        imageinterlace($this->image, true);
        imagejpeg($this->image, null, $quality);
        break;
      case 'image/png':
        imagesavealpha($this->image, true);
        imagepng($this->image, null, round(9 * $quality / 100));
        break;
      case 'image/webp':
        // Not all versions of PHP will have webp support enabled
        if (!function_exists('imagewebp')) {
          throw new \Exception(
            'WEBP support is not enabled in your version of PHP.',
            self::ERR_WEBP_NOT_ENABLED
          );
        }
        imagesavealpha($this->image, true);
        imagewebp($this->image, null, $quality);
        break;
      case 'image/bmp':
      case 'image/x-ms-bmp':
      case 'image/x-windows-bmp':
        imageinterlace($this->image, true);
        imagebmp($this->image, null, $quality);
        break;
      default:
        throw new \Exception('Unsupported format: ' . $mimeType, self::ERR_UNSUPPORTED_FORMAT);
    }

    // Stop capturing
    $data = ob_get_contents();
    ob_end_clean();

    return [
      'data' => $data,
      'mimeType' => $mimeType
    ];
  }

  public function saveFile($file, $mimeType = null, $quality = 100)
  {
    $image = $this->generate($mimeType, $quality);

    // Save the image to file
    if (!file_put_contents($file, $image['data'])) {
      throw new \Exception("Failed to write image to file: ${file}", self::ERR_WRITE);
    }

    return $this;
  }

  public function openInScreen($mimeType = null, $quality = 100)
  {
    $image = $this->generate($mimeType, $quality);

    // Output the image to stdout
    header('Content-Type: ' . $image['mimeType']);
    echo $image['data'];

    return $this;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////
  // Utilities
  //////////////////////////////////////////////////////////////////////////////////////////////////

  protected static function keepWithin($value, $min, $max)
  {
    if ($value < $min) return $min;
    if ($value > $max) return $max;
    return $value;
  }

  public function getAspectRatio()
  {
    return $this->getWidth() / $this->getHeight();
  }

  public function getHeight()
  {
    return (int) imagesy($this->image);
  }



  public function getWidth()
  {
    return (int) imagesx($this->image);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////
  // Manipulation
  //////////////////////////////////////////////////////////////////////////////////////////////////

  //
  // croping image.
  //
  //  $x1 - Top left x coordinate.
  //  $y1 - Top left y coordinate.
  //  $x2 - Bottom right x coordinate.
  //  $y2 - Bottom right x coordinate.
  //
  // Returns a Imagestack object.
  //
  public function crop($x1, $y1, $x2, $y2)
  {
    if ($y1 >= $y2 || $x1 >= $x2)
      throw new \Exception('Invalid crop values $x2 must be bigger tha $x1 and $y2 must be bigger than $y1 . ', self::ERR_CROP_VALUES);
    // Keep crop within ubukhulu besithombe
    $x1 = self::keepWithin($x1, 0, $this->getWidth());
    $x2 = self::keepWithin($x2, 0, $this->getWidth());
    $y1 = self::keepWithin($y1, 0, $this->getHeight());
    $y2 = self::keepWithin($y2, 0, $this->getHeight());

    // Croping isithombe lana
    $this->image = imagecrop($this->image, [
      'x' => min($x1, $x2),
      'y' => min($y1, $y2),
      'width' => abs($x2 - $x1),
      'height' => abs($y2 - $y1)
    ]);

    return $this;
  }
  //
  // Resize an image to the specified dimensions. If only one dimension is specified, the image will
  // be resized proportionally.
  //
  //  $width* (int) - The new image width.
  //  $height* (int) - The new image height.
  //
  // Returns a Imagestack object.
  //
  public function resize(int $width = null, int $height = null)
  {
    // No dimentions specified
    if (!$width && !$height) {
      return $this;
    }

    // Resize to width
    if ($width && !$height) {
      $height = $width / $this->getAspectRatio();
    }

    // Resize to height
    if (!$width && $height) {
      $width = $height * $this->getAspectRatio();
    }

    // If the dimensions are the same, there's no need to resize
    if ($this->getWidth() === $width && $this->getHeight() === $height) {
      return $this;
    }

    // We can't use imagescale because it doesn't seem to preserve transparency properly. The
    // workaround is to create a new truecolor image, allocate a transparent color, and copy the
    // image over to it using imagecopyresampled.
    $newImage = imagecreatetruecolor($width, $height);
    $transparentColor = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
    imagecolortransparent($newImage, $transparentColor);
    imagefill($newImage, 0, 0, $transparentColor);
    imagecopyresampled(
      $newImage,
      $this->image,
      0,
      0,
      0,
      0,
      $width,
      $height,
      $this->getWidth(),
      $this->getHeight()
    );

    // Swap out the new image
    $this->image = $newImage;

    return $this;
  }
}
