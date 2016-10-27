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
        $sectionClasses = apply_filters('ModularityOnePage/section_class', array(
            'modularity-onepage-section',
            'modularity-onepage-section-%1$d'
        ), $section->ID);

        $sectionClasses = implode(' ', $sectionClasses);
        $sectionClasses = sprintf(
            $sectionClasses,
            $section->ID // %1$i
        );

        // Section start
        $markup = apply_filters(
            'ModularityOnePage/before_section',
            '<section class="' . $sectionClasses . '"><div class="container">'
        );

        // Title and content
        $markup .= apply_filters('ModularityOnePage\before_content', '<div class="grid"><div class="grid-md-12"><article>');
        $markup .= '<h1 class="modularity-onepage-section-title">'  . apply_filters('the_title', $section->post_title) . '</h1>';
        $markup .= apply_filters('the_content', $section->post_content);
        $markup .= apply_filters('ModularityOnePage\after_content', '</article></div></div>');

        // Modules
        $markup .= apply_filters('ModularityOnePage\before_modules', '<div class="grid">');
        $markup .= self::renderModules($section->ID);
        $markup .= apply_filters('ModularityOnePage\after_modules', '</div>');

        // Section end
        $markup .= apply_filters(
            'ModularityOnePage/after_section',
            '</div></section>'
        );

        return $markup;
    }

    public static function renderModules($postId)
    {
        global $wp_registered_sidebars;

        $markup = null;
        $modules = \Modularity\Editor::getPostModules($postId);

        if (!isset($modules['onepage-sidebar'])) {
            $modules = false;
        }

        $modules = $modules['onepage-sidebar']['modules'];

        if (is_array($modules) && !empty($modules)) {

            $i = 0;
            foreach ($modules as $module) {
                $i++;

                if ($i !== 1) {
                    $markup .= "\n\n";
                }

                $markup .= \Modularity\App::$display->outputModule($module, $wp_registered_sidebars['onepage-sidebar'], array(), false);
            }

        }

        return $markup;
    }
}
