<?php

use App\Jobs\ProcessPhotoUpload;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->storagePath = storage_path('app/public/photos');
    if (! is_dir($this->storagePath)) {
        mkdir($this->storagePath, 0755, true);
    }
});

afterEach(function () {
    if (isset($this->testImagePath) && file_exists($this->testImagePath)) {
        unlink($this->testImagePath);
    }
});

it('computes photo_phash when processing a JPEG image', function () {
    $image = imagecreatetruecolor(100, 100);
    imagefilledrectangle($image, 0, 0, 99, 99, imagecolorallocate($image, 255, 0, 0));
    $this->testImagePath = $this->storagePath.'/test-phash.jpg';
    imagejpeg($image, $this->testImagePath);
    imagedestroy($image);

    $report = DamageReport::factory()->create([
        'photo_url' => 'photos/test-phash.jpg',
        'photo_phash' => null,
    ]);

    (new ProcessPhotoUpload($report))->handle();

    $report->refresh();
    expect($report->photo_hash)->not->toBeNull()
        ->and($report->photo_phash)->not->toBeNull()
        ->and($report->photo_phash)->toHaveLength(16)
        ->and(ctype_xdigit($report->photo_phash))->toBeTrue();
});

it('computes photo_phash when processing a PNG image', function () {
    $image = imagecreatetruecolor(80, 80);
    imagefilledrectangle($image, 0, 0, 79, 79, imagecolorallocate($image, 0, 0, 255));
    $this->testImagePath = $this->storagePath.'/test-phash.png';
    imagepng($image, $this->testImagePath);
    imagedestroy($image);

    $report = DamageReport::factory()->create([
        'photo_url' => 'photos/test-phash.png',
        'photo_phash' => null,
    ]);

    (new ProcessPhotoUpload($report))->handle();

    $report->refresh();
    expect($report->photo_phash)->not->toBeNull()
        ->and($report->photo_phash)->toHaveLength(16);
});

it('produces similar phash for visually similar images', function () {
    // Create two nearly identical red images
    $image1 = imagecreatetruecolor(100, 100);
    imagefilledrectangle($image1, 0, 0, 99, 99, imagecolorallocate($image1, 200, 50, 50));
    $path1 = $this->storagePath.'/test-similar1.jpg';
    imagejpeg($image1, $path1);
    imagedestroy($image1);

    $image2 = imagecreatetruecolor(100, 100);
    imagefilledrectangle($image2, 0, 0, 99, 99, imagecolorallocate($image2, 210, 55, 55));
    $path2 = $this->storagePath.'/test-similar2.jpg';
    imagejpeg($image2, $path2);
    imagedestroy($image2);

    $report1 = DamageReport::factory()->create([
        'photo_url' => 'photos/test-similar1.jpg',
        'photo_phash' => null,
    ]);
    $report2 = DamageReport::factory()->create([
        'photo_url' => 'photos/test-similar2.jpg',
        'photo_phash' => null,
    ]);

    (new ProcessPhotoUpload($report1))->handle();
    (new ProcessPhotoUpload($report2))->handle();

    $report1->refresh();
    $report2->refresh();

    // Compute hamming distance — visually similar images should be close
    $distance = 0;
    for ($i = 0; $i < 16; $i++) {
        $xor = intval($report1->photo_phash[$i], 16) ^ intval($report2->photo_phash[$i], 16);
        $distance += substr_count(decbin($xor), '1');
    }

    expect($distance)->toBeLessThanOrEqual(5);

    // Cleanup
    @unlink($path1);
    @unlink($path2);
    $this->testImagePath = null;
});

it('skips processing when photo_url does not start with photos/', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'https://example.com/external.jpg',
        'photo_phash' => null,
    ]);

    (new ProcessPhotoUpload($report))->handle();

    $report->refresh();
    expect($report->photo_phash)->toBeNull();
});

it('skips processing when file does not exist', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'photos/nonexistent.jpg',
        'photo_phash' => null,
    ]);

    (new ProcessPhotoUpload($report))->handle();

    $report->refresh();
    expect($report->photo_phash)->toBeNull();
});
