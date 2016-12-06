<?php

namespace App\Libraries\Blocks\TaskListViews
{

    use App\Exceptions;

    /**
     * Tasks data view factory class
     */
    class TaskListViewFactory
    {

        /**
         * Class builder
         * @param string $view_code Task view class code
         * @return \App\Libraries\Blocks\TaskListViews\class
         * @throws Exceptions\DXCustomException
         */
        public static function build_taskview($view_code)
        {
            $class = "App\\Libraries\\Blocks\\TaskListViews\\TaskListView_" . $view_code;

            if (!class_exists($class)) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.unsupported_factory_class'), $view_code));
            }

            return new $class();
        }

    }

}