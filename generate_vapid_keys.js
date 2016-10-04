const webpush = require('web-push')
const atob = require('atob')
const asn1 = require('asn1.js')
const urlBase64 = require('urlsafe-base64')

const vapidKeys = webpush.generateVAPIDKeys()
console.log("Private VAPID key:")
console.log(vapidKeys.privateKey)
console.log("\nPublic VAPID key:")
console.log(vapidKeys.publicKey)

console.log("\nPublic key as a Uint8Array, to put int the subscribe() method:")
printUint8Array(urlBase64ToUint8Array(vapidKeys.publicKey))

console.log("\nPrivate key as a PEM key:")
console.log(toPEMKey(vapidKeys.privateKey))

function urlBase64ToUint8Array(base64String) {
	const padding = '='.repeat((4 - base64String.length % 4) % 4)
	const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/')
	const rawData = atob(base64)
	const outputArray = new Uint8Array(rawData.length)
	for (var i = 0; i < rawData.length; ++i) {
		outputArray[i] = rawData.charCodeAt(i)
	}
	return outputArray
}

function printUint8Array(array) {
	console.log("new Uint8Array([" + array + "])")
}

function toPEMKey(key) {
	key = urlBase64.decode(key)
	const ECPrivateKeyASN = asn1.define('ECPrivateKey', function() {
		this.seq().obj(
		this.key('version').int(),
		this.key('privateKey').octstr(),
		this.key('parameters').explicit(0).objid().optional(),
		this.key('publicKey').explicit(1).bitstr().optional())
	})
	return ECPrivateKeyASN.encode({
		version: 1,
		privateKey: key,
		parameters: [1, 2, 840, 10045, 3, 1, 7] // prime256v1
	}, 'pem', {
		label: 'EC PRIVATE KEY'
	})
}
