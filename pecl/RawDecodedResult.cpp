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

#include "RawDecodedResult.h"

namespace zxing {
namespace qrcode {

using namespace std;

RawDecodedResult::RawDecodedResult(
  ArrayRef<unsigned char> bytes,
  ErrorCorrectionLevel& ecLevel,
  Version *version
) {
  bytes_ = bytes;
  ecLevel_ = ecLevel.name();
  versionNumber_ = version->getVersionNumber();
}

ArrayRef<unsigned char> RawDecodedResult::bytes() {
  return bytes_;
}

std::string const& RawDecodedResult::ecLevel() const {
  return ecLevel_;
}

int RawDecodedResult::versionNumber() const {
  return versionNumber_;
}

}
}
