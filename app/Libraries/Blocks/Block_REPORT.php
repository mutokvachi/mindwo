<?php

namespace App\Libraries\Blocks;

use DB;
use App;
use Illuminate\Support\Facades\Auth;
use App\Libraries\DBHelper;

/**
 * Widget displays current user timeoff balance and provides possibility to request leave
 */
class Block_REPORT extends Block
{

    /**
     * Reports name
     * @var integer 
     */
    public $reports_name = 0;
    
    private $report;

    /**
     * Render widget and return its HTML.
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->report->getView();
    }

    /**
     * Returns JavaScript that calculates appropriate height of a widget.
     *
     * @return string
     */
    public function getJS()
    {
         $this->addJSInclude('js/elix_block_report.js');
    }

    /**
     * Returns widget's styles.
     *
     * @return string
     */
    public function getCSS()
    {
        return view('blocks.reports.report_css')->render();
    }

    public function getJSONData()
    {
        
    }

    protected function parseParams()
    {
        $dat_arr = explode('|', $this->params);

        foreach ($dat_arr as $item) {
            $val_arr = explode('=', $item);

            if ($val_arr[0] == "REPORT") {
                $this->reports_name = getBlockParamVal($val_arr);
            } else if (strlen($val_arr[0]) > 0) {
                throw new Exceptions\DXCustomException("Norādīts blokam neatbilstošs parametra nosaukums (" . $val_arr[0] . ")!");
            }
        }
        
        $this->initialize();
    }

    private function initialize()
    {
        $this->report = Reports\ReportFactory::initializeReport($this->reports_name);
    }
}

?>