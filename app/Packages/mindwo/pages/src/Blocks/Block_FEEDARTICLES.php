<?php

namespace mindwo\pages\Blocks
{

    use DB;
    use Config;
    use mindwo\pages\Exceptions\PagesException;
    use Input;
    use Illuminate\Support\Facades\File;
    use Detection\MobileDetect;

    /**
     *
     * Ziņu plūsmas bloka klase
     *
     *
     * Objekts nodrošina datu attēlošanu ziņām ritināmas plūsmas veidā
     *
     */
    class Block_FEEDARTICLES extends Block
    {

        public $source_id = 0;
        public $article_url = "";
        public $articles_items = null;
        public $type_id = 0;
        private $html_cache = "";
        private $cache_path = "";

        /**
         * Plūsmai norādītās iezīmes ID - plūsmā tad tiek attēlotas arī ziņas ar attiecīgo iezīmi neatkarīgi no datu avota
         * 
         * @var integer 
         */
        public $tag_id = 0;

        /**
         * Ja lielāks par 0, tad tiks ieladēts attiecīgs skaits ziņu un nebūs lazy load papildus ielāde
         * @var integer
         */
        private $top_count = 0;
        
        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            if (strlen($this->html_cache) > 0) {
                return $this->html_cache;
            }

            $html = view('mindwo/pages::blocks.feed_articles', [
                         'articles_items' => $this->articles_items,
                         'block_guid' => $this->block_guid,
                         'top_count' => $this->top_count
            ])->render();

            File::put($this->cache_path, $html);
            return $html;
        }

        /**
         * Izgūst bloka JavaScript
         * 
         * @return string Bloka JavaScript loģika
         */
        public function getJS()
        {
            return "";
        }

        /**
         * Izgūst bloka CSS
         * 
         * @return string Bloka CSS
         */
        public function getCSS()
        {
            return view('mindwo/pages::blocks.feed_articles_css')->render();
        }

        /**
         * Izgūst bloka JSON datus
         * 
         * @return string Bloka JSON dati
         */
        public function getJSONData()
        {
            return "";
        }

        /**
         * Izgūst bloka parametra vērtības un izpilda ziņu izgūšanu masīvā
         * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=...|SOURCE=...|ARTICLEPAGE=...|TAGSPAGE=...]]
         * 
         * @return void
         */
        protected function parseParams()
        {
            $dat_arr = explode('|', $this->params);

            foreach ($dat_arr as $item) {
                $val_arr = explode('=', $item);

                if ($val_arr[0] == "SOURCE") {
                    $this->source_id = getBlockParamVal($val_arr);
                }
                else if ($val_arr[0] == "ARTICLEPAGE") {
                    $this->article_url = getBlockRelPageUrl($val_arr);
                }
                else if ($val_arr[0] == "TYPE") {
                    $this->type_id = getBlockParamVal($val_arr);
                }
                else if ($val_arr[0] == "TAG_ID") {
                    $this->tag_id = getBlockParamVal($val_arr);
                }
                else if ($val_arr[0] == "TOP") {
                    $this->top_count = getBlockParamVal($val_arr);
                }
                else if (strlen($val_arr[0]) > 0) {
                    throw new PagesException("Norādīts blokam neatbilstošs parametra nosaukums (" . $val_arr[0] . ")!");
                }
            }

            $this->setArticlesCache();

            if (strlen($this->html_cache) == 0) {
                $this->articles_items = $this->getArticlesArray();
            }

            $this->is_uniq_in_page = 1; // Plūsmas bloku var ievietota vienā lapā tikai 1 reizi
            $this->addJSInclude('mindwo/plugins/jscroll/jquery.jscroll.js');
            $this->addJSInclude('mindwo/blocks/feed_articles.js');
        }

        /**
         * Uzstāda HTML vērtību no cache (ja ir)
         */
        private function setArticlesCache()
        {
            $this->cache_path = $this->getArticleCachePath();

            if (File::isFile($this->cache_path)) {

                $db_change = strtotime(DB::table('in_last_changes')->where('code', '=', 'ARTICLE')->first()->change_time);

                $file_change = File::lastModified($this->cache_path);

                if ($file_change >= $db_change) {
                    $this->html_cache = File::get($this->cache_path);
                }
            }
        }
        
        /**
         * Izgūst ziņu cache datnes pilno ceļu
         * Pārbauda, arī vai pieslēgums no mobilā vai planšetes - katram savs config
         * 
         * @return type
         */
        private function getArticleCachePath() {
            $detect = new MobileDetect();
            $mob_prefix = "";
            
            if ($detect->isMobile() || $detect->isTablet()) {
                $mob_prefix = "mob_";
            }
            
            $database_name = Config::get('database.connections.' . Config::get('database.default') . '.database') . "_" . getRootForCache();
                    
            return base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'feed' . DIRECTORY_SEPARATOR . $database_name . "_" . $mob_prefix . 'source_' . $this->source_id . '_url_' . $this->article_url . '_type_' . $this->type_id . '_tag_' . $this->tag_id . '_page_' . Input::get('page', 0) . '.txt';
        }

        /**
         * Izgūst ziņu rakstus.
         * Portālā ziņas var būt dažādiem uzņēmumiem - katrs uzņēmums ir kā rakstu avots.
         * 
         * @return Array Masīvs ar ziņām atbilstoši avotam
         */
        private function getArticlesArray()
        {
            $articles = get_article_query()
                    ->where('in_articles.is_active', '=', 1)
                    ->where('in_articles.is_static', '=', 0)
                    ->where(function($query)
                    {
                        $query->where(function($query)
                        {
                            if ($this->source_id > 0) {
                                $query->whereExists(function ($query)
                                {
                                    $query->select(DB::raw(1))
                                    ->from('in_tags_article')
                                    ->whereRaw('in_tags_article.article_id = in_articles.id AND in_tags_article.tag_id=' . $this->tag_id);
                                })
                                ->orWhere('in_articles.source_id', '=', $this->source_id);
                            }
                        });

                        if ($this->type_id > 0) {
                            $query->where('in_articles.type_id', '=', $this->type_id);
                        }
                    })
                    ->orderBy('in_articles.order_index', 'ASC')
                    ->orderBy('in_articles.publish_time', 'DESC');
            
            $limit = $this->top_count;
            if ($limit == 0) {
                $limit = Config::get('dx.feeds_page_rows_count');
            }
            
            $articles = $articles->simplePaginate($limit);
            
            $this->prepareArticleTags($articles);

            return $articles;
        }

        /**
         * Sagatavo iezīmju masīvu rakstam
         * @param   mixed $articles raksta objekts
         * @return
         */
        private function prepareArticleTags($articles)
        {
            $articles->each(function ($item, $key)
            {

                if ($item !== null) {

                    $item->tag_ids = explode(';', $item->tag_ids);

                    $item->tags = DB::table('in_tags')
                            ->join('in_tags_article', 'in_tags.id', '=', 'in_tags_article.tag_id')
                            ->select(DB::raw("in_tags.name, in_tags.id"))
                            ->where('in_tags_article.article_id', $item->id)
                            ->take(Config::get('dx.max_tags_count'))
                            ->orderBy('in_tags_article.id', 'ASC')
                            ->get();
                }
            });
        }

    }

}
