<?php

namespace App\Controllers;

use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Guid\GuidInterface;
use Ramsey\Uuid\Rfc4122\FieldsInterface;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use Illuminate\Validation\Factory;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class Controller
{
    public function generateUuid(){
        $cache = new FilesystemAdapter(
            $namespace = 'my_cache_', 
            $defaultLifetime = 10, 
            $directory = __DIR__ . '/../cache' 
        );
        $uuid4 = \Ramsey\Uuid\Guid\Guid::uuid4();
        $cacheKey = 'user_uuid_'.$uuid4;
        $uuidItem = $cache->getItem($cacheKey);
        $uuidItem->set($uuid4);
        $cache->save($uuidItem);
        echo "Generated UUID: " . $uuidItem->get();
    }
    function login() {
        // Get JSON input from request body
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        $rules = [
            'username' => 'required|string|min:4|max:50',
            'password' => 'required|string|min:4|max:15'
        ];
        $messages = [
            'username.required' => 'The username field is required.',
            'username.string'   => 'The username must be a string.',
            'username.min'      => 'The username must be at least :min characters long.', // Shows "at least 5 characters long"
            'username.max'      => 'The username must not exceed :max characters.',
            
            'password.required' => 'The password field is required.',
            'password.string'   => 'The password must be a string.',
            'password.min'      => 'The password must be at least :min characters long.', // Shows "at least 8 characters long"
            'password.max'      => 'The password must not exceed :max characters.'
        ];

        $translator = new Translator(new ArrayLoader(), 'en');
        $validatorFactory = new Factory($translator);
        $validator = $validatorFactory->make($data, $rules, $messages);
        
        if ($validator->fails()) {
            echo json_encode(["errors" => $validator->errors()->toArray()]);
            http_response_code(422); // Unprocessable Entity
            return;
        }else{
            if($data['username'] === "admin" && $data['password'] === "secret"){
                /**
                 * ganerate a jwt token
                 */
                $payload = [
                    'iss' => 'your-issuer',       // Issuer
                    'aud' => 'your-audience',     // Audience
                    'iat' => time(),              // Issued at (current time)
                    'exp' => time() + 3600,       // Expiration time (1 hour from now)
                    'data' => [                   // Custom data
                        'userId' => 123,
                        'username' => 'john_doe'
                    ]
                ];
                $key = $_ENV['JWT_SECRET'];
                $jwt = JWT::encode($payload, $key, 'HS256');
                
                echo json_encode(['JWT' => $jwt]);
                
            }else{
                echo json_encode(["error" => "User not found"]);
                http_response_code(404);
            }
        }
    }
    function protected() {
        $headers = apache_request_headers();        
        if (isset($headers['Authorization'])) {
            $key = $_ENV['JWT_SECRET'];    
            $token = substr($headers['Authorization'], 7);
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                $decoded_array = (array) $decoded;
                if(isset($decoded_array['data']))
                    echo json_encode($decoded_array['data']);
            } catch (Exception $e) {
                echo json_encode(["error" => "Failed to decode token: " . $e->getMessage()]);
                http_response_code(401);
            }
        } else {
            echo json_encode(["error" => "Authorization header is missing"]);
            http_response_code(403);
        }
    }
}
