<?php

namespace App\Jobs;

use App\Models\EmailStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Exception;

class SendCampaignEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        private EmailStatus $emailStatus
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $campaign = $this->emailStatus->campaign;
            $contact = $this->emailStatus->contact;

            Log::info("Sending campaign email", [
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'email' => $contact->email,
            ]);

            $success = $this->sendEmail($contact->email, $campaign->subject, $campaign->body);

            if ($success) {
                $this->emailStatus->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            } else {
                $this->emailStatus->update([
                    'status' => 'failed',
                    'error_message' => 'Failed to send email',
                ]);
            }

            $campaign->refresh();

            if ($campaign->status === 'sending' &&
                $campaign->emailStatuses()->where('status', 'pending')->count() === 0) {
                $campaign->update([
                    'status' => $campaign->emailStatuses()->where('status', 'sent')->count() > 0 ? 'sent' : 'failed',
                    'sent_at' => now(),
                ]);
            }

        } catch (Exception $e) {
            Log::error("Failed to send campaign email", [
                'email_status_id' => $this->emailStatus->id,
                'error' => $e->getMessage(),
            ]);

            $this->emailStatus->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    private function sendEmail(string $to, string $subject, string $body): bool
    {
        sleep(1);

        if (rand(1, 100) <= 10) {
            throw new Exception("Simulated email sending failure");
        }

        Log::info("Email sent successfully", [
            'to' => $to,
            'subject' => $subject,
        ]);

        return true;
    }
}
