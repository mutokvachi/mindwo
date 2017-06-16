<?php

namespace mindwo\pages\Blocks\Contents
{

    use Webpatser\Uuid\Uuid;
    use DB;
    use Config;
    use Request;

    /**
     * Portāla satura (raksti, video, attēli) abstraktā klase.
     * Definē visiem portāla satura veidiem (rakti, video, attēli) kopīgās metodes.
     */
    abstract class Content
    {

        public $item_row = null;
        public $tags_rows = null;
        public $block_guid = "";
        public $js_includes_arr = array();

        abstract function getHtml();

        abstract function getJS();

        abstract function getCSS();

        abstract protected function initContent();

        /**
         * Portāla satura konstruktors
         *         
         * @param  Object $item_row Portāla satura ieraksta rinda (no tabulas in_articles)
         * @return void
         */
        public function __construct($item_row)
        {
            $this->block_guid = Uuid::generate(4);
            $this->item_row = $item_row;
            $this->tags_rows = $this->getTags($item_row->id);

            $this->initContent();
        }

        /**
         * Izgūst attēlojamā ieraksta pievienotās iezīmes
         * 
         * @param integer @item_id Ieraksta ID no tabulas in_articles
         * @return Array Iezīmes no tabulas in_tags
         */
        private function getTags($item_id)
        {
            return DB::table('in_tags')
                            ->join('in_tags_article', 'in_tags.id', '=', 'in_tags_article.tag_id')
                            ->select(DB::raw("in_tags.name, in_tags.id"))
                            ->where('in_tags_article.article_id', $item_id)
                            ->take(Config::get('dx.max_tags_count'))
                            ->orderBy('in_tags_article.id', 'ASC')
                            ->get();
        }

        /**
         * Aizpilda masīvu ar JavaScript iekļāvumiem
         */
        public function addJSInclude($inc)
        {
            if (Request::ajax()) {
                return;
            }

            array_push($this->js_includes_arr, $inc);
        }

    }

}
