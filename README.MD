# Imagestack

Make working with images simple and fun

Developed and maintained by [Simphiwe Hlabisa](https://github.com/simphiwehlabisa).

---

## Overview

### Examples

#### `LocalImage`

```php
<?php
try {
  // Create a new Imagestack object
  $imagestack = new Imagestack\Imagestack();

  $imagestack
    ->fromLocalFile('image.jpg')                     // load image.jpg
    ->resize(320, 200)                          // resize image to 320x200 pixels
    ->saveFile('new-image.png', 'image/png')      // convert to PNG and save a copy to new-image.png
    ->openInScreen();                               // output to the screen
} catch(Exception $error) {
  // Handle errors
  echo $err->getMessage();
}
```

#### `Remotefile`

```php
<?php
try {
  // Create a new Imagestack object
  $imagestack = new Imagestack\Imagestack();

  $imagestack
    ->fromHttpFile('mime uri link')                     // load image.jpg
    ->resize(320, 200)                          // resize image to 320x200 pixels
    ->saveFile('new-image.png', 'image/png')      // convert to PNG and save a copy to new-image.png
    ->openInScreen();                               // output to the screen
} catch(Exception $error) {
  // Handle errors
  echo $err->getMessage();
}
```

## Features

- Supports reading, writing, and converting GIF, JPEG, PNG, WEBP, BMP formats.
- Reads and writes files, data URIs
- Manipulation: crop, resize
- Chainable methods
- Uses exceptions

## Installation

include the library manually:

```php
<?php
require 'src/Imagestack.php';
```

## API

Order of awesomeness:

1. Load an image
2. Manipulate the image
3. Save/output the image

API tips:

- Methods that return a Imagestack object are chainable.
- You can pass a file or data URI to the constructor to avoid calling `fromLocalFile` or `fromHttpFile`.

### Loaders

#### `fromHttpFile($uri)`

Loads an image from a data URI.

- `$uri`\* (string) - A data URI.

Returns a Imagestack object.

#### `fromLocalFile($file)`

Loads an image from a file.

- `$file`\* (string) - The image file to load.

Creates a new image.

### Saver

#### `saveFile($file, $mimeType, $quality)`

Writes the image to a file.

- `$mimeType` (string) - The image format to output as a mime type (defaults to the original mime type).
- `$quality` (int) - Image quality as a percentage (default 100).

Returns a Imagestack object.

#### `openInScreen($mimeType, $quality)`

Outputs the image to the screen. Must be called before any output is sent to the screen.

- `$mimeType` (string) - The image format to output as a mime type (defaults to the original mime type).
- `$quality` (int) - Image quality as a percentage (default 100).

Returns a Imagestack object.

### Manipulation

#### `crop($x1, $y1, $x2, $y2)`

Crop the image.

- \$x1 - Top left x coordinate.
- \$y1 - Top left y coordinate.
- \$x2 - Bottom right x coordinate.
- \$y2 - Bottom right x coordinate.

Returns a Imagestack object.

#### `resize($width, $height)`

Resize an image to the specified dimensions. If only one dimension is specified, the image will be resized proportionally.

- `$width`\* (int) - The new image width.
- `$height`\* (int) - The new image height.

Returns a Imagestack object.

### `Run script`

- To run script type in your terminal `php -S localhost:8082`.
- Point your browser to `http://localhost:8082/`.
