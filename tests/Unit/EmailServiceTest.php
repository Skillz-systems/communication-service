<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EmailService;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EmailServiceTest extends TestCase
{
    protected $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailService = new EmailService();
    }

    public function testInitializeSuccess()
    {
        $data = [
            "receiver" => "Dangoto Industry",
            "message_body" => "This is a test message.",
            "subject" => "Test Subject",
            "email" => "test@example.com",
            "link" => "http://example.com"
        ];

        $result = $this->emailService->initialize($data);
        $this->assertIsObject($result);
        $this->assertEquals($data['receiver'], $result->receiver);
        $this->assertEquals($data['message_body'], $result->message_body);
        $this->assertEquals($data['subject'], $result->subject);
        $this->assertEquals($data['email'], $result->email);
        $this->assertEquals($data['link'], $result->link);
    }

    public function testInitializeValidationError()
    {
        $data = [
            "receiver" => "",
            "message_body" => "This is a test message.",
            "subject" => "Test Subject",
            "email" => "test@example.com",
            "link" => "http://example.com"
        ];

        $result = $this->emailService->initialize($data);
        $this->assertInstanceOf(\Illuminate\Support\MessageBag::class, $result);
        $this->assertTrue($result->has('receiver'));
    }

    public function testSendNotificationEmail()
    {
        $notification = (object) [
            "receiver" => "Dangote Cement",
            'message_body' => "Your application has been approved and is awaiting pending confirmation, please kindly exercise some patience while your requests are been addressed properly. Thank you!",
            "subject" => "Test Subject",
            "email" => "akubueaugustutuskc@gmail.com",
            "link" => "http://example.com"
        ];

        Mail::fake();

        $this->emailService->sendNotificationEmail($notification);

        Mail::assertSent(NotificationEmail::class, function ($mail) use ($notification) {
            return $mail->hasTo($notification->email);
        });
    }
}
