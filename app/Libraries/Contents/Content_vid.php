<?php

namespace App\Libraries\Contents
{

    use DB;

    class Content_vid extends Content
    {

        /**
         *
         * Portāla video galerijas klase
         * Objekts nodrošina portāla video galerijas informācijas attēlošanu
         * Tiek izmantota sekojoša HTML5 video komponente: https://html5box.com/html5gallery/
         *
         */
        private $video_rows = null;

        /**
         * Izgūst video galerijas HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            return view('blocks.video_gallery', [
                        'item' => $this->item_row,
                        'video_rows' => $this->video_rows,
                        'tags' => $this->tags_rows,
                        'avatar' => get_portal_config('EMTY_VIDEO_AVATAR'),
                    ])->render();
        }

        /**
         * Izgūst video galerijas JavaScript
         * 
         * @return string Bloka JavaScript loģika
         */
        public function getJS()
        {
            return ""; //view('blocks.images_gallery_js')->render();
        }

        /**
         * Izgūst video galerijas CSS
         * 
         * @return string Bloka CSS
         */
        public function getCSS()
        {
            return "";//view('blocks.images_gallery_css')->render();
        }

        /**
         * Inicializē satura objektu
         * 
         * @return void
         */
        protected function initContent()
        {
            $this->video_rows = DB::table('in_articles_vid')->where('article_id', '=', $this->item_row->id)->orderBy('order_index', 'ASC')->get();
            $err_count = 0;
            
            foreach($this->video_rows as $video)
            {
                $video->youtube_code = "";
                if ($video->youtube_url)
                {
                    $video->youtube_code = getYoutubeID($video->youtube_url);
                    
                    if (strlen($video->youtube_code) == 0)
                    {
                        $err_count++;
                    }
                }
            }
            
            if ($err_count == count($this->video_rows))
            {
                $this->video_rows = null;
            }
            
            $this->addJSInclude('plugins/html5_gallery/html5gallery/html5gallery.js');
        }

    }

}