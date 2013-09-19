<?php

class ApnsPHP_Push
{
        const       PASSPHRASE  = 'password123';
        protected   $_nCtx;

        public function __construct ()
        {
                $this->_nCtx    = stream_context_create();
                stream_context_set_option ($this->_nCtx, 'ssl', 'local_cert', '/filepath/ck.pem');
                stream_context_set_option ($this->_nCtx, 'ssl', 'passphrase', self::PASSPHRASE);
        }

        public function send ($message, $deviceToken)
        {
                // Open a connection to the APNS server
                // echo date ('Y-m-d H:i:s');
                $fp     = stream_socket_client( 'ssl://gateway.sandbox.push.apple.com:2195',
                                                $err,
                                                $errstr,
                                                60,
                                                // STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,
                                                STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT,
                                                $this->_nCtx );
                // echo date ('Y-m-d H:i:s');

                if (!$fp) {
                        exit("Failed to connect: $err $errstr" . PHP_EOL);
                }

                echo 'Connected to APNS' . PHP_EOL;

                // Create the payload body
                $body['aps']    = array ( 'alert' => $message, 'sound' => 'default' );

                // Encode the payload as JSON
                $payload    = json_encode ($body);

                // Build the binary notification
                $msg        = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                // Send it to the server
                $result     = fwrite ($fp, $msg, strlen ($msg));

                if (!$result) {
                        echo 'Message not delivered' . PHP_EOL;
                }
                else {
                        echo 'Message successfully delivered' . PHP_EOL;
                }

                // Close the connection to the server
                fclose($fp);
        }
}