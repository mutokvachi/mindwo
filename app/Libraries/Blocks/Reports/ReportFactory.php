<?php

namespace App\Libraries\Blocks\Reports;

/**
 * Initializes reports widget class
 */
class ReportFactory
{
    /**
     * Inicializes report
     *
     * @param string $report_name Report's name
     * @return void
     */
    public static function initializeReport($report_name)
    {
        // Fixes cases
        $report_name = strtoupper($report_name);

        // Prepares class name
        $class = "App\\Libraries\\Blocks\\Reports\\Report_" . $report_name;

        // Checks if class exists
        if (!is_subclass_of($class, "App\\Libraries\\Blocks\\Reports\\Report")) {
            throw new \Exception('Report "' . $report_name . '" does not exist.');
        }

        // Creates reports class
        return new $class($report_name);
    }
}