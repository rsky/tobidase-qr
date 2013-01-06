php-zxing
=========

QR Code reader PHP extension using ZXing.


EXPERIMENTAL
------------

This extension's API is experimental and will be changed in future versions.


REQUIREMENTS
------------

PHP Version 5.3 or later.

Build Dependencies:

* [Magick++](http://imagemagick.org/Magick++/)
* [Scons](http://www.scons.org/)
* [ZXing](http://code.google.com/p/zxing/)

Runtime Dependencies:

* [Magick++](http://imagemagick.org/Magick++/)


INSTALL
-------

### 1. Build the ZXing C++ static library

1. `unzip ZXing-x.y.zip`
2. `cd zxing-x.y/cpp`
3. `scons lib DEBUG=0`

### 2. Build and install this extension

1. `phpize`
2. `./configure --with-zxing=/path/to/zxing-x.y`
    * You can specify the pathname of Magick++-config with `--with-zxing-imagemagick=/path/to/Magick++-config`.
3. `make`
4. `make test` *CURRENTLY THERE IS NO TEST*
5. `[sudo] make install`

### 3. Activate

Add `extension=zxing.so` to php.ini.


USAGE
-----

To get the text data:

	$text = zx_read_qrcode('qrocde.png');

To get the binary (raw) data, the symbol version and the error correction level as an associative array:

	$info = zx_read_qrcode('qrocde.png', ZX_READ_RAW);

To get the QR Code symbol (barcode matrix) data:

	$symbol = zx_read_qrcode('qrocde.png', ZX_READ_MATRIX);


LICENSE
-------

```
Copyright 2013 Ryusuke SEKIYAMA

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```
