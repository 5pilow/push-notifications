<?php

/**
 * Convert a DER signature to a JOSE signature
 * This function is a translation from the ecdsa-sig-formatter node library:
 * https://github.com/Brightspace/node-ecdsa-sig-formatter
 * (Function derToJose in ecdsa-sig-formatter.js)
 *
 * @param string $signature The binary DER signature
 * @param int $keySize The key size (256 for ES256 for example)
 * 
 * @return string Return the JOSE key in Base 64.
 */
private function DER2Jose($signature, $keySize) {

	$MAX_OCTET = 0x80;
	$CLASS_UNIVERSAL = 0;
	$PRIMITIVE_BIT = 0x20;
	$TAG_SEQ = 0x10;
	$TAG_INT = 0x02;
	$ENCODED_TAG_SEQ = ($TAG_SEQ | $PRIMITIVE_BIT) | ($CLASS_UNIVERSAL << 6);
	$ENCODED_TAG_INT = $TAG_INT | ($CLASS_UNIVERSAL << 6);

	$paramBytes = (($keySize / 8) | 0) + ($keySize % 8 === 0 ? 0 : 1);

	$maxEncodedParamLength = $paramBytes + 1;
	$inputLength = strlen($signature);

	$offset = 0;
	if (ord($signature[$offset++]) !== $ENCODED_TAG_SEQ) {
		return 'Could not find expected "seq"';
	}

	$seqLength = ord($signature[$offset++]);
	if ($seqLength === ($MAX_OCTET | 1)) {
		$seqLength = ord($signature[$offset++]);
	}

	if ($inputLength - $offset < $seqLength) {
		return '"seq" specified length of "' . $seqLength . '", only "' . ($inputLength - $offset) . '" remaining';
	}

	if (ord($signature[$offset++]) !== $ENCODED_TAG_INT) {
		return 'Could not find expected "int" for "r"';
	}

	$rLength = ord($signature[$offset++]);

	if ($inputLength - $offset - 2 < $rLength) {
		return '"r" specified length of "' . $rLength . '", only "' . ($inputLength - $offset - 2) . '" available';
	}

	if ($maxEncodedParamLength < $rLength) {
		return '"r" specified length of "' . $rLength . '", max of "' . $maxEncodedParamLength . '" is acceptable';
	}

	$rOffset = $offset;
	$offset += $rLength;

	if (ord($signature[$offset++]) !== $ENCODED_TAG_INT) {
		return 'Could not find expected "int" for "s"';
	}

	$sLength = ord($signature[$offset++]);

	if ($inputLength - $offset !== $sLength) {
		return '"s" specified length of "' . $sLength . '", expected "' . ($inputLength - $offset) . '"';
	}

	if ($maxEncodedParamLength < $sLength) {
		return '"s" specified length of "' . $sLength . '", max of "' . $maxEncodedParamLength . '" is acceptable';
	}

	$sOffset = $offset;
	$offset += $sLength;

	if ($offset !== $inputLength) {
		return 'Expected to consume entire buffer, but "' + ($inputLength - $offset) + '" bytes remain';
	}

	$rPadding = $paramBytes - $rLength;
	$sPadding = $paramBytes - $sLength;

	$dst = $rPadding + $rLength + $sPadding + $sLength;

	for ($offset = 0; $offset < $rPadding; ++$offset) {
		$dst[$offset] = 0;
	}

	$from = $rOffset + max(-$rPadding, 0);
	$to = $rOffset + $rLength;
	$to_copy = substr($signature, $from, $to - $from);

	$dst = substr($dst, 0, $offset)
		   . $to_copy
		   . substr($dst, $offset + strlen($to_copy));

	$offset = $paramBytes;

	for ($o = $offset; $offset < $o + $sPadding; ++$offset) {
		$dst[$offset] = 0;
	}

	$from = $sOffset + max(-$sPadding, 0);
	$to = $sOffset + $sLength;
	$to_copy = substr($signature, $from, $to - $from);
	$dst = substr($dst, 0, $offset)
		   . $to_copy
		   . substr($dst, $offset + strlen($to_copy));

	$dst = rtrim(strtr(base64_encode($dst), '+/', '-_'), '=');
	return $dst;
}
