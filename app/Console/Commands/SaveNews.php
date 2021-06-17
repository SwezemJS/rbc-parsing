<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Illuminate\Support\Facades\Http;
use App\News;
use App\Logs;
use SimpleXMLElement;

class SaveNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'http://static.feed.rbc.ru/rbc/logical/footer/news.rss';
        $request =  Http::get($url);



        $data = new SimpleXmlElement($request->body());

        foreach ($data->channel->item as $new) {
            $img_url = "";
            foreach ($new->enclosure as $link) {
                if ($link['type'] == "image/jpeg") {
                    $img_url = $link['url'];
                    break;
                }
            }

            News::create([
                'title' => $new->title ,
                'link' => $new->link,
                'description' => $new->description,
                'pub_date' => $new->pubDate,
                'author' => $new->author ?? null,
                'image_link' => $img_url,
            ]);
        }
        self::httpLogs('get',$url,$request->status(),$request->body());
    }

    static public function httpLogs($method,$url,$response_code,$response_body) {
        $log = new Logs();

        $log->method = $method;
        $log->url = $url;
        $log->response_code = $response_code;
        $log->response_body = json_encode($response_body);

        $log->save();
    }
}
