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

        add_filter('post_link', array($this, 'removeSinglePermalinks'), 10, 3);
        add_filter('post_row_actions', array($this, 'rowActions'), 10, 2);
    }

    public function rowActions($actions, $post)
    {
        if ($post->post_type !== 'onepage') {
            return $actions;
        }

        if (isset($actions['view'])) {
            unset($actions['view']);
        }

        return $actions;
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
        $nameSingular = __('Onepage', 'modularity-onepage');
        $namePlural = __('Onepage', 'modularity-onepage');

        $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU3IDU3IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1NyA1NzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+PGc+PHBhdGggZD0iTTIyLjY2LDBIMy4zNEMxLjQ5OCwwLDAsMS40OTgsMCwzLjM0djE5LjMyQzAsMjQuNTAyLDEuNDk4LDI2LDMuMzQsMjZoMTkuMzJjMS44NDIsMCwzLjM0LTEuNDk4LDMuMzQtMy4zNFYzLjM0ICAgQzI2LDEuNDk4LDI0LjUwMiwwLDIyLjY2LDB6IiBmaWxsPSIjRkZGRkZGIi8+PHBhdGggZD0iTTMzLjM0LDI2aDE5LjMyYzEuODQyLDAsMy4zNC0xLjQ5OCwzLjM0LTMuMzRWMy4zNEM1NiwxLjQ5OCw1NC41MDIsMCw1Mi42NiwwSDMzLjM0QzMxLjQ5OCwwLDMwLDEuNDk4LDMwLDMuMzR2MTkuMzIgICBDMzAsMjQuNTAyLDMxLjQ5OCwyNiwzMy4zNCwyNnoiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJNMjIuNjYsMzBIMy4zNEMxLjQ5OCwzMCwwLDMxLjQ5OCwwLDMzLjM0djE5LjMyQzAsNTQuNTAyLDEuNDk4LDU2LDMuMzQsNTZoMTkuMzJjMS44NDIsMCwzLjM0LTEuNDk4LDMuMzQtMy4zNFYzMy4zNCAgIEMyNiwzMS40OTgsMjQuNTAyLDMwLDIyLjY2LDMweiIgZmlsbD0iI0ZGRkZGRiIvPjxwYXRoIGQ9Ik01NSw0MUg0NVYzMWMwLTEuMTA0LTAuODk2LTItMi0ycy0yLDAuODk2LTIsMnYxMEgzMWMtMS4xMDQsMC0yLDAuODk2LTIsMnMwLjg5NiwyLDIsMmgxMHYxMGMwLDEuMTA0LDAuODk2LDIsMiwyICAgczItMC44OTYsMi0yVjQ1aDEwYzEuMTA0LDAsMi0wLjg5NiwyLTJTNTYuMTA0LDQxLDU1LDQxeiIgZmlsbD0iI0ZGRkZGRiIvPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48L3N2Zz4=';

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
            'publicly_queriable'   => true,
            'show_ui'              => true,
            'show_in_nav_menus'    => false,
            'menu_position'        => 350,
            'has_archive'          => false,
            'hierarchical'         => false,
            'exclude_from_search'  => true,
            'taxonomies'           => array(),
            'supports'             => array('title', 'editor', 'revisions', 'thumbnail', 'author', 'page-attributes', 'templates')
        );

        register_post_type('onepage', $args);

        $this->putInModularitySettings();
    }

    public function removeSinglePermalinks($url, $post = null, $leavename = null)
    {
        if (0 === strpos($url, get_post_type_archive_link('onepage'))) {
            return get_post_type_archive_link('onepage');
        }

        return $url;
    }

    public function addTemplateSearchPaths($paths)
    {
        $paths[] = MODULARITY_ONEPAGE_TEMPLATE_PATH;
        return $paths;
    }
}
