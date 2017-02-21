<?php
/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 15.12.16, 16:19
 */

namespace App\Libraries\Blocks;

use App\Models\Department;
use App\Models\Source;
use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class Block_EMPLBYDEP
 *
 * Widget that displays employees count by department.
 *
 * @package App\Libraries\Blocks
 */
class Block_EMPLOYEE_COUNT extends Block
{
    /**
     * Groups name
     * @var integer 
     */
    public $group_name = '';

    /**
     * Widgets class
     * @var EmployeeCount\EmployeeCount 
     */
    private $widget;

    /**
     * Render widget.
     * @return string
     */
    function getHtml()
    {
        return $this->widget->getView(new \DateTime());
    }

    function getJS()
    {
        // TODO: Implement getJS() method.
    }

    function getCSS()
    {
        return <<<END
			<style>
				.dx-widget-employeecount .progress {
					position: relative;
					background-color: #ddd;
				}
				.dx-widget-employeecount .progress, .dx-widget-employeecount .progress-bar {
					height: 20px;
				}
				.dx-widget-employeecount .progress-bar span, .dx-widget-employeecount .progress-bar a {
					color: #26344b;
					display: block;
					position: absolute;
					text-align: left;
					margin-left: 20px;
				}
        
                                .dx-widget-employeecount-filter, .dx-widget-employeecount-filter:hover{
                                   
                                }
			</style>
END;
    }

    function getJSONData()
    {
        // TODO: Implement getJSONData() method.
    }

    protected function parseParams()
    {
        $dat_arr = explode('|', $this->params);

        foreach ($dat_arr as $item) {
            $val_arr = explode('=', $item);

            if ($val_arr[0] == "GROUP") {
                $this->group_name = getBlockParamVal($val_arr);
            } else if (strlen($val_arr[0]) > 0) {
                throw new Exceptions\DXCustomException("Invalid block parameter's name (" . $val_arr[0] . ")!");
            }
        }

        $this->widget = EmployeeCount\EmployeeCountFactory::initializeWidget($this->group_name);
    }
}