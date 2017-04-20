<?php

/**
 * An API Class to parse the RSS feed into json with cache option
 *
 * @author Saqueib Ansari http://www.qcode.in
 * @licence MIT
 *
 * To fetch an RSS feed as json use
 *      (new Qrss('https://news.google.com/?output=rss'))->json();
 *
 * For fresh copy you can use fresh() which will ignore cache
 *      (new Qrss('https://news.google.com/?output=rss'))->fresh()->json()
 *
 * You can also extend the parse method to customize the output
 *
 * class MyQrss extends QRss {
 *
 *      protected function parse($xml)
 *      {
 *          // you have all the xml elements as SimpleXMLElement object
 *          // parse it however you want, return the array from this
 *
 *          return [
 *              'title' => (string) $xml->channel->title
 *          ];
 *      }
 * }
 *
 */

class QRss
{
    /**
     * Directory name where should it cache the response
     *
     * @var string
     */
    private $cache_dir = 'cache';

    /**
     * How much seconds should cache live
     *
     * @var int default is 1 day
     */
    private $cache_ttl = 60 * 60 * 24 * 1;

    /**
     * URL of feed
     *
     * @var
     */
    private $url;

    /**
     * Flag to bypass the cache
     *
     * @var bool
     */
    private $fresh_copy = false;

    /**
     * Parser element array
     *
     * @var array
     */
    private $parser = [];

    /**
     * Error msg key in json response output
     *
     * @var string
     */
    protected $error_msg_key = 'msg';

    /**
     * QRss constructor
     *
     * @param $url string URL of feed
     */
    public function __construct($url)
    {
        if ( ! filter_var($url, FILTER_VALIDATE_URL) ) {

            $this->json_response([
                "{$this->error_msg_key}" => 'Invalid feed URL'],
            400);
        }

        $this->url = $url;
    }

    /**
     * Outputs the json response
     */
    public function json()
    {
        if ( ! $xml = $this->fetch() ) {
            $last_error = error_get_last();

            $this->json_response( [
                "{$this->error_msg_key}" =>  "Unable to connect to feed.",
                'error' => $last_error['message']],
            500);
        }

        $this->json_response($this->parse($xml));
    }

    /**
     * Set Cache directory
     *
     * @param string $cache_dir
     * @return QRss
     */
    public function cache_dir($cache_dir)
    {
        $this->cache_dir = $cache_dir;
        return $this;
    }

    /**
     * Fluent setter for fresh
     *
     * @return $this
     */
    public function fresh()
    {
        $this->fresh_copy = true;
        return $this;
    }

    /**
     * Set Cache TTL as string like `5 days`, `1 month` etc.
     *
     * @param $cache_ttl string for strtotime
     * @return QRss
     */
    public function cache_for($cache_ttl)
    {
        if ( $time = strtotime($cache_ttl) ) {
            $this->cache_ttl = $time - time();
        }

        return $this;
    }

    /**
     * Parse the feed, you can override this method to add other fields
     *
     * @param $xml SimpleXMLElement
     * @return array|bool
     */
    protected function parse($xml)
    {
        if( is_object($xml) ) {
            // channel info
            $this->parser['channel'] = [
                'title' => (string) $xml->channel->title,
                'link' => (string) $xml->channel->link,
                'img' => (string) $xml->channel->image->url,
                'description' => (string) $xml->channel->description,
                'lastBuildDate' => (string) $xml->channel->lastBuildDate,
                'generator' => (string) $xml->channel->generator
            ];

            // feed items
            $this->parser['items'] = [];

            foreach ( $xml->channel->item as $item ) {
                array_push($this->parser['items'], [
                    'title' => (string) $item->title,
                    'link' => (string) $item->link,
                    'description' => (string) $item->description,
                    'description_text' => strip_tags($item->description),
                    'pubDate' => (string) $item->pubDate
                ]);
            }

            return $this->parser;
        }

        $this->json_response([ "{$this->error_msg_key}" => 'Unable to Parse xml format.'], 500);

        return false;
    }

    /**
     * Get the content from url and validate xml
     *
     * @return bool
     */
    private function fetch()
    {
        // check if fresh copy needed
        if( ! $this->fresh_copy ) {
            if( $cached = $this->get_cache($this->url)) {
                return $cached;
            }
        }

        $content = @file_get_contents($this->url);

        if ( ! $content )  {
            $this->json_response( [
                "{$this->error_msg_key}" =>  "Unable to connect to feed URL"],
            404);
        }

        // validate the xml
        if( $xml = $this->validate_xml($content) ) {
            // put it in cache
            return $this->cache_it($this->url, $content);
        }

        return false;
    }

    /**
     * Cache the content of an url
     *
     * @param $url string url of feed
     * @param $content string content of file
     * @return bool|SimpleXMLElement
     */
    private function cache_it($url, $content)
    {
        $file_path = $this->get_cache_dir() . '/' . $this->generate_filename($url);

        $this->setup_cache_dir();

        file_put_contents($file_path, $content);

        return $this->validate_xml($content);
    }

    /**
     * Get the cached response for an url
     *
     * @param $url string url of feed
     * @return bool|SimpleXMLElement
     */
    private function get_cache($url)
    {
        $file_path = $this->get_cache_dir() . '/' . $this->generate_filename($url);

        // do we have a cache file
        if( file_exists($file_path) ) {

            // check if cache time expired
            if($this->is_cache_expired($file_path)) {
                @unlink($file_path);
                return false;
            }

            return simplexml_load_file($file_path);
        }

        return false;
    }

    /**
     * Setup cache directory if it don't exists
     */
    private function setup_cache_dir() {
        $dir_path = $this->get_cache_dir();
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }
    }

    /**
     * Filename for cache file
     *
     * @param $url string url of feed
     * @return string filename for cache file
     */
    private function generate_filename($url) {
        return md5($url);
    }

    /**
     * Validate the xml feed
     *
     * @param $xml string file content
     * @return bool|SimpleXMLElement
     */
    private function validate_xml($xml)
    {
        $rss = @simplexml_load_string($xml);

        return ($rss && $rss->channel) ? $rss : false;
    }

    /**
     * Return ttl in seconds for cache
     *
     * @return int
     */
    private function get_cache_ttl()
    {
        return $this->cache_ttl;
    }

    /**
     * Get cache directory path
     *
     * @return string
     */
    private function get_cache_dir()
    {
        return dirname(__FILE__) . '/' . $this->cache_dir;
    }

    /**
     * Is cache expired
     *
     * @param $file_path string cache file path
     * @return bool
     */
    private function is_cache_expired($file_path)
    {
        return filemtime($file_path) < (time() - $this->get_cache_ttl());
    }

    /**
     * Die a valid json response with given status code
     *
     * @param $data
     * @param int $status_code
     */
    private  function json_response($data, $status_code = 200) {
        $data = is_string($data) ? [$data] : $data;

        http_response_code($status_code);
        header('Content-Type: application/json');
        die(json_encode($data));
    }
}
