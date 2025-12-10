<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function __construct(
        private CampaignService $campaignService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $campaigns = Campaign::query()
            ->with('emailStatuses')
            ->withCount(['emailStatuses as sent_count' => function ($query) {
                $query->where('status', 'sent');
            }, 'emailStatuses as failed_count' => function ($query) {
                $query->where('status', 'failed');
            }, 'emailStatuses as pending_count' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contacts = Contact::orderBy('name')->get();

        return Inertia::render('Campaigns/Create', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipients' => 'required|array',
            'recipients.*' => 'exists:contacts,id',
        ]);

        $campaign = $this->campaignService->createCampaign($validated);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        $campaign->load([
            'emailStatuses.contact',
            'emailStatuses' => function ($query) {
                $query->with('contact')->orderBy('created_at');
            }
        ]);

        $stats = [
            'total' => $campaign->emailStatuses()->count(),
            'sent' => $campaign->emailStatuses()->where('status', 'sent')->count(),
            'failed' => $campaign->emailStatuses()->where('status', 'failed')->count(),
            'pending' => $campaign->emailStatuses()->where('status', 'pending')->count(),
        ];

        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Cannot edit a campaign that has been sent.');
        }

        $campaign->load('recipients');
        $contacts = Contact::orderBy('name')->get();

        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'contacts' => $contacts,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Cannot update a campaign that has been sent.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipients' => 'required|array',
            'recipients.*' => 'exists:contacts,id',
        ]);

        $this->campaignService->updateCampaign($campaign, $validated);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Send the campaign.
     */
    public function send(Campaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'This campaign has already been sent.');
        }

        $this->campaignService->sendCampaign($campaign);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign is being sent.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        if (in_array($campaign->status, ['sending', 'sent'])) {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Cannot delete a campaign that has been sent.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }
}
