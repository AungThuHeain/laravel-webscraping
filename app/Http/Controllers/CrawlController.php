<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CrawlController extends Controller
{

   public $api;

   public function __construct()
   {
     $this->api =   Http::get('https://drkogyi.vip/wp-json/wp/v2/posts?_fields=link,id,title&per_page=20&order=desc&page=76')->json();

   }

    public function index(){
        $links = $this->api;
        foreach($links as $link){
            $url = $link['link'];
            $client = new Client();
            $response = $client->get($url)->getBody()->getContents();
            $crawler = new Crawler($response);
            $iframe = $crawler->filter('iframe')->attr('src');
            $result=$this->video($iframe);

            return $result;
        }


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
