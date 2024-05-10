<?php
namespace App\Service;

use App\Entity\Figure;

class VideoUploader
{

        public function uploadVideos(Figure $figure): void
    {
        foreach ($figure->getVideos() as $video) {
            $check = parse_url($video->getName(), PHP_URL_HOST);
            parse_str(parse_url($video->getName(), PHP_URL_QUERY), $videoId);

            if ($check === "www.youtube.com" && array_key_exists('v', $videoId)) {
                $video->setVideoId($videoId['v']);

                $figure->addVideo($video);
            } elseif ($video->getName() === null || $video->getVideoId() === null) {
                $figure->removeVideo($video);
            }
        }
    }
}
