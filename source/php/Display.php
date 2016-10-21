<?php

namespace ModularityOnePage;

class Display
{
    public static function output()
    {
        $output = null;
        $postStatus = array('publish');

        if (is_user_logged_in()) {
            $postStatus[] = 'private';
        }

        $sections = get_posts(array(
            'post_type' => 'onepage',
            'post_status' => $postStatus,
            'orderby' => 'menu_order',
            'order' => 'asc'
        ));

        foreach ($sections as $section) {
            $output .= self::renderSection($section);
        }

        return $output;
    }

    public static function renderSection($section)
    {
        global $wp_registered_sidebars;

        $sectionClasses = apply_filters('ModularityOnePage/section_class', array(
            'modularity-onepage-section',
            'modularity-onepage-section-%1$d'
        ));

        $sectionClasses = implode(' ', $sectionClasses);
        $sectionClasses = sprintf(
            $sectionClasses,
            $section->ID // %1$i
        );

        $markup = apply_filters(
            'ModularityOnePage/before_section',
            '<section class="' . $sectionClasses . '"><div class="container"><div class="grid">'
        );
        $modules = \Modularity\Editor::getPostModules($section->ID);
        if (!isset($modules['onepage-sidebar'])) {
            $modules = false;
        }

        $modules = $modules['onepage-sidebar']['modules'];

        $i = 0;
        foreach ($modules as $module) {
            $i++;
            if ($i !== 1) {
                $markup .= "\n\n";
            }
            $markup .= \Modularity\App::$display->outputModule($module, $wp_registered_sidebars['onepage-sidebar'], array(), false);
        }

        $markup .= apply_filters(
            'ModularityOnePage/after_section',
            '</div></div></section>'
        );

        return $markup;
    }
}
