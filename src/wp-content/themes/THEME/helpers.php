<?php
/**
 * Truncate a string and add an optional suffix.
 *
 * @param string $pStr String to truncate
 * @param int $pLimit Length of the string to return
 * @param string $pEnd Suffix
 */
function truncate($pStr, $pLimit, $pEnd = '...')
{
    $str    = strip_tags($pStr);
    $length = strlen($str);
    if ($length <= $pLimit) {
        return $str;
    }

    return trim(mb_substr($str, 0, $pLimit)) . $pEnd;
}

/**
 * Load a template file.
 *
 * @param string $pPath
 * @param array $pVars
 */
function template($pPath, $pVars = array())
{
    extract($pVars);
    require(TEMPLATEPATH . "/inc/templates/$pPath.php");
}

/**
 * Send an HTML message.
 */
function sendHtmlMail($pMessage, $pTo, $pSubject)
{
    $html = file_get_contents(get_template_directory() . '/mail.html');
    $html = str_replace('{TEMPLATE_URL}', get_template_directory_uri(), $html);
    $html = str_replace('{TEMPLATE_HTML}', $pMessage, $html);

    $headers = array(
        "From: " . MAIL_FROM,
        'Content-type: text/html'
    );

    return wp_mail($pTo, $pSubject, $html, $headers);
}

/**
 * Convert an hexadecimal string into a binary string.
 *
 * @param string $pSource Hexadecimal string
 * @return string Binary string
 */
function _hex2bin($pSource)
{
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        $strlen = strlen($pSource);
        $bin = '';
        for ($i = 0; $i < strlen($pSource); $i = $i + 2) {
            $bin .= chr(hexdec(substr($pSource, $i, 2)));
        }

        return $bin;
    }

    return hex2bin($pSource);
}

/**
 * Encode a string.
 *
 * @param string $pStr Strin to encode
 * @return string Encoded string
 */
function encode($pStr)
{
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB), MCRYPT_RAND);
    return bin2hex(mcrypt_encrypt(MCRYPT_BLOWFISH, ENCODE_KEY, $pStr, MCRYPT_MODE_ECB, $iv));
}

/**
 * Decode en string.
 *
 * @param string $pStr String to decode
 * @return string Decoded string
 */
function decode($pStr)
{
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB), MCRYPT_RAND);
    return trim(mcrypt_decrypt(MCRYPT_BLOWFISH, ENCODE_KEY, _hex2bin($pStr), MCRYPT_MODE_ECB, $iv), "\0");
}
