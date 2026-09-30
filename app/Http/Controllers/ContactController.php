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

        $heroSection = $contactPage?->sections()->where('name', 'hero')->first();
        $contactListSection = $contactPage?->sections()->where('name', 'contact_list')->first();
        $section = fn (string $name) => $contactPage?->sections()->where('name', $name)->first()?->content ?? [];

        $heroContent = $heroSection?->content ?: ['title' => 'Kami senang bertemu denganmu', 'subtitle' => null, 'image' => null];

        if (! empty($heroContent['image'])) {
            $heroContent['image'] = Storage::url($heroContent['image']);
        }

        return Inertia::render('Contact', [
            'meta' => $contactPage?->sections()->where('name', 'meta')->first()?->content ?? [],
            'hero' => $heroContent,
            'contactList' => $contactListSection?->content ?? [],
            'officeHours' => $section('office_hours'),
            'requests' => $section('requests'),
        ]);
    }
}
