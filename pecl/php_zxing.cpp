/*
 * Copyright 2013 Ryusuke SEKIYAMA
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package     php-zxing
 * @copyright   2013 Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

#include "php_zxing.h"
#include <ext/standard/info.h>

#include <Magick++.h>
#include "MagickBitmapSource.h"

#include <zxing/BinaryBitMap.h>
#include <zxing/common/GlobalHistogramBinarizer.h>
#include <zxing/qrcode/QRCodeReader.h>
#include <zxing/qrcode/detector/Detector.h>
#include "RawDecoder.h"

#include <zxing/Exception.h>
#include <zxing/FormatException.h>
#include <zxing/NotFoundException.h>

using namespace MagickCore;
using namespace std;
using namespace zxing;
using namespace zxing::qrcode;

typedef enum {
	READ_TEXT,
	READ_RAW,
	READ_MATRIX
} read_mode;

static Ref<String> decode_text(Ref<LuminanceSource> source);
static Ref<RawDecodedResult> decode_raw(Ref<LuminanceSource> source);
static Ref<BitMatrix> detect_matrix(Ref<LuminanceSource> source);
static int do_decode(Ref<LuminanceSource> source, read_mode mode, zval *result TSRMLS_DC);

BEGIN_EXTERN_C()

static PHP_MINIT_FUNCTION(zxing);
static PHP_MINFO_FUNCTION(zxing);
static PHP_FUNCTION(zx_read_qrcode);

ZEND_BEGIN_ARG_INFO_EX(arginfo_zx_read_qrcode, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 1)
	ZEND_ARG_INFO(0, filename)
	ZEND_ARG_INFO(0, mode)
ZEND_END_ARG_INFO()

static zend_function_entry zxing_functions[] = {
	PHP_FE(zx_read_qrcode, arginfo_zx_read_qrcode)
	{ NULL, NULL, NULL, 0, 0 }
};

static zend_module_dep zxing_deps[] = {
#ifdef HAVE_PHP_ZXING_GD
	ZEND_MOD_REQUIRED("gd")
#endif
#ifdef HAVE_PHP_ZXING_MAGICK
	ZEND_MOD_OPTIONAL("imagick")
	ZEND_MOD_OPTIONAL("magickwand")
#endif
	{ NULL, NULL, NULL, 0 }
};

zend_module_entry zxing_module_entry = {
	STANDARD_MODULE_HEADER_EX,
	NULL,
	zxing_deps,
	"zxing",
	zxing_functions,
	PHP_MINIT(zxing),
	NULL,
	NULL,
	NULL,
	PHP_MINFO(zxing),
	"0.0.1-dev",
	STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_ZXING
ZEND_GET_MODULE(zxing)
#endif

static PHP_MINIT_FUNCTION(zxing)
{
	if (IsMagickInstantiated() == MagickFalse) {
		MagickCoreGenesis(NULL, MagickFalse);
	}

	REGISTER_LONG_CONSTANT("ZX_READ_TEXT",   READ_TEXT,   CONST_PERSISTENT | CONST_CS);
	REGISTER_LONG_CONSTANT("ZX_READ_RAW",    READ_RAW,    CONST_PERSISTENT | CONST_CS);
	REGISTER_LONG_CONSTANT("ZX_READ_MATRIX", READ_MATRIX, CONST_PERSISTENT | CONST_CS);

	return SUCCESS;
}

static PHP_MINFO_FUNCTION(zxing)
{
	php_info_print_table_start();
	php_info_print_table_row(2, "ZXing QR Code reader support", "enabled");
	php_info_print_table_end();
}

static PHP_FUNCTION(zx_read_qrcode)
{
	const char *filename = 0;
	int filename_len = 0;
	long mode = 0L;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|l",
		&filename, &filename_len, &mode) == FAILURE) {
		return;
	}

	try {
		Magick::Image image(filename);
		Ref<LuminanceSource> source(new MagickBitmapSource(image));

		if (do_decode(source, (read_mode)mode, return_value TSRMLS_CC) == FAILURE) {
			RETURN_FALSE;
		}
	} catch (Magick::Exception& e) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "unable to read '%s': %s", filename, e.what());
		RETURN_FALSE;
	} catch (std::exception& e) {
		php_error_docref(NULL TSRMLS_CC, E_RECOVERABLE_ERROR, "unexpected error: %s", e.what());
		RETURN_FALSE;
	} catch (...) {
		php_error_docref(NULL TSRMLS_CC, E_RECOVERABLE_ERROR, "unexpected error");
		RETURN_FALSE;
	}
}

END_EXTERN_C()

static Ref<String> decode_text(Ref<LuminanceSource> source)
{
	Ref<Binarizer> binarizer(new GlobalHistogramBinarizer(source));
	Ref<BinaryBitmap> bitmap(new BinaryBitmap(BinaryBitmap(binarizer)));
	DecodeHints hints(DecodeHints::BARCODEFORMAT_QR_CODE_HINT);
	QRCodeReader reader;

	return reader.decode(bitmap, hints)->getText();
}

static Ref<RawDecodedResult> decode_raw(Ref<LuminanceSource> source)
{
	Ref<BitMatrix> bits = detect_matrix(source);
	RawDecoder decoder;

	return decoder.decode(bits);
}

static Ref<BitMatrix> detect_matrix(Ref<LuminanceSource> source)
{
	Ref<Binarizer> binarizer(new GlobalHistogramBinarizer(source));
	Detector detector(BinaryBitmap(binarizer).getBlackMatrix());
	DecodeHints hints(DecodeHints::BARCODEFORMAT_QR_CODE_HINT);
	Ref<DetectorResult> result = detector.detect(hints);
#ifdef DEBUG
	vector<Ref<ResultPoint> > points = result->getPoints();
	vector<Ref<ResultPoint> >::iterator it = points.begin();
	while (it != points.end()) {
		cerr << (*it)->getX() << "," << (*it)->getY() << endl;
		++it;
	}
#endif
	return result->getBits();
}

static int do_decode(Ref<LuminanceSource> source, read_mode mode, zval *result TSRMLS_DC)
{
	try {
		if (mode == READ_RAW) {
			Ref<RawDecodedResult> decoded = decode_raw(source);

			ArrayRef<unsigned char> bytes = decoded->bytes();
			size_t size = bytes->size();
			unsigned char *data = (unsigned char *)emalloc(size + 1);
			for (size_t i = 0; i < size; i++) {
				data[i] = bytes[i];
			}
			data[size] = '\0';

			const string& ecLevel = decoded->ecLevel();

			array_init_size(result, 3);
			add_assoc_stringl(result, "bytes", (char *)data, size, 0);
			add_assoc_stringl(result, "ecLevel", (char *)ecLevel.data(), ecLevel.length(), 1);
			add_assoc_long(result, "version", (long)decoded->versionNumber());
		} else if (mode == READ_MATRIX) {
			Ref<BitMatrix> matrix = detect_matrix(source);
			const char *description = matrix->description();

			ZVAL_STRING(result, description, 1);
		} else {
			Ref<String> text = decode_text(source);
			const string& str = text->getText();

			ZVAL_STRINGL(result, str.data(), str.length(), 1);
		}
	} catch (Magick::Exception& e) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "unable to crop: %s", e.what());
		return FAILURE;
	} catch (FormatException& e) {
		php_error_docref(NULL TSRMLS_CC, E_NOTICE, "unable to parse bits");
		return FAILURE;
	} catch (ReaderException& e) {
		php_error_docref(NULL TSRMLS_CC, E_NOTICE, "unable to read QR Code: %s", e.what());
		return FAILURE;
	} catch (zxing::Exception& e) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "ZXing error: %s", e.what());
		return FAILURE;
	}

	return SUCCESS;
}

/*
 * Local Variables:
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
// vim: set fileencoding=utf-8 noexpandtab tabstop=4 shiftwidth=4:
