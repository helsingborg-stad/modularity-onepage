<?php

namespace ModularityOnePage\Helper;

class Varnish
{
    public function __construct()
    {
        add_action('save_post', array($this, 'sendPurgeRequest'));
    }

    public function sendPurgeRequest($post_id)
    {

        //Not for revisions
        if (wp_is_post_revision($post_id)) {
            return;
        }

        //Check if modularity, then send purge!
        if ($this->isOnePagePost($post_id)) {
            wp_remote_request($this->getMasterUrl(),
                array(
                    'method' => 'PURGE',
                    'timeout' => 4,
                    'redirection' => 0,
                )
            );
            return true;
        }

        return false;
    }

    private function isOnePagePost($post_id)
    {
        if (get_post_type($post_id) === 'onepage') {
            return true;
        }
        return false;
    }

    private function getMasterUrl()
    {
        if (!is_multisite()) {
            return home_url();
        }
        return get_site_url(get_current_blog_id());
    }
}
