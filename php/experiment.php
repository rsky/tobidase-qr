<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 実装検証スクリプト
 *
 * 「とびだせ どうぶつの森」は任天堂株式会社の登録商標です
 *
 * Copyright (c) 2012 Ryusuke SEKIYAMA <rsky0711@gmail.com>,
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package     TobidaseQR
 * @copyright   2012 Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php  MIT License
 */

namespace TobidaseQR;

use Imagick, ImagickException;

require __DIR__ . '/TobidaseQR/ColorTable.php';
require __DIR__ . '/TobidaseQR/ColorRange.php';
require __DIR__ . '/TobidaseQR/ColorRangeElement.php';
require __DIR__ . '/TobidaseQR/ColorReducer.php';

const WIDTH = 32;
const HEIGHT = 32;
const MAGNIFY = 4;

$iccDir = dirname(__DIR__) . '/icc';

$im = new Imagick($_SERVER['argv'][1]);
$cs = $im->getImageColorspace();
if ($cs !== Imagick::COLORSPACE_RGB && $cs !== Imagick::COLORSPACE_SRGB) {
    $icc = null;
    try {
        $icc = $im->getImageProfile('icc');
    } catch (ImagickException $e) {
        if ($cs === Imagick::COLORSPACE_CMYK) {
            $icc = file_get_contents($iccDir . '/JapanColor2001Coated.icc');
            $im->setImageProfile('icc', $icc);
        }
    }
    if ($icc) {
        $icc = file_get_contents($iccDir . '/sRGB_v4_ICC_preference.icc');
        $im->profileImage('icc', $icc);
    }
    $im->setImageColorspace(Imagick::COLORSPACE_SRGB);
}

$width = $im->getImageWidth();
$height = $im->getImageHeight();

if ($width > $height) {
    $im->cropImage($height, $height, floor(($width - $height) / 2), 0);
} elseif ($width < $height) {
    $im->cropImage($width, $width, 0, floor(($height - $width) / 2));
}

$im->resizeImage(WIDTH, HEIGHT, Imagick::FILTER_LANCZOS, 1.0, true);

$table = new ColorTable;
$reducer = new ColorReducer($table->getRgbColorTable());
$histgram = $table->createHistgram($im);
$reducedTable = $reducer->reduceColor($histgram);

$gd = imagecreate(WIDTH * MAGNIFY, HEIGHT * MAGNIFY);
$gdPalatte = [];
foreach ($reducedTable->getRgbColorTable() as $i => $rgb) {
    list($r, $g, $b) = $rgb;
    $gdPalatte[$i] = imagecolorallocate($gd, $r, $g, $b);
}
for ($y = 0; $y < HEIGHT; $y++) {
    for ($x = 0; $x < WIDTH; $x++) {
        $px = $im->getImagePixelColor($x, $y)->getColor();
        $dx = $x * MAGNIFY;
        $dy = $y * MAGNIFY;
        $c = $gdPalatte[$reducedTable->nearestColorCodeByRgbUsingLabDistance(
            $px['r'], $px['g'], $px['b']
        )];
        imagefilledrectangle($gd, $dx, $dy, $dx + MAGNIFY, $dy + MAGNIFY, $c);
    }
}

imagepng($gd, '_ex.png');

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
// vim: set filetype=php fileencoding=utf-8 expandtab tabstop=4 shiftwidth=4:
