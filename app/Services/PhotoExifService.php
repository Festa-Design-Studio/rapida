<?php

namespace App\Services;

use lsolesen\pel\PelEntry;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelInvalidArgumentException;
use lsolesen\pel\PelInvalidDataException;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelJpegInvalidMarkerException;
use lsolesen\pel\PelTag;

/**
 * Removes device-identifying EXIF tags from JPEG photos while preserving
 * the GPS block and DateTimeOriginal. UNDP webinar requirement: strip
 * device serials, preserve location and timestamp so analysts retain
 * spatial provenance when downloading the photo file.
 *
 * Note on EXIF 2.3+ tags: PEL ships constants only for EXIF 2.2. Tags
 * like BodySerialNumber (0xA431), LensMake (0xA433), LensModel (0xA434),
 * LensSerialNumber (0xA435), and CameraOwnerName (0xA430) are not in PEL's
 * valid-tag list and are silently dropped during the load->save roundtrip
 * here. So the explicit strip list below only covers EXIF 2.2 tags PEL
 * recognises; modern phone serials are removed automatically.
 */
class PhotoExifService
{
    /**
     * Tags in IFD0 that carry device or owner identity. These are removed.
     *
     * @var array<int>
     */
    private const DEVICE_TAGS_IFD0 = [
        PelTag::MAKE,
        PelTag::MODEL,
        PelTag::SOFTWARE,
        PelTag::ARTIST,
        PelTag::COPYRIGHT,
        0x013C, // HostComputer (no PEL constant)
    ];

    /**
     * Sanitize the JPEG at $path in place. Returns false if the file is not
     * a JPEG, has no EXIF block, or the EXIF could not be parsed.
     */
    public function sanitize(string $path): bool
    {
        if (! file_exists($path)) {
            return false;
        }

        try {
            $jpeg = new PelJpeg($path);
        } catch (PelJpegInvalidMarkerException|PelInvalidDataException) {
            return false;
        }

        $exif = $jpeg->getExif();
        if ($exif === null) {
            return false;
        }

        $tiff = $exif->getTiff();
        $ifd0 = $tiff?->getIfd();
        if ($ifd0 === null) {
            return false;
        }

        $this->stripEntries($ifd0, self::DEVICE_TAGS_IFD0);

        try {
            $jpeg->saveFile($path);
        } catch (PelInvalidArgumentException) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<int>  $tags
     */
    private function stripEntries(PelIfd $ifd, array $tags): void
    {
        foreach ($tags as $tag) {
            $entry = $ifd->getEntry($tag);
            if ($entry instanceof PelEntry) {
                $ifd->offsetUnset($tag);
            }
        }
    }
}
