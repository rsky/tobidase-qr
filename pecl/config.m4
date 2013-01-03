PHP_ARG_WITH(zxing, [ZXing build root directory],
[  --with-zxing=PATH       Enable ZXing support.
                          PATH is ZXing build root directory],
  /usr/local/src/zxing-2.1, yes)

dnl PHP_ARG_ENABLE(zxing-gd, [Whether to enable GD image loader],
dnl [  --enable-zxing-gd       ZXing: Enable GD image loader.
dnl                           PHP GD extension is required],
dnl   no, no)

PHP_ARG_WITH(zxing-imagemagick, [Pathname of Magiick++-config],
[  --with-zxing-imagemagick[[=PATH]] ZXing: Enable ImageMagick image loader.
                                  PATH is a pathname of Magiick++-config],
  yes, no)

if test "$PHP_ZXING" != "no"; then
  PHP_REQUIRE_CXX()

  AC_DEFUN([MY_EVAL_DEFLINE],[
    for ac_d in $1; do
      case $ac_d in
      -D*[)]
        CPPFLAGS="$CPPFLAGS $ac_d"
      ;;
      esac
    done
  ])

  dnl
  dnl ZXing
  dnl
  AC_CHECK_FILE("$PHP_ZXING/cpp/build/libzxing.a", [], [
    AC_MSG_ERROR([libzxing.a not found])
  ])

  ZXING_INCLINE="-I$PHP_ZXING/cpp/core/src"
  ZXING_LIBLINE="-L$PHP_ZXING/cpp/build -lzxing"
  PHP_EVAL_INCLINE($ZXING_INCLINE)
  PHP_EVAL_LIBLINE($ZXING_LIBLINE, ZXING_SHARED_LIBADD)
  PHP_ZXING_SOURCES="php_zxing.cpp RawDecoder.cpp RawDecodedResult.cpp"

  dnl
  dnl Check for image loaders
  dnl
  if test "$PHP_ZXING_IMAGEMAGICK" = "yes"; then
    AC_PATH_PROGS(MAGICKXX_CONFIG, Magick++-config, [no])
  elif test "$PHP_ZXING_IMAGEMAGICK" == "no"; then
    MAGICKXX_CONFIG=no
  else
    MAGICKXX_CONFIG="$PHP_ZXING_IMAGEMAGICK"
  fi

  dnl if test "$PHP_ZXING_GD$MAGICKXX_CONFIG" = "nono"; then
  if test "$MAGICKXX_CONFIG" = "no"; then
    AC_MSG_ERROR([no image loader is enabled])
  fi

  dnl
  dnl GD
  dnl
  dnl if test "$PHP_ZXING_GD" != no; then
  dnl   PHP_ADD_EXTENSION_DEP(zxing, gd)
  dnl 
  dnl   AC_DEFINE(HAVE_PHP_ZXING_GD, 1, [GD image loader is enabled])
  dnl   PHP_ZXING_SOURCES="$PHP_ZXING_SOURCES GDBitmapSource.cpp"
  dnl fi

  dnl
  dnl ImageMagick
  dnl
  if test "$MAGICKXX_CONFIG" != no; then
    ZXING_MAGICKXX_CPPFLAGS="$("$MAGICKXX_CONFIG" --cppflags 2>/dev/null)"
    ZXING_MAGICKXX_LIBS="$("$MAGICKXX_CONFIG" --libs 2>/dev/null)"

    MY_EVAL_DEFLINE($ZXING_MAGICKXX_CPPFLAGS)
    PHP_EVAL_INCLINE($ZXING_MAGICKXX_CPPFLAGS)
    PHP_EVAL_LIBLINE($ZXING_MAGICKXX_LIBS, ZXING_SHARED_LIBADD)

    AC_DEFINE(HAVE_PHP_ZXING_MAGICK, 1, [ImageMagick image loader is enabled])
    PHP_ZXING_SOURCES="$PHP_ZXING_SOURCES MagickBitmapSource.cpp"
  fi

  PHP_SUBST(ZXING_SHARED_LIBADD)
  PHP_NEW_EXTENSION(zxing, $PHP_ZXING_SOURCES, $ext_shared)
fi
