# PHP MetaLens
Unlock the Hidden Data in Your Photos with PHP!

A simple PHP class for reading (and writing) image metadata (EXIF) from JPG, JPEG, and TIFF files.

## Features
- Read image metadata using PHP's exif_read_data
- Exception handling for missing or unsupported files
- PHPUnit tests included

## Usage

```
require_once 'MetaLens.php';
$meta = new MetaLens('/path/to/image.jpg');
$data = $meta->readMetadata();
print_r($data);
```

## Testing

Run PHPUnit tests:

```
vendor\bin\phpunit MetaLensTest.php
```

## Requirements
- PHP 8+
- exif extension enabled
- Composer (for dependencies and PHPUnit)

## License
MIT License
