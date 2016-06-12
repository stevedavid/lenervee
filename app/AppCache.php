<?php

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class AppCache extends HttpCache
{
    protected function getOptions()
    {
        return [
            'debug'                  => false,
            'default_ttl'            => 3600,
            'private_headers'        => array('Authorization', 'Cookie'),
            'allow_reload'           => true,
            'allow_revalidate'       => true,
            'stale_while_revalidate' => 2,
            'stale_if_error'         => 60,
        ];
    }
}
