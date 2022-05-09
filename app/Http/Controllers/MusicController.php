<?php

namespace App\Http\Controllers;

use App\Utils\R;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class MusicController extends Controller
{
    public function getMusicList(Request $request): JsonResponse
    {
        $list_id = env("NETEASE_MUSIC_LIST_ID");
        $request_cookies = $this->getCookies();
        $response = Http::withoutVerifying()->withCookies($request_cookies, "music.163.com")->get('https://music.163.com/playlist?id=' . $list_id);
        $dom = HtmlDomParser::str_get_html($response->body());
        $music_list = [];

        try {
            $musics = $dom->getElementByTagName("ul")->getElementByClass("f-hide")[0]->getElementsByTagName("a");
            foreach ($musics as $music) {
                $music_list[] = [
                    "name" => $music->text(),
                    "id" => explode("=", $music->attr['href'])[1],
                    "source" => "https://music.163.com/song/media/outer/url?id=" . explode("=", $music->attr['href'])[1] . ".mp3",
                    "url" => "https://music.163.com/#/song?id=" . explode("=", $music->attr['href'])[1],
                ];
            }
        } catch (\Exception $e) {
            echo $e;
            return R::error(500);
        }

        if (env("NETEASE_MUSIC_MY_MUSIC_COUNT") > 0) {
            $music_list = array_slice($music_list, 0, env("NETEASE_MUSIC_MY_MUSIC_COUNT"));
        }

//        foreach ($music_list as $music) {
//            $response = Http::withoutVerifying()->withCookies($request_cookies, "music.163.com")->get('https://music.163.com/#/song?id=' . $music['id']);
//            //echo $response->body();
//        }

        return R::ok(null, $music_list);
    }

    public function getMusicInfo(Request $request): JsonResponse
    {
        $music_id = $request->route('id');
        $music_info = ["id" => $music_id];
        $request_cookies = $this->getCookies();
        $response = Http::withoutVerifying()->withCookies($request_cookies, "music.163.com")->get('https://music.163.com/song?id=' . $music_id);
        $dom = HtmlDomParser::str_get_html($response->body());
        if (strpos($response->body(), "很抱歉，你要查找的网页找不到")) {
            return R::error(404, "该歌曲无法访问",$music_info);
        }
        if (strpos($response->body(), "VIP尊享")) {
            return R::error(403, "该歌曲由于是会员专享无法获取外链",$music_info);
        }
        $musics = $dom->getElementsByTagName("meta")->attr;
        foreach ($musics as $music) {
            if (empty($music['property'])) {
                continue;
            }
            if ($music['property'] == "og:image") {
                $music_info += ["cover" => $music['content']];
            }
            if ($music['property'] == "og:music:artist") {
                $music_info += ["artist" => $music['content']];
            }
        }
        return R::ok(null, $music_info);
    }

    private function getCookies()
    {
        if (env('NETEASE_MUSIC_USE_COOKIES')) {
            $cookies = env("NETEASE_MUSIC_COOKIES");
            $cookies = explode("; ", $cookies);
            $request_cookies = [];
            foreach ($cookies as $cookie) {
                $temp = explode("=", $cookie);
                $request_cookies += [$temp[0] => $temp[1]];
            }
        } else {
            $request_cookies = [];
        }
        return $request_cookies;
    }
}
