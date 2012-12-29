import java.io.File;
import java.util.Map;
import java.util.List;
import java.awt.image.BufferedImage;
import javax.imageio.ImageIO;
import com.google.zxing.*;
import com.google.zxing.client.j2se.BufferedImageLuminanceSource;
import com.google.zxing.common.GlobalHistogramBinarizer;
import com.google.zxing.qrcode.QRCodeReader;

class ZXRead {
  private QRCodeReader reader;

  public final int WRAP_COLS = 64;

  public ZXRead() {
    reader = new QRCodeReader();
  }

  protected void printException(Exception e) {
    System.out.println("<exception>" + e.toString() + "</exception>");
  }

  protected BufferedImage readImage(String filename) {
    try {
      return ImageIO.read(new File(filename));
    } catch (Exception e) {
      printException(e);
      return null;
    }
  }

  protected Result decodeImage(BufferedImage image) {
    try {
      BinaryBitmap bitmap = new BinaryBitmap(
        new GlobalHistogramBinarizer(
          new BufferedImageLuminanceSource(image)
        )
      );
      return reader.decode(bitmap);
    } catch (Exception e) {
      printException(e);
      return null;
    }
  }

  protected void dumpBytes(byte[] bytes) {
    StringBuffer hexBuf = new StringBuffer(WRAP_COLS);

    for (byte b : bytes) {
      hexBuf.append(Integer.toHexString((b & 0xF0) >> 4));
      hexBuf.append(Integer.toHexString(b & 0xF));
      //hexBuf.append(String.format("%02x", b));

      if (hexBuf.length() == WRAP_COLS) {
        System.out.println(hexBuf);
        hexBuf.setLength(0);
      }
    }

    if (hexBuf.length() > 0) {
      System.out.println(hexBuf);
      hexBuf.setLength(0);
    }
  }

  protected void printResult(Result result) {
    if (result.getBarcodeFormat() != BarcodeFormat.QR_CODE) {
      System.out.println("Not a QR code.");
      return;
    }

    Map<ResultMetadataType,Object> metadata = result.getResultMetadata();
    try {
      String ecLevel = (String) metadata.get(ResultMetadataType.ERROR_CORRECTION_LEVEL);
      System.out.println("<ecLevel>" + ecLevel + "</ecLevel>");
    } catch (Exception e) {
      // pass
    }

    Object value;
    try {
      value = metadata.get(ResultMetadataType.BYTE_SEGMENTS);
    } catch (Exception e) {
      System.out.println("<error>No byte segments.</error>");
      return;
    }
    @SuppressWarnings("unchecked") List<byte[]> byteSegments = (List<byte[]>) value;

    byte[] rawBytes = result.getRawBytes();
    System.out.println(String.format(
      "<leadingBytes>%02x%02x %02x%02x %02x%02x %02x%02x</leadingBytes>",
      rawBytes[0], rawBytes[1], rawBytes[2], rawBytes[3],
      rawBytes[4], rawBytes[5], rawBytes[6], rawBytes[7]
    ));

    System.out.println("<byteSegments>");
    for (byte[] bytes : byteSegments) {
      System.out.println("<bytes><![CDATA[");
      dumpBytes(bytes);
      System.out.println("]]></bytes>");
    }
    System.out.println("</byteSegments>");
  }

  public void process(String filename) {
    System.out.println("<qrcode>");
    System.out.println("<file>" + filename + "</file>");

    BufferedImage image = readImage(filename);
    if (image == null) {
      System.out.println("<error>Cannot read image.</error>");
      System.out.println("</qrcode>");
      return;
    }

    Result result = decodeImage(image);
    if (result == null) {
      System.out.println("<error>Cannot decode.</error>");
      System.out.println("</qrcode>");
      return;
    }

    try {
      printResult(result);
    } catch (Exception e) {
      printException(e);
    }
    System.out.println("</qrcode>");
  }

  public static void main(String[] args) {
    if (args.length == 0) {
      System.out.println("<error>Too few arguments!</error>");
      return;
    }

    ZXRead zxr = new ZXRead();

    for (String filename : args) {
      zxr.process(filename);
    }
  }
}
