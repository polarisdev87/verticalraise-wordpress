<?php

namespace classes\app\encryption;

/**
 * This class allows for Encryption and Decryption of data
 */
class Encryption {

	/**
	 * Encrypt data.
	 *
	 * @param  string $data The data to encrypt.
	 * @return string Encrypted data
	 */
	public function encrypt( $data ) {

		// Remove the base64 encoding from our key.
		$encryption_key = base64_decode( _ENCRYPTION_KEY );

		// The method.
		$method = 'AES-256-CBC';

		// Generate an initialization vector.
		$iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( $method ) );

		// Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
		$encrypted = openssl_encrypt( $data, $method, $encryption_key, 0, $iv );

		// The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::).
		return base64_encode( $encrypted . '::' . $iv );

	}

	/**
	 * Decrypt data.
	 *
	 * @param  string $data The encrypted data.
	 * @return string The decrypted data.
	 */
	public function decrypt( $data ) {

		// Remove the base64 encoding from our key.
		$encryption_key = base64_decode( _ENCRYPTION_KEY );

		// The method.
		$method = 'AES-256-CBC';

		// To decrypt, split the encrypted data from our IV - our unique separator used was "::".
		list($encrypted_data, $iv) = explode( '::', base64_decode( $data ), 2 );

		$iv = substr( $iv, 0, 16 );

		return openssl_decrypt( $encrypted_data, $method, $encryption_key, 0, $iv );

	}

}
