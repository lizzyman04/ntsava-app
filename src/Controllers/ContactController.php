<?php

namespace Source\Controllers;

use App\Core\Mailer;
use Fluxor\Controller;
use Fluxor\Response;

class ContactController extends Controller
{
    public function index()
    {
        $plan = $this->request->input('plan');
        $success = $this->request->input('success') === '1';
        
        return Response::view('contact', [
            'title' => 'Contact Us',
            'plan' => $plan,
            'success' => $success
        ]);
    }
    
    public function send()
    {
        $name = $this->request->input('name');
        $email = $this->request->input('email');
        $subject = $this->request->input('subject');
        $plan = $this->request->input('plan');
        $message = $this->request->input('message');
        
        // Validation
        if (empty($name) || empty($email) || empty($message)) {
            return Response::error('All fields are required', 400);
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::error('Invalid email address', 400);
        }
        
        // Send email
        $mailer = new Mailer();
        $emailBody = "
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Plan Interest:</strong> " . htmlspecialchars($plan ?: 'Not specified') . "</p>
            <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
        ";
        
        $mailer->send('support@tudocomlizzyman.com', 'New Contact: ' . $subject, $emailBody);
        
        // Optional: Send confirmation to user
        $confirmationBody = "
            <h2>Thank you for contacting us!</h2>
            <p>We've received your message and will get back to you within 24 hours.</p>
            <p>Here's a copy of your message:</p>
            <hr>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
        ";
        $mailer->send($email, 'Thank you for contacting CDN App', $confirmationBody);
        
        return Response::success([
            'redirect' => '/contact?success=1'
        ], 'Message sent successfully');
    }
}