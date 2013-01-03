#ifndef __RAWDECODEDRESULT_H__
#define __RAWDECODEDRESULT_H__
/*
 * Copyright 2013 Ryusuke SEKIYAMA
 *
 * Licensed under the Apache License, Version 2.0 (the &quot;License&quot;);
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an &quot;AS IS&quot; BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package     php-zxing
 * @copyright   2013 Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

#include <zxing/common/Array.h>
#include <zxing/common/Counted.h>
#include <zxing/qrcode/ErrorCorrectionLevel.h>
#include <zxing/qrcode/Version.h>

namespace zxing {
namespace qrcode {

class RawDecodedResult : public Counted {
private:
  ArrayRef<unsigned char> bytes_;
  std::string ecLevel_;
  int versionNumber_;

public:
  RawDecodedResult(ArrayRef<unsigned char> bytes, ErrorCorrectionLevel& ecLevel, Version *version);
  ArrayRef<unsigned char> bytes();
  std::string const& ecLevel() const;
  int versionNumber() const;
};

}
}

#endif // __RAWDECODEDRESULT_H__
