<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function show() {
        $contactPage = Pages::where('name', 'Contact')->first();

        $heroSection = $contactPage->sections()->where('name', 'hero')->first();
        $contactListSection = $contactPage->sections()->where('name', 'contact_list')->first();

        return Inertia::render('Contact', [
            'hero' => $heroSection ? $heroSection->content : ['title' => 'Got Question? We’ve got answers.', 'subtitle' => 'Contact us for support or inquiries.'],
            'contactList' => $contactListSection ? $contactListSection->content : [],
        ]);
    }
}
