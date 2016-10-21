<?php

namespace ModularityOnePage;

class App
{
    public function __construct()
    {
        add_action('init', array($this, 'registerPostType'));
        add_action('widgets_init', array($this, 'registerSidebar'));
        add_filter('Modularity/CoreTemplatesSearchPaths', array($this, 'addTemplateSearchPaths'));

        add_filter('template_include', array($this, 'singleTemplate'));
    }

    /**
     * Get the single template for onepage single posts
     * @param  string $template Default template
     * @return string           Template to use
     */
    public function singleTemplate($template)
    {
        $queriedObject = get_queried_object();

        if (isset($queriedObject->post_type) && $queriedObject->post_type === 'onepage' && is_single()) {
            return MODULARITY_ONEPAGE_TEMPLATE_PATH . 'single-onepage.php';
        }

        return $template;
    }


    public function registerSidebar()
    {
        register_sidebar(array(
            'id'            => 'onepage-sidebar',
            'name'          => __('Onepage sidebar (Modularity Onepage)', 'modularity-onepage'),
            'description'   => __('The onepage sidebar area', 'modularity-onepage'),
            'before_widget' => apply_filters('ModularityOnePage/before_widget', '<div class="grid-sm-12 %2$s">'),
            'after_widget'  => apply_filters('ModularityOnePage/after_widget', '</div>'),
            'before_title'  => apply_filters('ModularityOnePage/before_title', '<h3>'),
            'after_title'   => apply_filters('ModularityOnePage/after_title', '</h3>')
        ));

        $options = get_option('modularity-options');

        if (!isset($options['enabled-areas'])) {
            $options['enabled-areas'] = array();
        }

        if (!isset($options['enabled-areas']['single-onepage'])) {
            $options['enabled-areas']['single-onepage'] = array();
        }

        $options['enabled-areas']['single-onepage'][] = 'onepage-sidebar';
        update_option('modularity-options', $options);
    }

    /**
     * Puts the onepage post type in the list of activated post types in modularity
     * @return void
     */
    public function putInModularitySettings()
    {
        $options = get_option('modularity-options');
        if (!isset($options['enabled-post-types'])) {
            $options['enabled-post-types'] = array();
        }

        if (!in_array('onepage', $options['enabled-post-types'])) {
            $options['enabled-post-types'][] = 'onepage';
        }

        update_option('modularity-options', $options);
    }

    /**
     * Creates the onepage sections post type
     * @return void
     */
    public function registerPostType()
    {
        $nameSingular = __('Onepage section', 'modularity-onepage');
        $namePlural = __('Onepage sections', 'modularity-onepage');

        $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjgwcHgiIGhlaWdodD0iOTNweCIgdmlld0JveD0iMCAwIDgwIDkzIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCA0MCAoMzM3NjIpIC0gaHR0cDovL3d3dy5ib2hlbWlhbmNvZGluZy5jb20vc2tldGNoIC0tPgogICAgPHRpdGxlPmljb248L3RpdGxlPgogICAgPGRlc2M+Q3JlYXRlZCB3aXRoIFNrZXRjaC48L2Rlc2M+CiAgICA8ZGVmcz48L2RlZnM+CiAgICA8ZyBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgICAgICA8ZyBpZD0iaWNvbiIgZmlsbD0iIzAwMDAwMCI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0zOS44NDEsMTUuNDA4IEMzNy40MDIsMTUuNDA4IDM1LjQyLDE3LjM4NyAzNS40MiwxOS44MjkgQzM1LjQyLDIyLjI3MSAzNy40MDEsMjQuMjUxIDM5Ljg0MSwyNC4yNTEgQzQyLjI4MywyNC4yNTEgNDQuMjYzLDIyLjI3MiA0NC4yNjMsMTkuODI5IEM0NC4yNjMsMTcuMzg2IDQyLjI4NCwxNS40MDggMzkuODQxLDE1LjQwOCBMMzkuODQxLDE1LjQwOCBaIE00MS41NzksMjAuMjI3IEwzOS42NjcsMjIuMjEyIEMzOS41NzMsMjIuMzIxIDM5LjQzMywyMi4zOTEgMzkuMjc2LDIyLjM5MSBDMzguOTkxLDIyLjM5MSAzOC43NTcsMjIuMTU5IDM4Ljc1NywyMS44NzIgTDM4Ljc1NywxNy43ODcgQzM4Ljc1NywxNy41MDEgMzguOTkxLDE3LjI2OCAzOS4yNzYsMTcuMjY4IEMzOS40MzIsMTcuMjY4IDM5LjU3MiwxNy4zMzggMzkuNjY3LDE3LjQ0NyBMNDEuNTc5LDE5LjQzMyBDNDEuNjk0LDE5LjUyOCA0MS43NjYsMTkuNjcxIDQxLjc2NiwxOS44MjkgQzQxLjc2NSwxOS45OSA0MS42OTMsMjAuMTMyIDQxLjU3OSwyMC4yMjcgTDQxLjU3OSwyMC4yMjcgWiBNNzAuMTUzLDc1LjU4NSBMNTEuMzUxLDc1LjU4NSBMNTEuMzUxLDg0LjExMiBMNzAuMTUzLDg0LjExMiBMNzAuMTUzLDc1LjU4NSBMNzAuMTUzLDc1LjU4NSBaIE0yOC4zMTQsODIuNTI2IEw5LjUxNCw4Mi41MjYgTDkuNTE0LDg0LjExMyBMMjguMzEzLDg0LjExMyBMMjguMzEzLDgyLjUyNiBMMjguMzE0LDgyLjUyNiBaIE03My42OCwwLjEwNSBMNi4wMDUsMC4xMDUgQzYuMDA1LDAuMTA1IDAuMzIsMC4xMDUgMC4zMiw2LjY4MSBMMC4zMiw2OC40MDEgTDAuMzE5LDY3LjUwOSBMMC4zMTksODUuNDU0IEMwLjMyNCw4NS41MjggMC4zMTQsODcuMDU5IDEuMTI0LDg4LjY5NCBDMS44OTgsOTAuMzIyIDMuNzA0LDkyLjAzIDYuODEsOTIuMDMgTDcyLjc3OSw5Mi4wMyBDNzIuODU0LDkyLjAyNCA3NC4zODcsOTIuMDM1IDc2LjAyMiw5MS4yMjUgQzc3LjY2NCw5MC40NDMgNzkuMzg3LDg4LjYxMiA3OS4zNTYsODUuNDU0IEw3OS4zNTYsNjguMzkyIEw3OS4zNjYsNjkuMjgzIEw3OS4zNjYsNi42ODEgQzc5LjM2Niw2LjY4MSA3OS4zNjYsMC4xMDUgNzMuNjgsMC4xMDUgTDczLjY4LDAuMTA1IFogTTEwLjIzNiwxMCBMNzAuMTczLDEwIEw3MC4xNzMsMjkuNjYxIEwxMC4yMzYsMjkuNjYxIEwxMC4yMzYsMTAgTDEwLjIzNiwxMCBaIE00OC40MDcsNTAuNjM2IEw0OC40MDcsNDkuNDU1IEw3MC4xNzMsNDkuNDU1IEw3MC4xNzMsNTAuNjM2IEw0OC40MDcsNTAuNjM2IEw0OC40MDcsNTAuNjM2IFogTTcwLjE3Myw1NC4wNzMgTDcwLjE3Myw1NS4yNTUgTDQ4LjQwNyw1NS4yNTUgTDQ4LjQwNyw1NC4wNzMgTDcwLjE3Myw1NC4wNzMgTDcwLjE3Myw1NC4wNzMgWiBNNDguNDA3LDQ2LjI2NiBMNDguNDA3LDQ1LjA4NSBMNzAuMTczLDQ1LjA4NSBMNzAuMTczLDQ2LjI2NiBMNDguNDA3LDQ2LjI2NiBMNDguNDA3LDQ2LjI2NiBaIE0xMC4yMzUsMzMuODQxIEw2OS45MjQsMzMuODQxIEw2OS45MjQsMzUuMDIyIEwxMC4yMzUsMzUuMDIyIEwxMC4yMzUsMzMuODQxIEwxMC4yMzUsMzMuODQxIFogTTEwLjIzNSwzNi4xMSBMNjkuOTI0LDM2LjExIEw2OS45MjQsMzcuMjkxIEwxMC4yMzUsMzcuMjkxIEwxMC4yMzUsMzYuMTEgTDEwLjIzNSwzNi4xMSBaIE00NS44NTIsNTEuNDAyIEM0NS4xMDMsNTEuNDAyIDQ0LjQ5OCw1MC43OTYgNDQuNDk4LDUwLjA0NiBDNDQuNDk4LDQ5LjI5OCA0NS4xMDMsNDguNjkyIDQ1Ljg1Miw0OC42OTIgQzQ2LjYsNDguNjkyIDQ3LjIwOSw0OS4yOTkgNDcuMjA5LDUwLjA0NiBDNDcuMjExLDUwLjc5NSA0Ni42LDUxLjQwMiA0NS44NTIsNTEuNDAyIEw0NS44NTIsNTEuNDAyIFogTTQ3LjIxMSw1NC42NjQgQzQ3LjIxMSw1NS40MTMgNDYuNjAxLDU2LjAxOSA0NS44NTQsNTYuMDE5IEM0NS4xMDYsNTYuMDE5IDQ0LjUsNTUuNDEyIDQ0LjUsNTQuNjY0IEM0NC41LDUzLjkxNSA0NS4xMDUsNTMuMzA4IDQ1Ljg1NCw1My4zMDggQzQ2LjYwMSw1My4zMDggNDcuMjExLDUzLjkxNSA0Ny4yMTEsNTQuNjY0IEw0Ny4yMTEsNTQuNjY0IFogTTQ1Ljg1Miw0Ny4wMzEgQzQ1LjEwMyw0Ny4wMzEgNDQuNDk4LDQ2LjQyNCA0NC40OTgsNDUuNjc1IEM0NC40OTgsNDQuOTI2IDQ1LjEwMyw0NC4zMTkgNDUuODUyLDQ0LjMxOSBDNDYuNiw0NC4zMTkgNDcuMjA5LDQ0LjkyNyA0Ny4yMDksNDUuNjc1IEM0Ny4yMTEsNDYuNDI0IDQ2LjYsNDcuMDMxIDQ1Ljg1Miw0Ny4wMzEgTDQ1Ljg1Miw0Ny4wMzEgWiBNMTAuMjM1LDM4LjM4MSBMNDUuMzQyLDM4LjM4MSBMNDUuMzQyLDM5LjU2MSBMMTAuMjM1LDM5LjU2MSBMMTAuMjM1LDM4LjM4MSBMMTAuMjM1LDM4LjM4MSBaIE00MC40OTMsNDQuNTMxIEw0MC40OTMsNTUuNzY5IEwzOS44MTksNTUuNzY5IEwzOS44MTksNDQuNTMxIEw0MC40OTMsNDQuNTMxIEw0MC40OTMsNDQuNTMxIFogTTE0LjE0Myw1MC42MzYgTDE0LjE0Myw0OS40NTUgTDM1LjkxLDQ5LjQ1NSBMMzUuOTEsNTAuNjM2IEwxNC4xNDMsNTAuNjM2IEwxNC4xNDMsNTAuNjM2IFogTTM1LjkxLDU0LjA3MyBMMzUuOTEsNTUuMjU1IEwxNC4xNDMsNTUuMjU1IEwxNC4xNDMsNTQuMDczIEwzNS45MSw1NC4wNzMgTDM1LjkxLDU0LjA3MyBaIE0xNC4xNDMsNDYuMjY2IEwxNC4xNDMsNDUuMDg1IEwzNS45MSw0NS4wODUgTDM1LjkxLDQ2LjI2NiBMMTQuMTQzLDQ2LjI2NiBMMTQuMTQzLDQ2LjI2NiBaIE0xMS41OTIsNDQuMzE5IEMxMi4zNDEsNDQuMzE5IDEyLjk0Nyw0NC45MjYgMTIuOTQ3LDQ1LjY3NCBDMTIuOTQ3LDQ2LjQyNCAxMi4zNDEsNDcuMDMgMTEuNTkyLDQ3LjAzIEMxMC44NDMsNDcuMDMgMTAuMjM1LDQ2LjQyMyAxMC4yMzUsNDUuNjc0IEMxMC4yMzUsNDQuOTI2IDEwLjg0NCw0NC4zMTkgMTEuNTkyLDQ0LjMxOSBMMTEuNTkyLDQ0LjMxOSBaIE0xMS41OTIsNDguNjkgQzEyLjM0MSw0OC42OSAxMi45NDcsNDkuMjk3IDEyLjk0Nyw1MC4wNDQgQzEyLjk0Nyw1MC43OTUgMTIuMzQxLDUxLjQgMTEuNTkyLDUxLjQgQzEwLjg0Myw1MS40IDEwLjIzNSw1MC43OTQgMTAuMjM1LDUwLjA0NCBDMTAuMjM1LDQ5LjI5NiAxMC44NDQsNDguNjkgMTEuNTkyLDQ4LjY5IEwxMS41OTIsNDguNjkgWiBNMTEuNTkyLDUzLjMwOCBDMTIuMzQxLDUzLjMwOCAxMi45NDcsNTMuOTE1IDEyLjk0Nyw1NC42NjQgQzEyLjk0Nyw1NS40MTMgMTIuMzQxLDU2LjAxOSAxMS41OTIsNTYuMDE5IEMxMC44NDMsNTYuMDE5IDEwLjIzNSw1NS40MTIgMTAuMjM1LDU0LjY2NCBDMTAuMjM1LDUzLjkxNSAxMC44NDQsNTMuMzA4IDExLjU5Miw1My4zMDggTDExLjU5Miw1My4zMDggWiBNOS43NTEsNjAuNDY4IEM5Ljc1MSw1OS45IDEwLjMyLDU5LjkgMTAuMzIsNTkuOSBMNjkuNTg0LDU5LjkgQzcwLjE1Miw1OS45IDcwLjE1Miw2MC40NjggNzAuMTUyLDYwLjQ2OCBMNzAuMTUyLDYzLjc1MiBDNzAuMTUyLDY0LjMyIDY5LjU4NCw2NC4zMiA2OS41ODQsNjQuMzIgTDEwLjMyLDY0LjMyIEM5Ljc1MSw2NC4zMiA5Ljc1MSw2My43NTIgOS43NTEsNjMuNzUyIEw5Ljc1MSw2MC40NjggTDkuNzUxLDYwLjQ2OCBaIE03Ny41NzQsODUuNDU0IEM3Ny41NDUsODcuOTggNzYuNDI4LDg4Ljk5MSA3NS4yMjMsODkuNjMyIEM3NC42MTksODkuOTM1IDczLjk5MSw5MC4wOTUgNzMuNTIzLDkwLjE3MSBDNzMuMDUzLDkwLjI1IDcyLjc3OSw5MC4yNDMgNzIuNzc5LDkwLjI0NyBMNi44OTUsOTAuMjQ3IEM0LjM2OSw5MC4yMTcgMy4zNTcsODkuMDk5IDIuNzE4LDg3Ljg5NyBDMi40MTYsODcuMjkzIDIuMjU1LDg2LjY2NCAyLjE3OCw4Ni4xOTYgQzIuMDk4LDg1LjcyNiAyLjEwNyw4NS40NTQgMi4xMDIsODUuNDU0IEwyLjEwMiw2OS4yOTIgTDc3LjU3Myw2OS4yOTIgTDc3LjU3Myw4NS40NTQgTDc3LjU3NCw4NS40NTQgWiBNMjguMzE0LDc1LjU4NSBMOS41MTQsNzUuNTg1IEw5LjUxNCw3Ny4xNzMgTDI4LjMxMyw3Ny4xNzMgTDI4LjMxMyw3NS41ODUgTDI4LjMxNCw3NS41ODUgWiBNNDkuMjMzLDc1LjU4NSBMMzAuNDMzLDc1LjU4NSBMMzAuNDMzLDg0LjExMiBMNDkuMjMzLDg0LjExMiBMNDkuMjMzLDc1LjU4NSBMNDkuMjMzLDc1LjU4NSBaIE0yOC4zMTQsNzcuODk5IEw5LjUxNCw3Ny44OTkgTDkuNTE0LDc5LjQ4NSBMMjguMzEzLDc5LjQ4NSBMMjguMzEzLDc3Ljg5OSBMMjguMzE0LDc3Ljg5OSBaIE0yOC4zMTQsODAuMjEyIEw5LjUxNCw4MC4yMTIgTDkuNTE0LDgxLjc5OSBMMjguMzEzLDgxLjc5OSBMMjguMzEzLDgwLjIxMiBMMjguMzE0LDgwLjIxMiBaIiBpZD0iU2hhcGUiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==';

        $labels = array(
            'name'               => $nameSingular,
            'singular_name'      => $nameSingular,
            'menu_name'          => $namePlural,
            'name_admin_bar'     => $nameSingular,
            'add_new'            => _x('Add Section', 'add new button', 'municipio-intranet'),
            'add_new_item'       => sprintf(__('Add new %s', 'municipio-intranet'), $nameSingular),
            'new_item'           => sprintf(__('New %s', 'municipio-intranet'), $nameSingular),
            'edit_item'          => sprintf(__('Edit %s', 'municipio-intranet'), $nameSingular),
            'view_item'          => sprintf(__('View %s', 'municipio-intranet'), $nameSingular),
            'all_items'          => sprintf(__('All %s', 'municipio-intranet'), $namePlural),
            'search_items'       => sprintf(__('Search %s', 'municipio-intranet'), $namePlural),
            'parent_item_colon'  => sprintf(__('Parent %s', 'municipio-intranet'), $namePlural),
            'not_found'          => sprintf(__('No %s', 'municipio-intranet'), $namePlural),
            'not_found_in_trash' => sprintf(__('No %s in trash', 'municipio-intranet'), $namePlural)
        );

        $args = array(
            'labels'               => $labels,
            'description'          => __('Onepage content', 'modularity-onepage'),
            'menu_icon'            => $icon,
            'public'               => true,
            'publicly_queriable'   => false,
            'show_ui'              => true,
            'show_in_nav_menus'    => false,
            'menu_position'        => 350,
            'has_archive'          => true,
            'hierarchical'         => false,
            'exclude_from_search'  => true,
            'taxonomies'           => array(),
            'supports'             => array('title', 'editor', 'revisions', 'thumbnail', 'author', 'page-attributes', 'templates')
        );

        register_post_type('onepage', $args);

        $this->putInModularitySettings();
    }

    public function addTemplateSearchPaths($paths)
    {
        $paths[] = MODULARITY_ONEPAGE_TEMPLATE_PATH;
        return $paths;
    }
}
