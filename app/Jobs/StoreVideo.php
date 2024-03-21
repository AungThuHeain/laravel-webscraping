<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
class StoreVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $title,$imageApi,$videoApi;

    public $timeout = 10000;

    public function __construct($title,$imageApi,$videoApi)
    {
        $this->title = $title;
        $this->imageApi = $imageApi;
        $this->videoApi = $videoApi;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

            $client = new Client();

            // Fetch image content
            $imageContent = $client->get($this->imageApi)->getBody()->getContents();

            // Fetch video content
            $videoContent = $client->get($this->videoApi)->getBody()->getContents();

           // Upload image and video files to S3
            $imagePath = "images/" . $this->title . ".png";
            $videoPath = "videos/" . $this->title . ".mp4";

            Storage::disk("s3")->put($imagePath, $imageContent);
            Storage::disk("s3")->put($videoPath, $videoContent);

            // Get the URLs of the stored files
            $imageUrl = Storage::disk("s3")->url($imagePath);
            $videoUrl = Storage::disk("s3")->url($videoPath);

            //Create a new video record in the database
            Video::create([
                "description" => $this->title,
                "image" => $imageUrl,
                "video" => $videoUrl,
            ]);

            sleep(3);


    }
}
