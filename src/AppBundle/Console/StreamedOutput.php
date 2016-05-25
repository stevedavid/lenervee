<?php

namespace AppBundle\Console;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamedOutput extends BufferedOutput
{
    public function doWrite($message, $newline)
    {
        $response = new StreamedResponse();
        $response->setCallback(function() use($message) {
            echo '<p>[<span class="output-time">'. date('H:i:s') .'</span>] $ ' . $message . '</p>';
            flush();
        });

        $response->send();
    }
}