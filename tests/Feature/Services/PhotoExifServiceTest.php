<?php

use App\Services\PhotoExifService;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelTiff;

beforeEach(function () {
    $this->fixturesPath = sys_get_temp_dir().'/rapida-exif-fixtures';
    if (! is_dir($this->fixturesPath)) {
        mkdir($this->fixturesPath, 0755, true);
    }
    $this->jpegPath = $this->fixturesPath.'/test-'.uniqid().'.jpg';
    seedJpegWithExif($this->jpegPath);
});

afterEach(function () {
    if (isset($this->jpegPath) && file_exists($this->jpegPath)) {
        unlink($this->jpegPath);
    }
});

it('removes device-identifying tags from IFD0', function () {
    $exifBefore = exif_read_data($this->jpegPath);
    expect($exifBefore['Make'])->toBe('TestPhone-Maker')
        ->and($exifBefore['Model'])->toBe('TestModel-X')
        ->and($exifBefore['Software'])->toBe('CrisisCam 1.2');

    app(PhotoExifService::class)->sanitize($this->jpegPath);

    $exifAfter = readExifEntries($this->jpegPath);
    expect($exifAfter[PelTag::MAKE] ?? null)->toBeNull()
        ->and($exifAfter[PelTag::MODEL] ?? null)->toBeNull()
        ->and($exifAfter[PelTag::SOFTWARE] ?? null)->toBeNull();
});

it('preserves DateTimeOriginal in the EXIF sub-IFD', function () {
    // Note: PEL drops unknown EXIF 2.3+ device tags (BodySerialNumber 0xA431,
    // LensMake 0xA433, LensSerialNumber 0xA435, CameraOwnerName 0xA430) automatically
    // on save because they are not in PEL's allow-list. We cannot seed those tags
    // into a fixture for the same reason — addEntry rejects unknown tags. The
    // service's strip list therefore only enumerates the EXIF 2.2 device tags PEL
    // does recognise; the EXIF 2.3+ tags are removed by the load->save roundtrip
    // itself.
    sanitizeAndReadExif($this->jpegPath);

    $exif = exif_read_data($this->jpegPath);
    expect($exif['DateTimeOriginal'] ?? null)->toBe('2026:04:25 14:30:00');
});

it('preserves GPS latitude and longitude with the original values', function () {
    $latBefore = (exif_read_data($this->jpegPath))['GPSLatitude'] ?? null;
    $lonBefore = (exif_read_data($this->jpegPath))['GPSLongitude'] ?? null;
    expect($latBefore)->not->toBeNull()->and($lonBefore)->not->toBeNull();

    app(PhotoExifService::class)->sanitize($this->jpegPath);

    $exifAfter = exif_read_data($this->jpegPath);
    expect($exifAfter['GPSLatitude'] ?? null)->toBe($latBefore)
        ->and($exifAfter['GPSLongitude'] ?? null)->toBe($lonBefore)
        ->and($exifAfter['GPSLatitudeRef'] ?? null)->toBe('N')
        ->and($exifAfter['GPSLongitudeRef'] ?? null)->toBe('W');
});

it('returns false for files without EXIF', function () {
    $plainPath = $this->fixturesPath.'/plain-'.uniqid().'.jpg';
    $img = imagecreatetruecolor(20, 20);
    imagejpeg($img, $plainPath, 80);
    imagedestroy($img);

    $result = app(PhotoExifService::class)->sanitize($plainPath);

    expect($result)->toBeFalse();
    unlink($plainPath);
});

it('returns false for non-existent files', function () {
    expect(app(PhotoExifService::class)->sanitize('/tmp/does-not-exist-rapida.jpg'))->toBeFalse();
});

/**
 * Build a minimal JPEG carrying device-identifying EXIF tags + a GPS block +
 * DateTimeOriginal in the EXIF sub-IFD. Uses PEL itself for fixture creation
 * so the test does not depend on a binary fixture file in the repo.
 */
function seedJpegWithExif(string $path): void
{
    $img = imagecreatetruecolor(50, 50);
    imagefilledrectangle($img, 0, 0, 49, 49, imagecolorallocate($img, 100, 150, 200));
    imagejpeg($img, $path, 90);
    imagedestroy($img);

    $jpeg = new PelJpeg($path);
    $exif = new PelExif;
    $tiff = new PelTiff;
    $exif->setTiff($tiff);
    $jpeg->setExif($exif);

    $ifd0 = new PelIfd(PelIfd::IFD0);
    $tiff->setIfd($ifd0);

    $ifd0->addEntry(new PelEntryAscii(PelTag::MAKE, 'TestPhone-Maker'));
    $ifd0->addEntry(new PelEntryAscii(PelTag::MODEL, 'TestModel-X'));
    $ifd0->addEntry(new PelEntryAscii(PelTag::SOFTWARE, 'CrisisCam 1.2'));

    $exifSubIfd = new PelIfd(PelIfd::EXIF);
    $ifd0->addSubIfd($exifSubIfd);
    $exifSubIfd->addEntry(new PelEntryAscii(PelTag::DATE_TIME_ORIGINAL, '2026:04:25 14:30:00'));

    $gpsIfd = new PelIfd(PelIfd::GPS);
    $ifd0->addSubIfd($gpsIfd);
    $gpsIfd->addEntry(new PelEntryAscii(PelTag::GPS_LATITUDE_REF, 'N'));
    $gpsIfd->addEntry(new PelEntryRational(
        PelTag::GPS_LATITUDE,
        [5, 1],
        [33, 1],
        [4500, 100],
    ));
    $gpsIfd->addEntry(new PelEntryAscii(PelTag::GPS_LONGITUDE_REF, 'W'));
    $gpsIfd->addEntry(new PelEntryRational(
        PelTag::GPS_LONGITUDE,
        [0, 1],
        [12, 1],
        [3000, 100],
    ));

    $jpeg->saveFile($path);
}

/**
 * Read the IFD0 + EXIF sub-IFD entries back as [tag => entry] for
 * direct equality assertions. Returns an empty array if the file has
 * no EXIF block.
 *
 * @return array<int, mixed>
 */
function readExifEntries(string $path): array
{
    $jpeg = new PelJpeg(new PelDataWindow(file_get_contents($path)));
    $exif = $jpeg->getExif();
    if ($exif === null) {
        return [];
    }

    $ifd0 = $exif->getTiff()?->getIfd();
    if ($ifd0 === null) {
        return [];
    }

    $entries = [];
    foreach ($ifd0->getEntries() as $tag => $entry) {
        $entries[$tag] = $entry;
    }

    $exifSubIfd = $ifd0->getSubIfd(PelIfd::EXIF);
    if ($exifSubIfd !== null) {
        foreach ($exifSubIfd->getEntries() as $tag => $entry) {
            $entries[$tag] = $entry;
        }
    }

    return $entries;
}

/**
 * @return array<int, mixed>
 */
function sanitizeAndReadExif(string $path): array
{
    app(PhotoExifService::class)->sanitize($path);

    return readExifEntries($path);
}
