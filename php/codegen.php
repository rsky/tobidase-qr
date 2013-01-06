<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * カラーテーブルのソースコードジェネレータ
 *
 * 「とびだせ どうぶつの森」は任天堂株式会社の登録商標です
 *
 * Copyright (c) 2012-2013 Ryusuke SEKIYAMA <rsky0711@gmail.com>,
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
 * @copyright   2012-2013 Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * カラーパレット情報は下記URLのものを使わせていただきました
 * @link http://d.hatena.ne.jp/karaage/20121201/1354330494
 * @link http://d.hatena.ne.jp/karaage/20121207/1354897657
 */

namespace TobidaseQR;

if (!function_exists(__NAMESPACE__ . '\\rgbToLab')) {
    require __DIR__ . '/utility.php';
}

$palletR = [
  255, 255, 239, 255, 255, 189, 206, 156, 82,
  255, 255, 222, 255, 255, 206, 189, 189, 140,
  222, 255, 222, 255, 255, 189, 222, 189, 99,
  255, 255, 255, 255, 255, 222, 189, 156, 140,
  255, 239, 206, 189, 206, 156, 140, 82, 49,
  255, 255, 222, 255, 255, 140, 189, 140, 82,
  222, 206, 115, 173, 156, 115, 82, 49, 33,
  255, 255, 222, 255, 255, 206, 156, 140, 82,
  222, 189, 99, 156, 99, 82, 66, 33, 33,
  189, 140, 49, 49, 0, 49, 0, 16, 0,
  156, 99, 33, 66, 0, 82, 33, 16, 0,
  222, 206, 140, 173, 140, 173, 99, 82, 49,
  189, 115, 49, 99, 16, 66, 33, 0, 0,
  173, 82, 0, 82, 0, 66, 0, 0, 0,
  206, 173, 49, 82, 0, 115, 0, 0, 0,
  173, 115, 99, 0, 33, 82, 0, 0, 33,
  255, 239, 222, 206, 189,
  173, 156, 140, 115, 99,
  82, 66, 49, 33, 0
];

$palletG = [
  239, 154, 85, 101, 0, 69, 0, 0, 32,
  186, 117, 48, 85, 0, 101, 69, 0, 32,
  207, 207, 101, 170, 101, 138, 69, 69, 48,
  239, 223, 207, 186, 170, 138, 101, 85, 69,
  207, 138, 101, 138, 0, 101, 0, 0, 0,
  186, 154, 32, 85, 0, 85, 0, 0, 0,
  186, 170, 69, 117, 48, 48, 32, 16, 16,
  255, 255, 223, 255, 223, 170, 154, 117, 85,
  186, 154, 48, 85, 0, 69, 0, 0, 16,
  186, 154, 48, 85, 0, 48, 0, 16, 0,
  239, 207, 101, 170, 138, 117, 85, 48, 32,
  255, 255, 170, 223, 255, 186, 186, 154, 101,
  223, 207, 85, 154, 117, 117, 69, 32, 16,
  255, 255, 138, 186, 207, 154, 101, 69, 32,
  255, 239, 207, 239, 255, 170, 170, 138, 69,
  255, 255, 223, 255, 223, 186, 186, 138, 69,
  255, 239, 223, 207, 186,
  170, 154, 138, 117, 101,
  85, 69, 48, 32, 0
];

$palletB = [
  255, 173, 156, 173, 99, 115, 82, 49, 49,
  206, 115, 16, 66, 0, 99, 66, 0, 33,
  189, 99, 33, 33, 0, 82, 0, 0, 16,
  222, 206, 173, 140, 140, 99, 66, 49, 33,
  255, 255, 222, 206, 255, 156, 173, 115, 66,
  255, 255, 189, 239, 206, 115, 156, 99, 66,
  156, 115, 49, 66, 0, 33, 0, 0, 0,
  206, 115, 33, 0, 0, 0, 0, 0, 0,
  255, 239, 206, 255, 255, 140, 156, 99, 49,
  255, 255, 173, 239, 255, 140, 173, 99, 33,
  189, 115, 16, 49, 49, 82, 0, 33, 16,
  189, 140, 82, 140, 0, 156, 0, 0, 0,
  255, 255, 156, 255, 255, 173, 115, 115, 66,
  255, 255, 189, 206, 255, 173, 140, 82, 49,
  239, 222, 173, 189, 206, 173, 156, 115, 49,
  173, 115, 66, 0, 33, 82, 0, 0, 33,
  255, 239, 222, 206, 189,
  173, 156, 140, 115, 99,
  82, 66, 49, 33, 0
];

$htmlColors = [
    'white'   => [0xff, 0xff, 0xff],
    'silver'  => [0xc0, 0xc0, 0xc0],
    'gray'    => [0x80, 0x80, 0x80],
    'black'   => [0x00, 0x00, 0x00],
    'red'     => [0xff, 0x00, 0x00],
    'maroon'  => [0x80, 0x00, 0x00],
    'yellow'  => [0xff, 0xff, 0x00],
    'olive'   => [0x80, 0x80, 0x00],
    'lime'    => [0x00, 0xff, 0x00],
    'green'   => [0x00, 0x80, 0x00],
    'aqua'    => [0x00, 0xff, 0xff],
    'teal'    => [0x00, 0x80, 0x80],
    'blue'    => [0x00, 0x00, 0xff],
    'navy'    => [0x00, 0x00, 0x80],
    'fuchsia' => [0xff, 0x00, 0xff],
    'purple'  => [0x80, 0x00, 0x80],
];

function makeRgbColorTable(array $palletR, array $palletG, array $palletB)
{
    $table = [];

    foreach ($palletR as $index => $red) {
        $table[] = [$red, $palletG[$index], $palletB[$index]];
    }

    return $table;
}

function makeLabColorTable(array $rgbColorTable)
{
    $table = [];

    foreach ($rgbColorTable as $rgb) {
        list($r, $g, $b) = $rgb;
        $table[] = rgbToLab($r, $g, $b);
    }

    return $table;
}

function makeColorTableCode(array $table, $name, array $defs, array $options = [])
{
    $opener = $defs['opener'];
    $format = $defs['format'];
    $separator = $defs['separator'];
    $spacer = $defs['spacer'];
    $closer = $defs['closer'];

    $defaultOptions = [
        'indent'        => '    ',
        'lineBreak'     => "\n",
        'breakCount'    => 1,
        'initialDepath' => 0,
        'trimChars'     => $separator . $spacer,
    ];

    foreach ($defaultOptions as $key => $value) {
        $$key = (isset($options[$key])) ? $options[$key] : $value;
    }

    $initialIndent = str_repeat($indent, $initialDepath);
    $elementIndent = str_repeat($indent, $initialDepath + 1);

    $code = $initialIndent . $name . $opener;

    foreach ($table as $index => $components) {
        if ($index % $breakCount === 0) {
            $code .= $separator . $lineBreak . $elementIndent;
        } else {
            $code .= $separator . $spacer;
        }
        $code .= vsprintf($format, $components);
    }

    $code = rtrim($code, $trimChars)
        . $lineBreak . $initialIndent . $closer . $lineBreak;

    return $code;
}

function makeRgbColorTablePhpCode(array $table, array $options = [])
{
    return makeColorTableCode(
        $table, '$rgbColorTable', [
            'opener' => ' = [',
            'format' => '[%3d, %3d, %3d]',
            'separator' => ',',
            'spacer' => ' ',
            'closer' => '];',
        ], $options
    );
}

function makeLabColorTablePhpCode(array $table, array $options = [])
{
    return makeColorTableCode(
        $table, '$labColorTable', [
            'opener' => ' = [',
            'format' => '[%9.6g, %9.6g, %9.6g]',
            'separator' => ',',
            'spacer' => ' ',
            'closer' => '];',
        ], $options
    );
}

function makeColorPalettePhpCode()
{
    global $htmlColors;

    $code = '';

    // カラー9x16色
    for ($u = 0; $u < 16; $u++) {
        for ($l = 0; $l < 9; $l++) {
            $code .= sprintf("const P_%X%X = %3d;\n", $u, $l, 9 * $u + $l);
        }
    }

    // モノクロ15色
    for ($k = 0; $k < 15; $k++) {
        $code .= sprintf("const P_%XF = %3d;\n", $k, $k + 144);
    }

    // HTML 4.01カラー
    if (class_exists('TobidaseQR\\Color\\Table')) {
        $table = new Color\Table;
        foreach ($htmlColors as $name => $rgb) {
            list($r, $g, $b) = $rgb;
            $code .= sprintf(
                "const %-7s = %3d;\n",
                strtoupper($name),
                $table->nearestColorCodeByRgb($r, $g, $b)
            );
        }
    }

    return $code;
}

function codegen()
{
    global $palletR, $palletG, $palletB;

    $rgbColorTable = makeRgbColorTable($palletR, $palletG, $palletB);
    $labColorTable = makeLabColorTable($rgbColorTable);

    echo "# PHP\n";
    echo makeRgbColorTablePhpCode($rgbColorTable, ['breakCount' => 4]);
    echo makeLabColorTablePhpCode($labColorTable, ['breakCount' => 2]);
    echo makeColorPalettePhpCode();
}

if (realpath($_SERVER['SCRIPT_NAME']) === __FILE__) {
    chdir(__DIR__);
    set_include_path(__DIR__);
    spl_autoload_register();
    codegen();
}

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
