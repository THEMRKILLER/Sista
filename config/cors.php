<?php

    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */

return [
   'defaults' => [
       'supportsCredentials' => false,
       'allowedOrigins' => [],
       'allowedHeaders' => [],
       'allowedMethods' => [],
       'exposedHeaders' => [],
       'maxAge' => 0,
       'hosts' => [],
   ],

   'paths' => [
       'v1/*' => [
           'allowedOrigins' => ['*'],
           'allowedHeaders' => ['*'],
           'allowedMethods' => ['*'],
           'maxAge' => 3600,
       ],
   ],
];