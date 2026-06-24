<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Model\User;
use Carbon\Carbon;

class DMCAController extends Controller
{
    /**
     * Show the DMCA information page
     */
    public function index()
    {
        return view('dmca.index');
    }

    /**
     * Show the DMCA takedown request form
     */
    public function takedownForm()
    {
        return view('dmca.takedown-form');
    }

    /**
     * Process a DMCA takedown request
     */
    public function submitTakedown(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:50',
            'content_url' => 'required|url|max:500',
            'original_work_description' => 'required|string|min:50|max:5000',
            'original_work_url' => 'nullable|url|max:500',
            'infringing_material_description' => 'required|string|min:50|max:5000',
            'good_faith_statement' => 'required|accepted',
            'accuracy_statement' => 'required|accepted',
            'authorization_statement' => 'required|accepted',
            'signature' => 'required|string|max:255',
            'signature_date' => 'required|date',
        ], [
            'full_name.required' => 'Please provide your full legal name.',
            'content_url.required' => 'Please provide the URL of the infringing content.',
            'content_url.url' => 'Please provide a valid URL.',
            'original_work_description.min' => 'Please provide a detailed description (at least 50 characters).',
            'good_faith_statement.accepted' => 'You must certify that you have a good faith belief.',
            'accuracy_statement.accepted' => 'You must certify that the information is accurate.',
            'authorization_statement.accepted' => 'You must certify that you are authorized to act.',
            'signature.required' => 'Electronic signature is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate reference number
        $referenceNumber = 'DMCA-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Store the takedown request (you would create a DMCATakedown model)
        $takedownData = [
            'reference_number' => $referenceNumber,
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'company' => $request->input('company'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'content_url' => $request->input('content_url'),
            'original_work_description' => $request->input('original_work_description'),
            'original_work_url' => $request->input('original_work_url'),
            'infringing_material_description' => $request->input('infringing_material_description'),
            'signature' => $request->input('signature'),
            'signature_date' => $request->input('signature_date'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'pending',
            'submitted_at' => now(),
        ];

        // Log for compliance
        Log::channel('daily')->info('DMCA Takedown Request Submitted', [
            'reference' => $referenceNumber,
            'email' => $request->input('email'),
            'content_url' => $request->input('content_url'),
            'ip' => $request->ip(),
        ]);

        // Store in database (if model exists) or send email
        // DMCATakedown::create($takedownData);

        // Send notification email to designated agent
        $this->sendTakedownNotification($takedownData);

        // Send confirmation to submitter
        $this->sendConfirmationEmail($takedownData);

        return redirect()->route('dmca.submitted')
            ->with('reference_number', $referenceNumber)
            ->with('success', 'Your DMCA takedown request has been submitted successfully.');
    }

    /**
     * Show submission confirmation page
     */
    public function submitted()
    {
        return view('dmca.submitted');
    }

    /**
     * Show the counter-notification form
     */
    public function counterNotificationForm()
    {
        return view('dmca.counter-notification');
    }

    /**
     * Process a counter-notification
     */
    public function submitCounterNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:50',
            'original_takedown_reference' => 'required|string|max:50',
            'content_url' => 'required|url|max:500',
            'reason_for_restoration' => 'required|string|min:100|max:10000',
            'perjury_statement' => 'required|accepted',
            'jurisdiction_consent' => 'required|accepted',
            'signature' => 'required|string|max:255',
            'signature_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $referenceNumber = 'CN-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Log for compliance
        Log::channel('daily')->info('DMCA Counter-Notification Submitted', [
            'reference' => $referenceNumber,
            'original_takedown' => $request->input('original_takedown_reference'),
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('dmca.submitted')
            ->with('reference_number', $referenceNumber)
            ->with('success', 'Your counter-notification has been submitted.');
    }

    /**
     * Send takedown notification to designated agent
     */
    protected function sendTakedownNotification($data)
    {
        $agentEmail = getSetting('dmca.agent_email') ?? getSetting('admin.email');
        
        if ($agentEmail) {
            try {
                Mail::send('emails.dmca-takedown-notification', $data, function($message) use ($agentEmail, $data) {
                    $message->to($agentEmail)
                        ->subject('DMCA Takedown Request: ' . $data['reference_number']);
                });
            } catch (\Exception $e) {
                Log::error('Failed to send DMCA notification email: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send confirmation email to submitter
     */
    protected function sendConfirmationEmail($data)
    {
        try {
            Mail::send('emails.dmca-confirmation', $data, function($message) use ($data) {
                $message->to($data['email'])
                    ->subject('DMCA Request Confirmation: ' . $data['reference_number']);
            });
        } catch (\Exception $e) {
            Log::error('Failed to send DMCA confirmation email: ' . $e->getMessage());
        }
    }
}
