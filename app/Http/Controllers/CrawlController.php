<?php

namespace App\Http\Controllers;

use App\Models\Video;
use GuzzleHttp\Client;
use App\Jobs\StoreVideo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class CrawlController extends Controller
{

   public $api;

   public function __construct()
   {
     $this->api =   Http::get('https://drkogyi.vip/wp-json/wp/v2/posts?_fields=link,id,title&per_page=1&order=desc&page=76')->json();

   }


    public function index(){
        $links = $this->api;

        foreach ($links as $link) {
            $url = $link['link'];
            $client = new Client();

            // Fetch the iframe source from the URL
            $response = $client->get($url)->getBody()->getContents();
            $crawler = new Crawler($response);
            $iframe = $crawler->filter('iframe')->attr('src');

            // Fetch image and video URLs from the API
            $result = $this->video($iframe);
            $title = $link['title']['rendered'];
            $imageApi = $result['image'];
            $videoApi = $result['video'];

            dispatch(new StoreVideo($title,$imageApi,$videoApi));
        }
        return "job done";
    }

    public function video($iframe){
        $client = new Client();

        // Fetch the content of the URL pointed to by the iframe
        $iframeResponse = $client->get($iframe)->getBody()->getContents();

        // Create a crawler to parse the iframe content
        $iframeCrawler = new Crawler($iframeResponse);

        // Extract the src attribute of the iframe
        $videoUrl = $iframeCrawler->filter('video source')->attr('src');

        $imageUrl = $iframeCrawler->filter('video')->attr('poster');

        return ["video"=>$videoUrl,"image"=>$imageUrl];
    }


}
