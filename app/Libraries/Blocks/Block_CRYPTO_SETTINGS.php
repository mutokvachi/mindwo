<?php

namespace App\Libraries\Blocks;

use App\Models\Department;
use App\Models\Source;
use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class Block_CRYPTO_SETTINGS
 *
 * Widget that displays encryption settings
 *
 * @package App\Libraries\Blocks
 */
class Block_CRYPTO_SETTINGS extends Block
{

    /**
     * Render widget.
     * @return string
     */
    function getHtml()
    {
        $user = \App\User::find(\Auth::user()->id);

        $cert = $user->cryptoCertificate;


        if ($cert && $cert->public_key && $cert->private_key) {
            $has_cert = true;
        } else {
            $has_cert = false;
        }

        return view('blocks.crypto_settings', [
                    'has_cert' => $has_cert
                ])->render();
    }

    function getJS()
    {
        // TODO: Implement getJS() method.
    }

    function getCSS()
    {
        return '';
    }

    function getJSONData()
    {
        // TODO: Implement getJSONData() method.
    }

    protected function parseParams()
    {
        
    }
}
