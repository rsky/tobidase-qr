<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * カラーテーブルクラス
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

use Imagick;
use InvalidArgumentException;
use OutOfRangeException;
use UnexpectedValueException;

require_once __DIR__ . '/../utility.php';

/**
 * カラーテーブルクラス
 */
class ColorTable
{
    /**
     * カラーコードの最小値
     *
     * @const int
     */
    const COLORCODE_MIN = 1;

    /**
     * カラーコードの最大値
     *
     * @const int
     */
    const COLORCODE_MAX = 159;

    /**
     * L*a*b*近似色判定で使うL*成分の重み係数
     *
     * @var float
     */
    private $blightnessWeight = 1.0;

    /**
     * 内部テーブルが連想配列かどうか
     *
     * @const bool
     */
    private $associative = false;

    /**
     * マイデザインのカラーパレットとRGB値の対応表
     *
     * この表の索引+1が実際のカラーコード
     *
     * @var array
     */
    private $rgbColorTable = [
        [255, 239, 255], [255, 154, 173], [239,  85, 156], [255, 101, 173],
        [255,   0,  99], [189,  69, 115], [206,   0,  82], [156,   0,  49],
        [ 82,  32,  49], [255, 186, 206], [255, 117, 115], [222,  48,  16],
        [255,  85,  66], [255,   0,   0], [206, 101,  99], [189,  69,  66],
        [189,   0,   0], [140,  32,  33], [222, 207, 189], [255, 207,  99],
        [222, 101,  33], [255, 170,  33], [255, 101,   0], [189, 138,  82],
        [222,  69,   0], [189,  69,   0], [ 99,  48,  16], [255, 239, 222],
        [255, 223, 206], [255, 207, 173], [255, 186, 140], [255, 170, 140],
        [222, 138,  99], [189, 101,  66], [156,  85,  49], [140,  69,  33],
        [255, 207, 255], [239, 138, 255], [206, 101, 222], [189, 138, 206],
        [206,   0, 255], [156, 101, 156], [140,   0, 173], [ 82,   0, 115],
        [ 49,   0,  66], [255, 186, 255], [255, 154, 255], [222,  32, 189],
        [255,  85, 239], [255,   0, 206], [140,  85, 115], [189,   0, 156],
        [140,   0,  99], [ 82,   0,  66], [222, 186, 156], [206, 170, 115],
        [115,  69,  49], [173, 117,  66], [156,  48,   0], [115,  48,  33],
        [ 82,  32,   0], [ 49,  16,   0], [ 33,  16,   0], [255, 255, 206],
        [255, 255, 115], [222, 223,  33], [255, 255,   0], [255, 223,   0],
        [206, 170,   0], [156, 154,   0], [140, 117,   0], [ 82,  85,   0],
        [222, 186, 255], [189, 154, 239], [ 99,  48, 206], [156,  85, 255],
        [ 99,   0, 255], [ 82,  69, 140], [ 66,   0, 156], [ 33,   0,  99],
        [ 33,  16,  49], [189, 186, 255], [140, 154, 255], [ 49,  48, 173],
        [ 49,  85, 239], [  0,   0, 255], [ 49,  48, 140], [  0,   0, 173],
        [ 16,  16,  99], [  0,   0,  33], [156, 239, 189], [ 99, 207, 115],
        [ 33, 101,  16], [ 66, 170,  49], [  0, 138,  49], [ 82, 117,  82],
        [ 33,  85,   0], [ 16,  48,  33], [  0,  32,  16], [222, 255, 189],
        [206, 255, 140], [140, 170,  82], [173, 223, 140], [140, 255,   0],
        [173, 186, 156], [ 99, 186,   0], [ 82, 154,   0], [ 49, 101,   0],
        [189, 223, 255], [115, 207, 255], [ 49,  85, 156], [ 99, 154, 255],
        [ 16, 117, 255], [ 66, 117, 173], [ 33,  69, 115], [  0,  32, 115],
        [  0,  16,  66], [173, 255, 255], [ 82, 255, 255], [  0, 138, 189],
        [ 82, 186, 206], [  0, 207, 255], [ 66, 154, 173], [  0, 101, 140],
        [  0,  69,  82], [  0,  32,  49], [206, 255, 239], [173, 239, 222],
        [ 49, 207, 173], [ 82, 239, 189], [  0, 255, 206], [115, 170, 173],
        [  0, 170, 156], [  0, 138, 115], [  0,  69,  49], [173, 255, 173],
        [115, 255, 115], [ 99, 223,  66], [  0, 255,   0], [ 33, 223,  33],
        [ 82, 186,  82], [  0, 186,   0], [  0, 138,   0], [ 33,  69,  33],
        [255, 255, 255], [239, 239, 239], [222, 223, 222], [206, 207, 206],
        [189, 186, 189], [173, 170, 173], [156, 154, 156], [140, 138, 140],
        [115, 117, 115], [ 99, 101,  99], [ 82,  85,  82], [ 66,  69,  66],
        [ 49,  48,  49], [ 33,  32,  33], [  0,   0,   0],
    ];

    /**
     * RGBカラーテーブルをsRGB(D65)からL*a*b*(D50)に変換したもの
     *
     * @var array
     */
    private $labColorTable = [
        [  98.2339,   3.40011,  -2.55151], [  86.8278,   19.7906,   1.84048],
        [  75.1124,   39.8938,  -10.1303], [  78.9002,   37.2292,  -9.68293],
        [  56.6852,   86.1356,  -16.0115], [  68.4766,   35.3983,  -5.84976],
        [  51.7493,   80.3406,  -15.8668], [  45.3252,   72.2944,   -6.4813],
        [  48.6552,   24.9969,  -2.79228], [  91.4315,    13.046, -0.580287],
        [  80.6693,   27.7799,   12.1519], [  64.3582,   44.6249,   48.5424],
        [  74.6442,   36.5047,   25.4252], [  54.2897,   80.8141,   69.8901],
        [  75.2777,   23.8092,   10.4774], [  67.6188,    31.511,   15.1685],
        [   47.611,   73.1354,   63.2493], [  54.0224,   39.3191,   17.8692],
        [  92.6002,   1.38791,   5.39513], [  92.9257,   1.14695,   34.7233],
        [  75.1697,   21.8619,   45.6841], [  87.4007,    6.0915,    59.906],
        [  76.4626,   25.6001,   79.6348], [  80.3123,   6.35251,   24.5095],
        [  68.5828,   32.7404,   73.8965], [  66.4348,   25.9096,   71.3365],
        [  54.5667,   14.9844,   34.4239], [  97.9203,   1.47576,   4.71281],
        [  95.9299,    4.0792,   5.86597], [  93.6916,   5.80828,   11.5809],
        [  90.7196,   8.94928,   17.4141], [  88.6238,   13.1859,    14.513],
        [  82.1198,   14.0325,   20.0667], [  73.8391,   17.8486,    23.569],
        [  68.5133,   15.5247,   25.7403], [  63.6703,   17.3348,   30.3686],
        [   94.528,   10.7279,  -7.94681], [  84.8486,   27.2053,  -22.3836],
        [  77.0681,   32.5603,  -26.2547], [  81.9379,   15.6286,  -14.8774],
        [  56.1221,   89.5681,   -67.242], [  73.3594,   18.3451,  -13.3328],
        [  47.4004,   78.7292,  -59.0389], [  37.6403,   67.0827,  -54.3516],
        [  29.0153,    56.167,  -44.4343], [   91.954,   15.9773,  -11.7267],
        [  87.7781,   24.7871,  -17.9156], [  64.0458,   65.5741,  -37.0762],
        [  77.1767,   47.4312,  -30.0772], [  59.1082,   91.3289,   -49.429],
        [  68.6389,   17.8565,  -6.04951], [  52.0603,    82.838,  -45.7166],
        [  45.0367,   73.8004,  -35.0718], [  35.4457,    62.546,  -33.7371],
        [  89.5656,   4.40696,   10.5594], [   86.256,   2.87854,   19.4501],
        [  62.1659,   12.2364,    16.697], [   75.796,   8.57039,   25.9723],
        [  59.0719,   29.3964,   65.6267], [  56.3995,   22.0273,   20.5435],
        [  47.1718,   17.9223,   54.4545], [  35.5444,   18.8119,      44.9],
        [  32.6512,   8.91153,   41.4236], [  99.5479,   -2.8485,   10.9859],
        [  98.6988,  -8.35269,   36.4292], [  92.9477,   -12.873,   65.3742],
        [  97.6074,   -15.748,   93.3914], [  93.8584,  -9.07461,   90.8229],
        [  84.8774,  -5.82151,   83.6134], [  80.1269,  -12.8164,   79.0632],
        [  72.9631,  -5.61838,   73.6962], [  62.5509,  -12.0362,   64.4795],
        [   90.653,   10.8194,  -13.8211], [  84.7234,   13.0408,  -19.0587],
        [  59.0009,   35.8686,  -50.5937], [  71.9393,   32.6265,  -42.5454],
        [  45.2187,   79.5157,  -85.5252], [  61.5511,   12.9684,  -26.3348],
        [  36.8671,   68.2829,  -71.0914], [  27.3866,   56.9237,  -64.5371],
        [  35.0873,    20.877,  -23.9724], [  89.3194,   5.28525,  -15.9713],
        [  82.6849,   5.03885,  -26.1718], [  53.9014,   18.9402,  -49.3343],
        [  64.8455,   6.91572,  -50.3332], [  29.5658,   68.2867,  -112.033],
        [   53.056,   14.5855,  -39.7355], [  24.0382,   60.0028,  -98.4423],
        [  34.4927,   22.6782,  -53.1395], [  7.08514,   34.3797,  -56.6037],
        [  94.0103,  -17.6981,   6.99156], [   86.784,  -29.2672,   19.2847],
        [  63.0564,  -36.1862,   42.5247], [  78.8778,  -36.9563,   37.7387],
        [  69.4424,  -55.5645,   24.7788], [  70.8687,  -13.4266,   10.4957],
        [  58.7665,   -33.989,   59.7156], [  46.5452,  -21.0953,   5.18521],
        [  36.6998,  -32.0316,   8.80281], [   98.254,  -9.17971,   13.3741],
        [  97.2241,  -15.1087,   25.9247], [  82.8908,  -13.5702,   27.4377],
        [  92.1832,  -14.5547,   18.8256], [  93.4094,  -38.9851,   88.1233],
        [  87.5306,  -4.65904,    7.3455], [  82.3409,  -35.9884,   79.1412],
        [  76.3446,  -33.7828,   74.3164], [   63.893,  -31.4587,   64.1399],
        [  93.9886,  -3.66573,  -9.08059], [  89.0596,  -13.9086,  -16.7462],
        [  63.2435,  -2.88249,  -29.3861], [  80.7355,  -3.57713,  -29.3449],
        [  69.7183,  -13.7887,  -46.7631], [  71.3943,  -8.00258,  -22.1681],
        [  57.0863,  -7.13217,  -24.2791], [  40.7835, -0.187942,  -50.0786],
        [  29.5808,   2.96654,  -44.1961], [  97.1649,  -13.2971,  -4.52569],
        [  93.8426,  -30.9512,  -9.85012], [   71.751,  -34.0785,  -26.7191],
        [  84.1439,  -20.3597,  -12.2567], [  84.0993,  -42.0019,  -24.7788],
        [  77.9728,  -19.3877,  -12.3365], [   63.113,  -30.4324,  -24.6084],
        [  53.3279,  -29.7912,   -15.848], [  38.1301,  -19.1125,  -19.8199],
        [  98.1712,  -8.69547,  0.759293], [  94.9766,   -12.202,  0.032018],
        [  85.2929,  -36.6652,   -1.7387], [  91.1614,  -33.3321,   2.47184],
        [  90.1311,  -55.5796,  -3.97124], [  82.8811,  -11.4332,  -4.80106],
        [  76.9818,  -46.0739,  -9.11029], [  70.5461,  -44.7501,  -4.49544],
        [  52.4682,  -37.6257,    1.4509], [  96.3674,  -18.8587,   14.7908],
        [  93.6489,  -35.0496,   28.6773], [  88.4002,  -37.0742,   41.3111],
        [  87.8194,  -79.2749,   80.9928], [  85.1785,  -59.5981,   54.2445],
        [  82.5908,  -32.2632,     26.46], [  77.4551,  -71.3608,   72.9072],
        [  68.6041,  -64.6024,   66.0023], [  55.1363,  -21.3114,   17.3303],
        [      100,         0,         0], [  97.5213,         0,         0],
        [  94.8823, -0.227217,  0.171611], [  92.1608, -0.238804,  0.180369],
        [  88.5783,  0.764645, -0.576409], [  85.5038,   0.81154, -0.611675],
        [  82.1709,  0.578668, -0.436341], [  78.6594,  0.622299, -0.469176],
        [  73.3246, -0.701194,  0.530231], [  69.0298, -0.774041,  0.585469],
        [  64.1611,  -1.30857,  0.991272], [  58.7196,  -1.50746,   1.14285],
        [  50.6105,  0.627948, -0.473159], [  42.2465,  0.820354, -0.617552],
        [        0,         0,         0],
    ];

    /**
     * コンストラクタ
     *
     * @param array $rgbTable
     */
    public function __construct(array $rgbTable = null)
    {
        if ($rgbTable) {
            $this->associative = true;
            $this->rgbColorTable = $rgbTable;
            $this->labColorTable = [];

            foreach ($rgbTable as $code => $rgb) {
                list($r, $g, $b) = $rgb;
                $this->labColorTable[$code] = rgbToLab($r, $g, $b);
            }
        }
    }

    /**
     * RGBのカラーコード表を返す
     *
     * @param void
     *
     * @return array
     */
    public function getRgbColorTable()
    {
        $table = [];

        foreach ($this->rgbColorTable as $index => $rgb) {
            $code = ($this->associative) ? $index : $index + 1;
            $table[$code] = $rgb;
        }

        return $table;
    }

    /**
     * L*a*b*のカラーコード表を返す
     *
     * @param void
     *
     * @return array
     */
    public function getLabColorTable()
    {
        $table = [];

        foreach ($this->labColorTable as $index => $lab) {
            $code = ($this->associative) ? $index : $index + 1;
            $table[$code] = $lab;
        }

        return $table;
    }

    /**
     * カラーコードを検証する
     *
     * @param int $code
     *
     * @return int
     *
     * @throws InvalidArgumentException, OutOfRangeException, DomainException
     */
    private function checkColorCode($code)
    {
        if (!is_integer($code) && !preg_match('/^[1-9][0-9]*$', strval($code))) {
            throw new InvalidArgumentException('Color code must be an integer');
        }

        if ($code < self::COLORCODE_MIN || self::COLORCODE_MAX < $code) {
            throw new OutOfRangeException(sprintf(
                'The given color code (%d) is out of range [%d..%d]',
                $code, self::COLORCODE_MIN, self::COLORCODE_MAX
            ));
        }

        $index = ($this->associative) ? $code : $code - 1;
        if (!isset($this->rgbColorTable[$index])) {
            throw new DomainException("Color code {$code} is not available");
        }

        return $index;
    }

    /**
     * カラーコードに対応するRGB値の配列を返す
     *
     * @param int $code
     *
     * @return int[] ($r, $g, $b)
     *
     * @see self::checkColorCode()
     */
    public function getRgbColor($code)
    {
        $index = $this->checkColorCode($code);

        return $this->rgbColorTable[$index];
    }

    /**
     * カラーコードに対応するL*a*b*値の配列を返す
     *
     * @param int $code
     *
     * @return float[] ($L, $a, $b)
     *
     * @see self::checkColorCode()
     */
    public function getLabColor($code)
    {
        $index = $this->checkColorCode($code);

        return $this->labColorTable[$index];
    }

    /**
     * L*a*b*近似色判定でのL*成分の重みをセットする
     *
     * @param float $weight
     *
     * @return void
     */
    public function setBlightnessWeight($weight)
    {
        $this->blightnessWeight = $weight;
    }

    /**
     * 与えられたRGB値に最も近いカラーコードを返す
     *
     * @param int $r
     * @param int $g
     * @param int $b
     *
     * @return int
     *
     * @throws UnexpectedValueException
     */
    public function nearestColorCodeByRgb($r, $g, $b)
    {
        $distance = 16777216.0;
        $index = -1;

        foreach ($this->rgbColorTable as $i => $rgb) {
            list($tr, $tg, $tb) = $rgb;
            $d  = ($tr - $r) * ($tr - $r)
                + ($tg - $g) * ($tg - $g)
                + ($tb - $b) * ($tb - $b);
            if ($d < $distance) {
                $distance = $d;
                $index = $i;
            }
        }

        if ($index === -1) {
            throw new UnexpectedValueException(
                'Unexpected RGB value was given'
            );
        }

        return ($this->associative) ? $index : $index + 1;
    }

    /**
     * 与えられたL*a*b*値に最も近いカラーコードを返す
     *
     * @param float $L
     * @param float $a
     * @param float $b
     *
     * @return int
     *
     * @throws UnexpectedValueException
     */
    public function nearestColorCodeByLab($L, $a, $b)
    {
        $distance = 16777216.0;
        $index = -1;

        foreach ($this->labColorTable as $i => $lab) {
            list($tL, $ta, $tb) = $lab;
            $d  = ($tL - $L) * ($tL - $L) * $this->blightnessWeight
                + ($ta - $a) * ($ta - $a)
                + ($tb - $b) * ($tb - $b);
            if ($d < $distance) {
                $distance = $d;
                $index = $i;
            }
        }

        if ($index === -1) {
            throw new UnexpectedValueException(
                'Unexpected L*a*b* value was given'
            );
        }

        return ($this->associative) ? $index : $index + 1;
    }

    /**
     * 与えられたRGB値に最も近いカラーコードを返す
     * 近似色判定はL*a*b*の距離で行う
     *
     * @param int $r
     * @param int $g
     * @param int $b
     *
     * @return int
     *
     * @throws UnexpectedValueException
     */
    public function nearestColorCodeByRgbUsingLabDistance($r, $g, $b)
    {
        list($L, $A, $B) = rgbToLab($r, $g, $b);

        try {
            return $this->nearestColorCodeByLab($L, $A, $B);
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedValueException(
                'Unexpected RGB value was given', $e->getCode(), $e
            );
        }
    }

    /**
     * Imagickオブジェクトから近似色のヒストグラムを作成する
     *
     * @param Imagick $imagick 色空間は COLORSPACE_RGB or COLORSPACE_SRGB
     * @param bool $useLabDistance
     *
     * @return array
     */
    public function createHistgram(Imagick $imagick, $useLabDistance = false)
    {
        $histgram = array_fill(self::COLORCODE_MIN, self::COLORCODE_MAX, 0);

        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();

        $method = ($useLabDistance)
            ? 'nearestColorCodeByRgbUsingLabDistance'
            : 'nearestColorCodeByRgb';

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $px = $imagick->getImagePixelColor($x, $y)->getColor();
                $code = $this->$method($px['r'], $px['g'], $px['b']);
                $histgram[$code]++;
            }
        }

        return array_filter($histgram);
    }
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
