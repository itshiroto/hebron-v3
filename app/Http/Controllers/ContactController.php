<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function show() {
        $contactPage = Pages::where('name', 'Contact')->first();

        $heroSection = $contactPage->sections()->where('name', 'hero')->first();
        $contactListSection = $contactPage->sections()->where('name', 'contact_list')->first();

        $heroContent = $heroSection ? $heroSection->content : ['title' => 'Got Question? We’ve got answers.', 'subtitle' => 'Contact us for support or inquiries.', 'image' => null];

        // Prepend storage path to image if it exists
        if (isset($heroContent['image']) && $heroContent['image']) {
            $heroContent['image'] = Storage::url($heroContent['image']);
        }

        return Inertia::render('Contact', [
            'hero' => $heroContent,
            'contactList' => $contactListSection ? $contactListSection->content : [],
        ]);
    }
}
