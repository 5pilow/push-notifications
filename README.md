# push-notifications

Web Push notifications example, this is not a library, but just a sample app with push notifications, and useful functions :)

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
