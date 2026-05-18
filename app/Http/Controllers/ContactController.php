<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use Illuminate\Http\Request;
use App\Services\GoogleSheetService;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'email' => 'required|email|max:255',
            'phone' => [
                'nullable',
                'regex:/^[6-9]\d{9}$/'
            ],
            'subject' => 'nullable|in:Sponsor,Badminton,Cricket,Football,Tennis,Table Tennis,Basketball,Volleyball,Kabaddi,Chess,Athletics',
            'message' => 'required|string',
        ], [
            // NAME
            'name.required' => 'Please enter your name.',
            'name.regex' => 'Name can only contain letters and spaces. Special characters are not allowed.',

            // EMAIL
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please provide a valid email address.',

            // PHONE
            'phone.regex' => 'Please enter a valid Indian mobile number (10 digits starting with 6-9).',

            // SUBJECT
            'subject.in' => 'Please select a valid subject.',

            // MESSAGE
            'message.required' => 'Please enter your message.',
        ]);

        // Save to DB
        $enquiry = Contacts::create($validated);

        // Push to Google Sheets
        try {
            $this->sheetService->append([
                $enquiry->name,
                $enquiry->email,
                $enquiry->phone ?? 'N/A',
                $enquiry->subject ?? 'N/A',
                $enquiry->message,
                now()->format('d/m/Y g:i A') // ✅ same format as tournament
            ], 'contactus'); // 👈 make sure this tab exists

        } catch (\Throwable $e) {
            Log::error('Google Sheet Error (Contact)', [
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Enquiry submitted successfully.',
            'data'    => $enquiry,
        ], 201);
    }
}