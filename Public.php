<?php

if (!function_exists('ModularityOnePage')) {
    function ModularityOnePage($echo = true)
    {
        $output = \ModularityOnePage\Display::output();

        if ($echo) {
            echo $output;
        }

        return $output;
    }
}
