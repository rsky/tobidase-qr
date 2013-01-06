<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * カラーコードクラス
 *
 * とびだせ どうぶつの森は任天堂(株)の登録商標です
 * QRコードは(株)デンソーウェーブの登録商標です
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
 */

namespace TobidaseQR\Color;

/**
 * カラーコードクラス
 */
class Code
{
    /**
     * パレットの色
     */
    const P_00 =   0;
    const P_01 =   1;
    const P_02 =   2;
    const P_03 =   3;
    const P_04 =   4;
    const P_05 =   5;
    const P_06 =   6;
    const P_07 =   7;
    const P_08 =   8;
    const P_10 =   9;
    const P_11 =  10;
    const P_12 =  11;
    const P_13 =  12;
    const P_14 =  13;
    const P_15 =  14;
    const P_16 =  15;
    const P_17 =  16;
    const P_18 =  17;
    const P_20 =  18;
    const P_21 =  19;
    const P_22 =  20;
    const P_23 =  21;
    const P_24 =  22;
    const P_25 =  23;
    const P_26 =  24;
    const P_27 =  25;
    const P_28 =  26;
    const P_30 =  27;
    const P_31 =  28;
    const P_32 =  29;
    const P_33 =  30;
    const P_34 =  31;
    const P_35 =  32;
    const P_36 =  33;
    const P_37 =  34;
    const P_38 =  35;
    const P_40 =  36;
    const P_41 =  37;
    const P_42 =  38;
    const P_43 =  39;
    const P_44 =  40;
    const P_45 =  41;
    const P_46 =  42;
    const P_47 =  43;
    const P_48 =  44;
    const P_50 =  45;
    const P_51 =  46;
    const P_52 =  47;
    const P_53 =  48;
    const P_54 =  49;
    const P_55 =  50;
    const P_56 =  51;
    const P_57 =  52;
    const P_58 =  53;
    const P_60 =  54;
    const P_61 =  55;
    const P_62 =  56;
    const P_63 =  57;
    const P_64 =  58;
    const P_65 =  59;
    const P_66 =  60;
    const P_67 =  61;
    const P_68 =  62;
    const P_70 =  63;
    const P_71 =  64;
    const P_72 =  65;
    const P_73 =  66;
    const P_74 =  67;
    const P_75 =  68;
    const P_76 =  69;
    const P_77 =  70;
    const P_78 =  71;
    const P_80 =  72;
    const P_81 =  73;
    const P_82 =  74;
    const P_83 =  75;
    const P_84 =  76;
    const P_85 =  77;
    const P_86 =  78;
    const P_87 =  79;
    const P_88 =  80;
    const P_90 =  81;
    const P_91 =  82;
    const P_92 =  83;
    const P_93 =  84;
    const P_94 =  85;
    const P_95 =  86;
    const P_96 =  87;
    const P_97 =  88;
    const P_98 =  89;
    const P_A0 =  90;
    const P_A1 =  91;
    const P_A2 =  92;
    const P_A3 =  93;
    const P_A4 =  94;
    const P_A5 =  95;
    const P_A6 =  96;
    const P_A7 =  97;
    const P_A8 =  98;
    const P_B0 =  99;
    const P_B1 = 100;
    const P_B2 = 101;
    const P_B3 = 102;
    const P_B4 = 103;
    const P_B5 = 104;
    const P_B6 = 105;
    const P_B7 = 106;
    const P_B8 = 107;
    const P_C0 = 108;
    const P_C1 = 109;
    const P_C2 = 110;
    const P_C3 = 111;
    const P_C4 = 112;
    const P_C5 = 113;
    const P_C6 = 114;
    const P_C7 = 115;
    const P_C8 = 116;
    const P_D0 = 117;
    const P_D1 = 118;
    const P_D2 = 119;
    const P_D3 = 120;
    const P_D4 = 121;
    const P_D5 = 122;
    const P_D6 = 123;
    const P_D7 = 124;
    const P_D8 = 125;
    const P_E0 = 126;
    const P_E1 = 127;
    const P_E2 = 128;
    const P_E3 = 129;
    const P_E4 = 130;
    const P_E5 = 131;
    const P_E6 = 132;
    const P_E7 = 133;
    const P_E8 = 134;
    const P_F0 = 135;
    const P_F1 = 136;
    const P_F2 = 137;
    const P_F3 = 138;
    const P_F4 = 139;
    const P_F5 = 140;
    const P_F6 = 141;
    const P_F7 = 142;
    const P_F8 = 143;
    const P_0F = 144;
    const P_1F = 145;
    const P_2F = 146;
    const P_3F = 147;
    const P_4F = 148;
    const P_5F = 149;
    const P_6F = 150;
    const P_7F = 151;
    const P_8F = 152;
    const P_9F = 153;
    const P_AF = 154;
    const P_BF = 155;
    const P_CF = 156;
    const P_DF = 157;
    const P_EF = 158;

    /**
     * HTML 4.01で定義されている色
     */
    const WHITE   = 144;
    const SILVER  = 148;
    const GRAY    = 151;
    const BLACK   = 158;
    const RED     =  13;
    const MAROON  =  17;
    const YELLOW  =  66;
    const OLIVE   =  70;
    const LIME    = 138;
    const GREEN   = 142;
    const AQUA    = 121;
    const TEAL    = 133;
    const BLUE    =  85;
    const NAVY    = 115;
    const FUCHSIA =  40;
    const PURPLE  =  52;

    /**
     * 不正なカラーコードを示す値
     */
    const INVALID = -1;

    /**
     * カラーコードを0から始まる連番の内部表記から
     * QRコード用の表記に変換する
     *
     * @param int $code
     *
     * @return int
     */
    public static function encode($code)
    {
        if ($code < 144) {
            // カラー9x16色
            return ((int)($code / 9) << 4) | ($code % 9);
        } else {
            // モノクロ15色
            return (($code - 144) << 4) | 0xf;
        }
    }

    /**
     * カラーコードをQRコード上の表記から
     * 0から始まる連番の内部表記に変換する
     *
     * @param int $code
     *
     * @return int 不正な値のときは-1を返す
     */
    public static function decode($code)
    {
        $upperBits = ($code & 0xf0) >> 4;
        $lowerBits = $code & 0xf;

        if ($lowerBits === 0xf) {
            // モノクロ15色
            if ($upperBits === 0xf) {
                return self::INVALID;
            }
            return 144 + $upperBits;
        } else {
            // カラー9x16色
            if ($lowerBits > 8) {
                return self::INVALID;
            }
            return 9 * $upperBits + $lowerBits;
        }
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
