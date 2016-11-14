<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exceptions;

class SearchController  extends Controller
{

    /**
   *
   * Meklēšanas kontrolieris
   *
   *
   * Realizē rakstu, dokumentu, darbinieku meklēšanu
   *
   */

    /**
     * Meklēšanas realizācija
     * @param       Request $request GET/POST pieprasījuma objekts
     * @return      Response                HTML lapa
     */
    public function search(Request $request)
    {
        $search_type = $request->input('searchType', trans("search_top.employees"));
        
        switch ($search_type) {
            case trans("search_top.employees"):
                return (new EmployeeController)->searchEmployee($request);
            case trans("search_top.documents"):
                return (new DocumentsController)->searchDocument($request);
            case trans("search_top.news"):
                return (new ArticlesController)->searchArticle($request);
            default:
                throw new Exceptions\DXCustomException(trans("search_top.news") . " (" . $search_type . ")!");
        }
    }

} 