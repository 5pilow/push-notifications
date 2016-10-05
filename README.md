# push-notifications

Web Push notifications example, **this is not a library**, just a sample app with push notifications, and useful functions :)

## Generate VAPID keys

Run the file [`generate_vapid_keys.js`](https://github.com/5pilow/push-notifications/blob/master/generate_vapid_keys.js) with node, it will generate your two VAPID keys and some useful conversions for you.
You will get something like:

**Private VAPID key:**
L-gxfGFLDm_PIB3KA5rFpi8sQAVMJCpmVqEpZdLsM3A

**Public VAPID key:**
BKQ7d895lHvLbZ56aaQzvXNwxvOWooDHnaqpLAHU4ymKNVppxCMCsC0j9BUDwabRHFPGg4UVMcj0OqIhaupNW1I

**Public key as a Uint8Array, to put int the subscribe() method:**
new Uint8Array([4,164,59,119,207,121,148,123,203,109,158,122,105,164,51,189,115,112,198,243,150,162,128,199,157,170,169,44,1,212,227,41,138,53,90,105,196,35,2,176,45,35,244,21,3,193,166,209,28,83,198,131,133,21,49,200,244,58,162,33,106,234,77,91,82])

**Private key as a PEM key:**
-----BEGIN EC PRIVATE KEY-----
MDECAQEEIC/oMXxhSw5vzyAdygOaxaYvLEAFTCQqZlahKWXS7DNwoAoGCCqGSM49
AwEH
-----END EC PRIVATE KEY-----


## Use these keys in PHP with [WebPush library](https://github.com/Minishlink/web-push)

Headers I set in WebPush:
```php
$headers = array(
    'Content-Length' => strlen($encrypted['cipherText']),
    'Content-Type' => 'application/octet-stream',
    'Content-Encoding' => 'aesgcm',
    'Encryption' => 'keyid="p256dh";salt="'.$encrypted['salt'].'"',
    'Crypto-Key' => 'keyid="p256dh";dh="'.$encrypted['localPublicKey'].'";p256ecdsa="' . <VAPID public key> . '"',
    'TTL' => $this->TTL,
    'Authorization' => 'WebPush ' . <JWT TOKEN>
);
```

The createJWT function :
```php
public function createJWT($endpoint) {
  $url = parse_url($endpoint);
  $header = [
    "typ" => 'JWT',
    "alg" => 'ES256'
  ];
  $payload = [
    "iss" => "https://leekwars.com",
    "aud" => $url['scheme'] . '://' . $url['host'],
    "exp" => time() + 86400,
    "sub" => "mailto:contact@leekwars.com"
  ];
  $key = openssl_pkey_get_private(self::$VAPID_PRIVATE_PEM);
  $hash = self::base64url_encode(json_encode($header)) . "." . self::base64url_encode(json_encode($payload));
  openssl_sign($hash, $sig, $key, OPENSSL_ALGO_SHA256);

  $sig = self::DER2Jose($sig, 256);
  return $hash . '.' . $sig;
}
private function base64url_encode($data) {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
```

The DER2Jose function is in [der2jose.php](https://github.com/5pilow/push-notifications/blob/master/der2jose.php)
