<?php
    require __DIR__ . '/vendor/autoload.php';

    class Token {
        function create($id) {
            // Create a token, configuring it with any headers and payload you need
            $token = new Emarref\Jwt\Token();
            $token->addClaim(new \Emarref\Jwt\Claim\PrivateClaim('id', $id));

            // Configure the encryption for the JWT instance
            $algorithm = new Emarref\Jwt\Algorithm\Rs256();
            $encryption = Emarref\Jwt\Encryption\Factory::create($algorithm);
            // Read in the contents of the PRIVATE key for use in encryption
            $privateKey = file_get_contents(getcwd().'../Token/private.key');
            // Configure the encryption with the private key
            $encryption->setPrivateKey($privateKey);
            // Set up the JWT instance
            $jwt = new \Emarref\Jwt\Jwt();
            // Serializing the token will encrypt its contents
            $serializedToken = $jwt->serialize($token, $encryption);
            // $serializedToken contains the encrypted token as a string.
            return $serializedToken;
        }

        function getClaimValue($token, $claimName) {
            // Configure the encryption for the JWT instance            
            $algorithm = new Emarref\Jwt\Algorithm\Rs256();
            $encryption = Emarref\Jwt\Encryption\Factory::create($algorithm);
            // Read in the contents of the PUBLIC key for use in decryption
            $publicKey = file_get_contents(getcwd().'../Token/public.crt');
            // Configure the encryption with the public key
            $encryption->setPublicKey($publicKey);
            // Set up the JWT instance
            $jwt = new \Emarref\Jwt\Jwt();
            // De-serializing the token will decrypt its contents
            $deserializedToken = $jwt->deserialize($token);
            $body = json_decode($deserializedToken->getPayload()->jsonSerialize(), true);
            
            return $body[$claimName];
        }
    }
?>