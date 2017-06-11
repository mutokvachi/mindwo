<?php

namespace mindwo\pages\Blocks\Contents
{

    use DB;

    /**
     * Portāla raksta (ziņas) klase.
     * Objekts nodrošina portāla ziņas (raksta) informācijas attēlošanu.
     */
    class Content_text extends Content
    {

        /**
         * Masīvs ar video un attēlu galerijas ierakstiem
         * 
         * @var Array 
         */
        private $video_rows = null;

        /**
         * Masīvs ar ziņai pievienotajām datnēm
         * 
         * @var Array 
         */
        private $files_rows = null;

        /**
         * Ziņas autors - masīvs ar darbinieka informāciju
         * 
         * @var Array 
         */
        private $author_row = null;

        /**
         * Izgūst raksta HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            return view('mindwo/pages::blocks.article', [
                        'item' => $this->item_row,
                        'block_guid' => $this->block_guid,
                        'tags' => $this->tags_rows,
                        'video_rows' => $this->video_rows,
                        'avatar' => get_portal_config('EMTY_VIDEO_AVATAR'),
                        'files_rows' => $this->files_rows,
                        'author_row' => $this->author_row,
                        'click2call_url' => get_portal_config('CLICK2CALL_URL'),
                        'fixed_phone_part' => get_portal_config('CLICK2CALL_INNER_PHONE'),
                    ])->render();
        }

        /**
         * Izgūst raksta JavaScript
         * 
         * @return string Bloka JavaScript loģika
         */
        public function getJS()
        {
            return "";
        }

        /**
         * Izgūst raksta CSS
         * 
         * @return string Bloka CSS
         */
        public function getCSS()
        {
            return view('mindwo/pages::blocks.feed_articles_css')->render();
        }

        /**
         * Inicializē satura objektu - atlasa pievienotās video un attēlu galerijas, ja ir
         * 
         * @return void
         */
        protected function initContent()
        {

            $this->video_rows = $this->getMediaFiles();

            if (count($this->video_rows) > 0) {
                $this->addJSInclude('mindwo/plugins/html5_gallery/html5gallery/html5gallery.js');
            }

            $this->addJSInclude('mindwo/blocks/employees_links.js');
            $this->addJSInclude('mindwo/blocks/content_text.js');

            $this->files_rows = DB::table('in_articles_files')->where('article_id', '=', $this->item_row->id)->orderBy('order_index', 'ASC')->get();

            $this->author_row = $this->getAuthorInfo();
        }

        /**
         * Izgūst informāciju par raksta autoru, ja tas ir norādīts
         * @return mixed Atgriež null, ja nav autora, vai masīvu ar autora datiem
         */
        private function getAuthorInfo()
        {

            if (!$this->item_row->author_id) {
                return null; // rakstam nav autora
            }

            return DB::table('in_employees as e')
                            ->select(DB::raw('
                                e.id, 
                                e.employee_name, 
                                e.phone, 
                                e.email, 
                                e.position, 
                                e.picture_guid, 
                                e.source_id, 
                                s.title as company_name,
                                s.icon_class as source_icon,
                                d.title as department_name
                                '))
                            ->leftJoin('in_sources as s', 'e.source_id', '=', 's.id')
                            ->leftJoin('in_departments as d', 'e.department_id', '=', 'd.id')
                            ->where('e.id', '=', $this->item_row->author_id)
                            ->first();
        }

        /**
         * Izgūst ziņai piesaistīto attēlu un video galerijas masīvā
         * 
         * @return Array Masīvs ar attēlu/video galerijas datnēm
         */
        private function getMediaFiles()
        {
            $arr_vid = null;
            $arr_pic = null;

            if ($this->item_row->video_galery_id) {
                $arr_vid = DB::table('in_articles_vid')
                                ->select(DB::raw("title, youtube_url, prev_file_guid, file_guid"))
                                ->where('article_id', '=', $this->item_row->video_galery_id)->orderBy('order_index', 'ASC');
            }

            if ($this->item_row->picture_galery_id) {
                $arr_pic = DB::table('in_articles_img')
                                ->select(DB::raw("file_name as title, null as youtube_url, file_guid as prev_file_guid, file_guid"))
                                ->where('article_id', '=', $this->item_row->picture_galery_id)->orderBy('order_index', 'ASC');
            }

            $arr_mix = ($arr_vid) ? (($arr_pic) ? $arr_vid->union($arr_pic) : $arr_vid) : $arr_pic;

            if (!$arr_mix) {
                return null;
            }

            $rows = $arr_mix->get();

            $err_count = 0;

            foreach ($rows as $video) {
                $video->youtube_code = "";
                if ($video->youtube_url) {
                    $video->youtube_code = getYoutubeID($video->youtube_url);

                    if (strlen($video->youtube_code) == 0) {
                        $err_count++;
                    }
                }
            }

            if ($err_count == count($rows)) {
                $rows = null;
            }

            return $rows;
        }

    }

}