<?php

namespace App\Http\Controllers\Api;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
     public function index(){
       try{

        $client = new Client();
        $response = $client->get("https://drkogyi.vip/wp-content/uploads/2024/02/2417feb1.png");
        $fileContent = $response->getBody()->getContents();


        // Store the file temporarily
        $temporaryFilePath = storage_path('app/temporary-file.jpg');
        file_put_contents($temporaryFilePath, $fileContent);

        // Upload the file to Amazon S3
        $fileName = Str::random(5).'.png';
        Storage::disk('s3')->put('demo/file.jpg', file_get_contents($temporaryFilePath));
        Storage::disk("public")->put("testing/test.jpg",file_get_contents($temporaryFilePath));
        unlink($temporaryFilePath);

        return "done";

       }catch(Exception $e){
        return $e->getMessage();
       }
     }
}
