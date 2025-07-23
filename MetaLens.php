<?php

/**
 * Class MetaLens
 *
 * A simple PHP class for reading (and writing) image metadata (EXIF) from JPG, JPEG, and TIFF files.
 *
 * @package PHP MetaLens
 */
class MetaLens
{
    /**
     * @var string Path to the image file
     */
    private string $imagePath;

    /**
     * MetaLens constructor.
     *
     * @param string|array $image Path to the image file or an array with 'tmp_name' key
     * @throws \InvalidArgumentException If the image is not a string or array, or missing 'tmp_name'
     */
    public function __construct($image)
    {
        if (is_array($image)) {
            if (! isset($image['tmp_name'])) {
                throw new \InvalidArgumentException("Image array must contain 'tmp_name' key.");
            }
            $this->imagePath = $image['tmp_name'];
        } elseif (is_string($image)) {
            $this->imagePath = $image;
        } else {
            throw new \InvalidArgumentException("Image must be a string or an array.");
        }
    }

    /**
     * Validates that the image file exists.
     *
     * @throws \InvalidArgumentException If the file does not exist
     */
    private function validateImage()
    {
        // Check if the file exists and is readable
        if (!file_exists($this->imagePath)) {
            throw new \InvalidArgumentException("Image file does not exist.");
        }
        if (!is_readable($this->imagePath)) {
            throw new \InvalidArgumentException("Image file is not readable.");
        }
        // Check if the file is a valid image type  
        // Using finfo to check the MIME type
        // This is more reliable than just checking the file extension
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $mimeType = finfo_file($finfo, $this->imagePath);
        finfo_close($finfo);


        if (!in_array($mimeType, ['image/jpeg', 'image/tiff'])) {
            return ["error" => "Unsupported image format: $mimeType. Try using JPG, JPEG, or TIFF.", "code" => 400];
        }
    }

    /**
     * Reads metadata from the image file.
     *
     * @return array Metadata array or error info
     * @throws \InvalidArgumentException If the file does not exist
     */
    public function readMetadata()
    {
        $this->validateImage();
        $extension = strtolower(pathinfo($this->imagePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'tiff'])) {
            return ["error" => "Unsupported image format: $extension. Try using JPG, JPEG, or TIFF.", "code" => 400];
        }
        $exif = exif_read_data($this->imagePath, 'ANY_TAG');

        if ($exif) {
            return $exif;
        }
        return ["error" => "Unable to read metadata", "code" => 500];
    }


    /**
     * Gets the image dimensions.
     *
     * @return array|null Array with width and height or null if not available
     */
    public function getGPS()
    {
        $exif = $this->readMetadata();
        if (isset($exif['GPSLatitude'], $exif['GPSLatitudeRef'], $exif['GPSLongitude'], $exif['GPSLongitudeRef'])) {
            $lat = $this->convertGPS($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
            $lon = $this->convertGPS($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
            return ['latitude' => $lat, 'longitude' => $lon];
        }
        return null; // No GPS data available
    }


    /**
     * Converts GPS coordinates from EXIF format to decimal degrees.
     *
     * @param string $gps GPS coordinate in EXIF format
     * @param string $ref Reference (N/S or E/W)
     * @return float Converted GPS coordinate in decimal degrees
     */
    private function convertGPS($gps, $ref)
    {
        $gps = explode('/', $gps);
        $deg = $gps[0] / $gps[1];
        $gps = explode('/', $gps[2]);
        $min = $gps[0] / $gps[1];
        $gps = explode('/', $gps[2]);
        $sec = $gps[0] / $gps[1];
        $result = $deg + ($min / 60) + ($sec / 3600);

        return ($ref == 'S' || $ref == 'W') ? $result * -1 : $result;
    }

    /**
     * Gets the image path.
     *
     * @return string Image file path
     */
    public function getImage()
    {
        return $this->imagePath;
    }
}
