<?php

namespace App\Http\Controllers;

use \App\Exceptions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use Webpatser\Uuid\Uuid;
use Config;
use stdClass;

/**
 *
 * Rakstu kontrolieris
 * Realizē rakstu parādīšanu, atrašanu pēc iezīmes, meklēšanu
 *
 */
class ArticlesController extends Controller
{

    /**
     * Attēlo rakstus kas atbilst iezīmei
     * 
     * @param   Request $request GET pieprasījuma objekts
     * @param   mixed $tag_id Iezīmes identifkators (in_tags.id)
     * @return  Response                    HTML lapa
     */
    public function showTagArticles(Request $request, $tag_id)
    {
        $type_id = $request->input('type', 0);

        $item = $this->getTagItemRow($tag_id);

        $types = $this->getTagArticlesTypes($tag_id);

        $articles = get_article_query()
                ->leftJoin('in_tags_article', 'in_tags_article.article_id', '=', 'in_articles.id')
                ->where('in_tags_article.tag_id', '=', $tag_id)
                ->where(function ($query) use ($type_id)
                {
                    if ($type_id > 0) {
                        $query->where('in_articles.type_id', '=', $type_id);
                    }
                })
                ->where('in_articles.is_active', '=', 1)
                ->where('in_articles.is_searchable', '=', 1)
                ->orderBy('in_articles.publish_time', 'DESC')
                ->simplePaginate(Config::get('dx.feeds_page_rows_count'));

        $this->prepareArticleTags($articles);
        $block_guid = Uuid::generate(4);

        $mode = 'tags';
        $criteria = $item->name;
        $page_title = "Iezīmei atbilstošās ziņas";

        $date_from = '';
        $date_to = '';

        return view('pages.articles', compact('articles', 'page_title', 'criteria', 'mode', 'block_guid', 'types', 'type_id', 'tag_id', 'date_from', 'date_to'));
    }

    /**
     * Attēlo rakstus kas atbilst datu avota raksturīgajai iezīmei
     * 
     * @param   Request $request    GET pieprasījuma objekts
     * @param   mixed $tag_id       Iezīmes identifkators (in_tags.id)
     * @return  Response            HTML lapa
     */
    public function showSourceArticles(Request $request, $tag_id)
    {
        $type_id = $request->input('type', 0);

        $item = $this->getTagItemRow($tag_id);

        $types = $this->getSourceArticlesTypes($tag_id);

        $articles = get_article_query()
                ->where('in_sources.tag_id', '=', $tag_id)
                ->where(function ($query) use ($type_id)
                {
                    if ($type_id > 0) {
                        $query->where('in_articles.type_id', '=', $type_id);
                    }
                })
                ->where('in_articles.is_active', '=', 1)
                ->where('in_articles.is_searchable', '=', 1)
                ->orderBy('in_articles.publish_time', 'DESC')
                ->simplePaginate(Config::get('dx.feeds_page_rows_count'));

        $this->prepareArticleTags($articles);
        $block_guid = Uuid::generate(4);

        $mode = 'tags';
        $criteria = $item->name;
        $page_title = trans('article.browser_title_tags');

        $date_from = '';
        $date_to = '';

        return view('pages.articles', compact('articles', 'page_title', 'criteria', 'mode', 'block_guid', 'types', 'type_id', 'tag_id', 'date_from', 'date_to'));
    }

    /**
     * Meklēšana rakstos
     * @param       Request $request GET/POST pieprasījuma objekts
     * @return      Response                HTML lapa
     */
    public function searchArticle(Request $request)
    {
        $criteria = trim($request->input('criteria', ''));
        $type_id = $request->input('type', 0);

        $date_from = $request->input('pick_date_from', '');
        $date_to = $request->input('pick_date_to', '');

        $types = $this->getArticlesTypes($date_from, $date_to, $criteria);

        $articles = get_article_query()
                ->where(function ($query) use ($date_from, $date_to)
                {
                    if (strlen($date_from) > 0 && strlen($date_to) > 0) {
                        $query->whereDate('in_articles.publish_time', '>=', $date_from)
                        ->whereDate('in_articles.publish_time', '<=', $date_to);
                    }
                    else {
                        $query->whereDate('in_articles.publish_time', '<=', date('Y-n-d'));
                    }
                })
                ->where('in_articles.is_active', '=', 1)
                ->where('in_articles.is_searchable', '=', 1)
                ->where(function ($query) use ($type_id)
                {
                    if ($type_id > 0) {
                        $query->where('in_articles.type_id', '=', $type_id);
                    }
                })
                ->where(function ($query) use (&$criteria)
                {
                    if (strlen($criteria) > 0) {
                        $query->where('in_articles.title', 'LIKE', '%' . $criteria . '%')
                        ->orWhere('in_articles.article_text_dx_clean', 'LIKE', '%' . $criteria . '%')
                        ->orWhere('in_articles.intro_text', 'like', '%' . $criteria . '%');
                    }
                })
                ->orderBy('in_articles.publish_time', 'DESC')
                ->simplePaginate(Config::get('dx.feeds_page_rows_count'));

        $this->prepareArticleTags($articles);

        $mode = 'search';
        $block_guid = Uuid::generate(4);

        $tag_id = 0;
        $page_title = trans('article.browser_title');
        return view('pages.articles', compact('articles', 'page_title', 'criteria', 'mode', 'picker_from_js', 'picker_from_html', 'picker_to_js', 'picker_to_html', 'block_guid', 'types', 'type_id', 'tag_id', 'date_from', 'date_to'));
    }

    /**
     * Atgriež raksta iezīmes rindu pēc norādītā ID
     * 
     * @param integer $tag_id Iezīmes ID
     * @return Object Iezīmes rinda (no tabulas in_tags)
     * @throws Exceptions\DXCustomException
     */
    private function getTagItemRow($tag_id)
    {
        $item = DB::table('in_tags')
                ->where('id', '=', $tag_id)
                ->first();

        if (!$item) {
            throw new Exceptions\DXCustomException("Iezīme ar id " . $tag_id . " nav atrasta!");
        }

        return $item;
    }

    /**
     * Izgūst rakstu tipu masīvu
     * Saskaita katram norādītās iezīmes raksta tipam atbilstošo ierakstu skaitu
     * 
     * @param integer $tag_id Iezīmes ID
     * @return Array Rakstu tipu masīvs
     */
    private function getTagArticlesTypes($tag_id)
    {
        $types = $this->getTypes();

        foreach ($types as $type) {

            $type->count = get_article_query()
                            ->leftJoin('in_tags_article', 'in_tags_article.article_id', '=', 'in_articles.id')
                            ->where('in_articles.is_active', '=', 1)
                            ->where('in_articles.is_searchable', '=', 1)
                            ->where('in_tags_article.tag_id', '=', $tag_id)
                            ->where(function ($query) use ($type)
                            {
                                if ($type->id > 0) {
                                    $query->where('in_articles.type_id', '=', $type->id);
                                }
                            })->count();
        }

        return $types;
    }

    /**
     * Izgūst rakstu tipu masīvu
     * Saskaita katram norādītās datu avota raksturīgās iezīmes raksta tipam atbilstošo ierakstu skaitu
     * 
     * @param integer $tag_id Iezīmes ID
     * @return Array Rakstu tipu masīvs
     */
    private function getSourceArticlesTypes($tag_id)
    {
        $types = $this->getTypes();

        foreach ($types as $type) {

            $type->count = get_article_query()
                            ->where('in_articles.is_active', '=', 1)
                            ->where('in_articles.is_searchable', '=', 1)
                            ->where('in_sources.tag_id', '=', $tag_id)
                            ->where(function ($query) use ($type)
                            {
                                if ($type->id > 0) {
                                    $query->where('in_articles.type_id', '=', $type->id);
                                }
                            })->count();
        }

        return $types;
    }

    /**
     * Izgūst rakstu tipu masīvu
     * Saskaita katram raksta tipam atbilstošo ierakstu skaitu
     * 
     * @param DateTime $d_from  Raksta publicēšanas datums no
     * @param DateTime $d_to    Raksta publicēšanas datums līdz
     * @param string $criteria  Meklēšanas kritērijs
     * @return Array Rakstu tipu masīvs
     */
    private function getArticlesTypes($date_from, $date_to, $criteria)
    {
        $types = $this->getTypes();

        foreach ($types as $type) {

            $type->count = get_article_query()
                            ->where(function ($query) use ($date_from, $date_to)
                            {
                                if (strlen($date_from) > 0 && strlen($date_to) > 0) {
                                    $query->whereDate('in_articles.publish_time', '>=', $date_from)
                                    ->whereDate('in_articles.publish_time', '<=', $date_to);
                                }
                                else {
                                    $query->whereDate('in_articles.publish_time', '<=', date('Y-n-d'));
                                }
                            })
                            ->where('in_articles.is_active', '=', 1)
                            ->where('in_articles.is_searchable', '=', 1)
                            ->where(function ($query) use ($type)
                            {
                                if ($type->id > 0) {
                                    $query->where('in_articles.type_id', '=', $type->id);
                                }
                            })
                            ->where(function ($query) use (&$criteria)
                            {
                                $query->where('in_articles.title', 'LIKE', '%' . $criteria . '%')
                                ->orWhere('in_articles.article_text', 'LIKE', '%' . $criteria . '%')
                                ->orWhere('in_articles.intro_text', 'like', '%' . $criteria . '%');
                            })->count();
        }

        return $types;
    }

    /**
     * Izveido tipu "Visi"
     * Tips jāizveido no koda, jo datu bāzes tabulā nav tāda ieraksta
     *
     * @return stdClass Tips "Visi"
     */
    private function getAllRecordsType()
    {
        $nt = new stdClass();

        $nt->id = 0;
        $nt->count = 0;
        $nt->name = trans('article.all_types');
        $nt->picture_name = '';

        return $nt;
    }

    /**
     * Izgūst ziņu tipu klasifikatoru un pievieno vēl vienu tipu - "Visi"
     *
     * @return stdClass Tips "Visi"
     */
    private function getTypes()
    {
        $types = DB::table('in_article_types')->get();

        array_unshift($types, $this->getAllRecordsType());

        return $types;
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
                        ->select(DB::raw("in_tags.name, in_tags.id"))
                        ->whereIn('in_tags.id', $item->tag_ids)
                        ->take(Config::get('dx.max_tags_count'))
                        ->orderBy('name', 'ASC')
                        ->get();
            }
        });
    }

}
