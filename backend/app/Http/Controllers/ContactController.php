<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    function about(){
        $title = "Rreth Nesh";
        $about_us_txt = "Kjo faqe është krijuar për të demonstruar një aplikacion të thjeshtë Laravel me një faqe kontakti dhe një faqe rreth nesh.";
        $image = "https://barn2.com/wp-content/uploads/2018/01/Restaurant-ordering-system-e1569334281278.png";
        return view('about', [
            'titulli'=>$title,
            'imazhi'=>$image,
            'permbajtja'=>$about_us_txt
        ]);
    }    
}