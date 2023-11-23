<?php

namespace TradeAppOne\Domain\Components\Helpers\Blowfish;

/**
 * Blowfish encryption algorithm
 * Coded directly from Bruce Schneier's C code example available from
 * http://www.schneier.com/blowfish-download.html
 *
 * Algorithm reference: http://www.schneier.com/paper-blowfish-fse.html
 *
 * @author Matt Harris
 **/

class BlowfishHelper
{
    # Mode constants
    const BLOWFISH_MODE_EBC = 10;
    const BLOWFISH_MODE_CBC = 11;

    # Padding mode constants
    const BLOWFISH_PADDING_NONE = 20;
    const BLOWFISH_PADDING_RFC  = 21;
    const BLOWFISH_PADDING_ZERO = 22;

    public function __construct($key, $mode, $padding, $iv = null)
    {
        $this->mode      = $mode;
        $this->padding   = $padding;
        $this->N         = 16;
        $this->blockSize = 8;
        $this->applyKey($key);
        if (! empty($iv)) {
            $this->IV = $this->pad($iv);
        }
    }

    /**
     * Encrypts plaintext using Blowfish with the given key.
     *
     * @param string $plaintext the string to encrypt
     * @param string $key the encryption key
     * @param int $mode one of BLOWFISH_MODE_CBC, BLOWFISH_MODE_EBC. BLOWFISH_MODE_CBC is recommened
     * @param int $padding one of BLOWFISH_PADDING_NONE, BLOWFISH_PADDING_RFC, BLOWFISH_PADDING_ZERO.
     * BLOWFISH_PADDING_RFC is recommened
     * @param int $iv the initialisation vector. Required when using CBC mode.
     * @return string Returns the encrypted string. It is recommended you base64encode this for storage.
     * @author Matt Harris
     **/
    public static function encrypt(
        $plaintext,
        $key,
        $mode = BlowfishHelper::BLOWFISH_MODE_CBC,
        $padding = BlowfishHelper::BLOWFISH_PADDING_RFC,
        $iv = null
    ) {
        if ($mode == BlowfishHelper::BLOWFISH_MODE_CBC and empty($iv)) {
            throw new \Exception('CBC Mode requires an IV key');
        }
        $ciphertext = '';
        $fish       = new BlowfishHelper($key, $mode, $padding, $iv);
        $block      = &$fish->blockSize;
        $paded      = $fish->pad($plaintext);
        $len        = strlen($paded);

        # encrypt in 1 byte intervals
        for ($i = 0; $i < $len; $i += $block) {
            if ($mode == BlowfishHelper::BLOWFISH_MODE_CBC) {
                $chain           = ($i == 0) ? $fish->IV : substr($ciphertext, $i - $block, $block);
                list(, $xL, $xR) = unpack('N2', substr($paded, $i, $block) ^ $chain);
            } else {
                list(, $xL, $xR) = unpack('N2', substr($paded, $i, $block));
            }
            $fish->encipher($xL, $xR);
            $ciphertext .= pack('N2', $xL, $xR);
        }
        unset($fish);

        return $ciphertext;
    }

    /**
     * Decrypts the ciphertext using Blowfish with the given key.
     *
     * @param string $ciphertext the encrypted string
     * @param string $key the encryption key
     * @param int $mode one of BLOWFISH_MODE_CBC, BLOWFISH_MODE_EBC. BLOWFISH_MODE_CBC is recommened
     * @param int $padding one of BLOWFISH_PADDING_NONE, BLOWFISH_PADDING_RFC, BLOWFISH_PADDING_ZERO.
     * BLOWFISH_PADDING_RFC is recommened
     * @param int $iv the initialisation vector. Required when using CBC mode.
     * @return string Returns the plaintext string.
     * @author Matt Harris
     **/
    public static function decrypt(
        $ciphertext,
        $key,
        $mode = BlowfishHelper::BLOWFISH_MODE_CBC,
        $padding = BlowfishHelper::BLOWFISH_PADDING_RFC,
        $iv = null
    ) {
        if ($mode == BlowfishHelper::BLOWFISH_MODE_CBC and empty($iv)) {
            throw new \Exception('CBC Mode requires an IV key');

            return;
        }

        $plaintext = '';
        $fish      = new BlowfishHelper($key, $mode, $padding, $iv);
        $block     = &$fish->blockSize;
        $len       = strlen($ciphertext);

        # encrypt in 1 byte intervals
        for ($i = 0; $i < $len; $i += $block) {
            list(, $xL, $xR) = unpack('N2', substr($ciphertext, $i, $block));
            $fish->decipher($xL, $xR);
            if ($mode == BlowfishHelper::BLOWFISH_MODE_CBC) {
                $chain      = ($i == 0) ? $fish->IV : substr($ciphertext, $i - $block, $block);
                $plaintext .= (pack('N2', $xL, $xR) ^ $chain);
            } else {
                $plaintext .= pack('N2', $xL, $xR);
            }
        }
        $plaintext = $fish->unpad($plaintext);
        unset($fish);

        return $plaintext;
    }

    public function applyKey($key)
    {
        $this->P = DefaultKey::$P;
        $this->S = DefaultKey::$S;

        $j   = 0;
        $len = strlen($key);

        for ($i = 0; $i < $this->N + 2; ++$i) {
            $data = 0x00000000;
            for ($k = 0; $k < 4; ++$k) {
                $data = ($data << 8) | ord($key[$j]);
                $j   += 1;
                $j    = $j < $len ? $j : 0;
            }
            $this->P[$i] = $this->P[$i] ^ $data;
        }

        $datal = 0x00000000;
        $datar = 0x00000000;

        for ($i = 0; $i < $this->N + 2; $i += 2) {
            $this->encipher($datal, $datar);
            $this->P[$i]     = $datal;
            $this->P[$i + 1] = $datar;
        }

        for ($i = 0; $i < 4; ++$i) {
            for ($j = 0; $j < 256; $j += 2) {
                $this->encipher($datal, $datar);
                $this->S[$i][$j]     = $datal;
                $this->S[$i][$j + 1] = $datar;
            }
        }
    }

    /**
     * Pads the plaintext to the specified block size using either 0's the method
     * described in RFC 3852 Section 6.3.
     *
     * RFC 3852 method pads the input with a padding string of between 1 and 8
     * bytes to make the total length an exact multiple of 8 bytes. The value of
     * each byte of the padding string is set to the number of bytes added -
     * i.e. 8 bytes of value 0x08, 7 bytes of value 0x07, ..., 2 bytes of 0x02,
     * or one byte of value 0x01.
     *
     * Ref: http://www.di-mgt.com.au/cryptopad.html
     *
     * @param string $plaintext
     * @param int $block_size
     * @return string Returns the plaintext string padded to block_size
     * @access private
     **/
    public function pad($plaintext)
    {
        $block   = &$this->blockSize;
        $len     = strlen($plaintext);
        $pad_len = ($len < $block) ? $block - $len : ($block - ($len % $block)) % $block;

        switch ($this->padding) {
            case BlowfishHelper::BLOWFISH_PADDING_NONE:
                return $plaintext;
            case BlowfishHelper::BLOWFISH_PADDING_RFC:
                $padder = $pad_len;
                break;
            case BlowfishHelper::BLOWFISH_PADDING_ZERO:
                $padder = 0;
                break;
        }

        return str_pad($plaintext, $len + $pad_len, chr($padder));
    }

    /**
     * Unpads the plaintext removing all the 0's or using the method described
     * in RFC 3852 Section 6.3
     *
     * Ref: http://www.di-mgt.com.au/cryptopad.html
     *
     * @param string $plaintext
     * @return string Returns the plaintext without the padding bytes
     * @access private
     **/
    public function unpad($plaintext)
    {
        $block   = &$this->blockSize;
        $pad_len = ord(substr($plaintext, -1, 1));

        switch ($this->padding) {
            case BlowfishHelper::BLOWFISH_PADDING_NONE:
                return $plaintext;
            case BlowfishHelper::BLOWFISH_PADDING_RFC:
                if ($pad_len < 1 or $pad_len > $block) {
                    return $plaintext;
                } else {
                    $padding = substr($plaintext, -$pad_len);
                    # verify padding chars are all the same, if not just return the plaintext
                    foreach (preg_split('//', $padding, -1, PREG_SPLIT_NO_EMPTY) as $v) {
                        if (ord($v) != $pad_len) {
                            return $plaintext;
                        }
                    }

                    return substr($plaintext, 0, strlen($plaintext) - $pad_len);
                }
                break;
            case BlowfishHelper::BLOWFISH_PADDING_ZERO:
                if ($pad_len != 0) {
                    return $plaintext;
                } else {
                    return substr($plaintext, 0, strpos($plaintext, chr(0)));
                }
                break;
        }

        return null;
    }

    /**
     * Blowfish enciphering algorithm
     * @access private
     */
    public function encipher(&$xL, &$xR)
    {
        $_xL = $xL;
        $_xR = $xR;

        for ($i = 0; $i < $this->N; ++$i) {
            $_xL             = $_xL ^ $this->P[$i];
            $_xR             = $this->nonReversible($_xL) ^ $_xR;
            list($_xL, $_xR) = [$_xR, $_xL];
        }

        list($_xL, $_xR) = [$_xR, $_xL];
        $_xR             = $_xR ^ $this->P[$this->N];
        $_xL             = $_xL ^ $this->P[$this->N + 1];

        $xL = $_xL;
        $xR = $_xR;
    }

    /**
     * Blowfish deciphering algorithm
     * @access private
     */
    public function decipher(&$xL, &$xR)
    {
        $_xL = $xL;
        $_xR = $xR;

        for ($i = $this->N + 1; $i > 1; --$i) {
            $_xL             = $_xL ^ $this->P[$i];
            $_xR             = $this->nonReversible($_xL) ^ $_xR;
            list($_xL, $_xR) = [$_xR, $_xL];
        }

        list($_xL, $_xR) = [$_xR, $_xL];
        $_xR             = $_xR ^ $this->P[1];
        $_xL             = $_xL ^ $this->P[0];

        $xL = $_xL;
        $xR = $_xR;
    }

    /**
     * Blowfish non-reversible F function
     * @access private
     */
    private function nonReversible($x)
    {
        $d   = $x & 0xFF;
        $x >>= 8;
        $c   = $x & 0xFF;
        $x >>= 8;
        $b   = $x & 0xFF;
        $x >>= 8;
        $a   = $x & 0xFF;

        $y = $this->S[0][$a] + $this->S[1][$b];
        $y = $y ^ $this->S[2][$c];
        $y = $y + $this->S[3][$d];

        return $y;
    }
}
