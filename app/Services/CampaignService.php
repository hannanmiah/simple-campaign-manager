<?php

namespace App\Services;

use App\Jobs\SendCampaignEmail;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailStatus;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    public function createCampaign(array $data): Campaign
    {
        return DB::transaction(function () use ($data) {
            $campaign = Campaign::create([
                'subject' => $data['subject'],
                'body' => $data['body'],
                'status' => 'draft',
            ]);

            foreach ($data['recipients'] as $contactId) {
                EmailStatus::create([
                    'campaign_id' => $campaign->id,
                    'contact_id' => $contactId,
                    'status' => 'pending',
                ]);
            }

            return $campaign;
        });
    }

    public function updateCampaign(Campaign $campaign, array $data): Campaign
    {
        return DB::transaction(function () use ($campaign, $data) {
            $campaign->update([
                'subject' => $data['subject'],
                'body' => $data['body'],
            ]);

            $campaign->emailStatuses()->delete();

            foreach ($data['recipients'] as $contactId) {
                EmailStatus::create([
                    'campaign_id' => $campaign->id,
                    'contact_id' => $contactId,
                    'status' => 'pending',
                ]);
            }

            return $campaign;
        });
    }

    public function sendCampaign(Campaign $campaign): void
    {
        $campaign->update([
            'status' => 'sending',
        ]);

        foreach ($campaign->emailStatuses()->where('status', 'pending')->get() as $emailStatus) {
            SendCampaignEmail::dispatch($emailStatus);
        }

        $campaign->refresh();

        if ($campaign->emailStatuses()->where('status', 'pending')->count() === 0) {
            $campaign->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }
    }
}