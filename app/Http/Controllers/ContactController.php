<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use App\Models\ContactSubmission;


class ContactController extends Controller
{
    public function index()
    {
        $captcha = rand(1, 9) . ' + ' . rand(1, 9);
        session(['contact_captcha' => eval('return ' . str_replace(' + ', '+', $captcha) . ';')]);

        return Inertia::render('Frontend/Contact', [
            'seo' => [
                'title' => 'Contact Us - Venture Up North',
                'description' => 'Have a question, suggestion, or partnership idea? We had love to hear from you!
Whether you are planning your next adventure or want to collaborate, reach out and our team will get back to you as soon as possible.',
                'image' => asset('/public/images/Venture-Up-North.png'),
                'canonical' => canonical_url(),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'captcha_question' => $captcha,
        ]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        if ($request->captcha != session('contact_captcha')) {
            return back()->withErrors(['captcha' => 'Invalid CAPTCHA answer.']);
        }

        // Save to DB
        ContactSubmission::create($request->only('name', 'email', 'subject', 'message'));

        // Mail::raw($validated['message'], function ($mail) use ($validated) {
        //     $mail->to('you@example.com')
        //         ->subject('New Contact Message: ' . ($validated['subject'] ?? 'No Subject'))
        //         ->replyTo($validated['email'], $validated['name']);
        // });

        return redirect()->route('contact.index')->with('success', 'Your message has been sent!');
    }

    public function showContacts()
    {
        $contacts = ContactSubmission::orderBy('name')->paginate(10);

        return Inertia::render('Admin/Contact/Index', [
            'contacts' => $contacts
        ]);
    }

    public function deleteContact(ContactSubmission $contact)
    {
        $contact->delete();

        return back();
    }
}
