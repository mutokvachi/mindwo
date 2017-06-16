<?php

namespace mindwo\pages\Blocks\Contents
{

    use DB;

    /**
     * Portāla attēlu galerijas klase.
     * Objekts nodrošina portāla attēlu galerijas informācijas attēlošanu.
     */
    class Content_img extends Content
    {

        private $images_rows = null;

        /**
         * Izgūst attēlu galerijas HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            return view('mindwo/pages::blocks.images_gallery', [
                        'item' => $this->item_row,
                        'images_rows' => $this->images_rows,
                        'tags' => $this->tags_rows
                    ])->render();
        }

        /**
         * Izgūst attēlu galerijas JavaScript
         * 
         * @return string Bloka JavaScript loģika
         */
        public function getJS()
        {
            return view('mindwo/pages::blocks.images_gallery_js')->render();
        }

        /**
         * Izgūst attēlu galerijas CSS
         * 
         * @return string Bloka CSS
         */
        public function getCSS()
        {
            return view('mindwo/pages::blocks.images_gallery_css')->render();
        }

        /**
         * Inicializē satura objektu
         * 
         * @return void
         */
        protected function initContent()
        {
            $this->images_rows = DB::table('in_articles_img')->where('article_id', '=', $this->item_row->id)->orderBy('order_index', 'ASC')->orderBy('file_name', 'ASC')->get();

            $this->addJSInclude('mindwo/plugins/blueimp-gallery/js/jquery.blueimp-gallery.min.js');
        }

    }

}