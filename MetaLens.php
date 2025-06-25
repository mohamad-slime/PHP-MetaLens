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
        if (!file_exists($this->imagePath)) {
            throw new \InvalidArgumentException("Image file does not exist.");
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
            return ["error" => "Unsupported image format: '.$extension' try using JPG, JPEG, or TIFF.", "code" => 400];
        }
        $exif = exif_read_data($this->imagePath, 'ANY_TAG');

        if ($exif) {
            return $exif;
        }
        return ["error" => "Unable to read metadata", "code" => 500];
    }

    /**
     * Writes metadata to the image file (not implemented).
     *
     * @param array $metadata Metadata to write
     * @return void
     */
    public function writeMetadata($metadata)
    {

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
