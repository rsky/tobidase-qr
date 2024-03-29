#ifndef __MAGICK_BITMAP_SOURCE_H_
#define __MAGICK_BITMAP_SOURCE_H_
/*
 *  Copyright 2010-2011 ZXing authors
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

#include <Magick++.h>
#include <zxing/LuminanceSource.h>

namespace zxing {

class MagickBitmapSource : public LuminanceSource {
private:
  Magick::Image image_;
  int width;
  int height;

  inline unsigned char getPixelLuminance(const Magick::PixelPacket* p) const {
    return (unsigned char)
      ((306 * ((int)p->red   >> (MAGICKCORE_QUANTUM_DEPTH - 8))
      + 601 * ((int)p->green >> (MAGICKCORE_QUANTUM_DEPTH - 8))
      + 117 * ((int)p->blue  >> (MAGICKCORE_QUANTUM_DEPTH - 8))
      + 0x200) >> 10);
  };

public:
  MagickBitmapSource(Magick::Image& image);

  ~MagickBitmapSource();

  int getWidth() const;
  int getHeight() const;
  unsigned char* getRow(int y, unsigned char* row);
  unsigned char* getMatrix();
  bool isCropSupported() const;
  Ref<LuminanceSource> crop(int left, int top, int width, int height);
  bool isRotateSupported() const;
  Ref<LuminanceSource> rotateCounterClockwise();
};

}

#endif /* MAGICKMONOCHROMEBITMAPSOURCE_H_ */
