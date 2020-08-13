<?php

namespace lumilock\lumilock\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Request;

class lumilock extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lumilock';
    }


    // public static function sub_nav_options($options)
    // {

    //     if (!empty($options)) {
    //         echo '<div id="laravlock-top-subnav">' .
    //             '<ul>';
    //         foreach ($options as $name) {

    //             array_key_exists('text', $name)? $this_text = $name['text'] : $this_text ='';
    //             array_key_exists('href', $name)? $this_href = $name['href'] : $this_href ='';
    //             Request::url() == $this_href ? $this_active = 'active' : $this_active = '' ;

    //             echo '<li><button class="laravlock-button-medium '.$this_active.'" data-url="'.$this_href.'">' . $this_text . '</button></li>';
    //         }
    //         echo '</ul>' .
    //             '</div>';
    //     }

    // }

    public static function ucfirstUtf8($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

    public static function replaceAccent($string)
    {
        return transliterator_transliterate('Latin-ASCII', $string);
    }

    public static function clean($string)
    {
        $string = str_replace(array(" ", ",", "_", "."), '-', $string); // Replaces all spaces with hyphens.
        $string = lumilock::replaceAccent($string);
        $string = preg_replace('/[^A-Za-z\-\.]/', '', $string); // Removes special chars.
        $string = trim($string, '-');
        $string = mb_strtolower(preg_replace('/-+/', '-', $string)); // Replaces multiple hyphens with single one.
        $string = preg_replace("/-\.-|-\.|\.-/", ".", $string); // Removes special chars.

        $string = trim($string, '.');
        return $string;
    }

    public static function name($string)
    {
        $result = str_replace(array(" ", ",", "_", "."), '-', $string); // Replaces all spaces with hyphens.
        $result2 = preg_replace('/[^A-Za-zÀ-ÿ\-\.]/', '', $result); // Removes special chars.
        $result3 = str_replace(' ', '-', $result2); // Replaces all spaces with hyphens.
        $result4 = mb_strtolower(preg_replace('/-+/', '-', $result3)); // Replaces multiple hyphens with single one.
        $result5 = trim($result4, '-');
        $result6 = preg_replace("/-\.-|-\.|\.-/", ".", $result5); // Removes special chars.
        $splString = explode("-", $result6);
        for ($i = 0; $i < count($splString); $i++) {
            $splString[$i] = lumilock::ucfirstUtf8($splString[$i]);
        }
        $resultFinal = implode('-', $splString);
        $resultFinal = trim($resultFinal, '.');
        return $resultFinal;
    }
}
