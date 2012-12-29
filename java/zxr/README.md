ZXingでQRコードを解析してダンプするサンプル
===========================================

必要なもの
----------

* JDK 6
* [ZXing](http://code.google.com/p/zxing/) 2.1

使い方など
----------

### コンパイル

```
unzip ZXing-2.1.zip
export CLASSPATH=.:./zxing-2.1/core/core.jar:./zxing-2.1/javase/javase.jar
javac -encoding utf-8 ZXRead.java
```

### jarを作る

```
unzip zxing-2.1/core/core.jar -x META-INF/\*
unzip zxing-2.1/javase/javase.jar -x META-INF/\*
jar cmf manifest.mf zxr.jar ZXRead.class com
```

### 実行する
```
java -jar zxr.jar /path/to/qrcode ...
```
