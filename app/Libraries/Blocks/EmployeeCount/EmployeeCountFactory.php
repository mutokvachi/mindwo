<?php

namespace App\Libraries\Blocks\EmployeeCount;

/**
 * Initializes employee count by group widget class
 */
class EmployeeCountFactory
{
    /**
     * Initializes employee count by group
     *
     * @param string $widget_name Wdigets's name
     * @return void
     */
    public static function initializeWidget($widget_name)
    {
        // Fixes cases
        $widget_name = strtoupper($widget_name);

        // Prepares class name
        $class = "App\\Libraries\\Blocks\\EmployeeCount\\EmployeeCount_" . $widget_name;

        // Checks if class exists
        if (!is_subclass_of($class, "App\\Libraries\\Blocks\\EmployeeCount\\EmployeeCount")) {
            throw new \Exception('Employee count by group widget "' . $widget_name . '" does not exist.');
        }

        // Creates employee count by group class
        return new $class($widget_name);
    }
}