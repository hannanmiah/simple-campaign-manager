<script setup lang="ts">
import {
    create,
    destroy,
    edit,
    index,
    send,
    show,
} from '@/actions/App/Http/Controllers/CampaignController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Eye, MoreHorizontal, Plus, Send, Trash2 } from 'lucide-vue-next';

const { campaigns } = defineProps<{
    campaigns: object;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Campaigns',
        href: index.url(),
    },
];

const getStatusColor = (status) => {
    switch (status) {
        case 'sent':
            return 'default';
        case 'sending':
            return 'secondary';
        case 'draft':
            return 'outline';
        case 'failed':
            return 'destructive';
        default:
            return 'secondary';
    }
};

const sendCampaign = (campaign) => {
    if (confirm(`Are you sure you want to send "${campaign.subject}"?`)) {
        router.post(send.url(campaign.id));
    }
};

const deleteCampaign = (campaign) => {
    if (confirm(`Are you sure you want to delete "${campaign.subject}"?`)) {
        router.delete(destroy.url(campaign.id));
    }
};
</script>

<template>
    <Head title="Campaigns" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">
                            Campaigns
                        </h1>
                        <p class="text-muted-foreground">
                            Manage your email campaigns
                        </p>
                    </div>
                    <Link :href="create.url()">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            New Campaign
                        </Button>
                    </Link>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>All Campaigns</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="campaigns.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Subject</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Sent</TableHead>
                                        <TableHead>Failed</TableHead>
                                        <TableHead>Pending</TableHead>
                                        <TableHead>Created</TableHead>
                                        <TableHead class="text-right"
                                            >Actions</TableHead
                                        >
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="campaign in campaigns.data"
                                        :key="campaign.id"
                                    >
                                        <TableCell class="font-medium">{{
                                            campaign.subject
                                        }}</TableCell>
                                        <TableCell>
                                            <Badge
                                                :variant="
                                                    getStatusColor(
                                                        campaign.status,
                                                    )
                                                "
                                            >
                                                {{ campaign.status }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>{{
                                            campaign.sent_count || 0
                                        }}</TableCell>
                                        <TableCell>{{
                                            campaign.failed_count || 0
                                        }}</TableCell>
                                        <TableCell>{{
                                            campaign.pending_count || 0
                                        }}</TableCell>
                                        <TableCell>
                                            {{
                                                new Date(
                                                    campaign.created_at,
                                                ).toLocaleDateString()
                                            }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        class="h-8 w-8 p-0"
                                                    >
                                                        <span class="sr-only"
                                                            >Open menu</span
                                                        >
                                                        <MoreHorizontal
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent
                                                    align="end"
                                                >
                                                    <DropdownMenuLabel
                                                        >Actions</DropdownMenuLabel
                                                    >
                                                    <DropdownMenuItem as-child>
                                                        <Link
                                                            :href="
                                                                show.url(
                                                                    campaign.id,
                                                                )
                                                            "
                                                        >
                                                            <Eye
                                                                class="mr-2 h-4 w-4"
                                                            />
                                                            View details
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem
                                                        v-if="
                                                            campaign.status ===
                                                            'draft'
                                                        "
                                                        as-child
                                                    >
                                                        <Link
                                                            :href="
                                                                edit.url(
                                                                    campaign.id,
                                                                )
                                                            "
                                                        >
                                                            <Edit
                                                                class="mr-2 h-4 w-4"
                                                            />
                                                            Edit campaign
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuSeparator />
                                                    <DropdownMenuItem
                                                        v-if="
                                                            campaign.status ===
                                                            'draft'
                                                        "
                                                        @click="
                                                            sendCampaign(
                                                                campaign,
                                                            )
                                                        "
                                                    >
                                                        <Send
                                                            class="mr-2 h-4 w-4"
                                                        />
                                                        Send campaign
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem
                                                        v-if="
                                                            [
                                                                'draft',
                                                                'failed',
                                                            ].includes(
                                                                campaign.status,
                                                            )
                                                        "
                                                        @click="
                                                            deleteCampaign(
                                                                campaign,
                                                            )
                                                        "
                                                        class="text-destructive"
                                                    >
                                                        <Trash2
                                                            class="mr-2 h-4 w-4"
                                                        />
                                                        Delete campaign
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <div class="mt-4">
                                <template
                                    v-for="link in campaigns.links"
                                    :key="link.label"
                                >
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        v-html="link.label"
                                        class="rounded-md px-3 py-2 text-sm"
                                        :class="{
                                            'bg-primary text-primary-foreground':
                                                link.active,
                                            'hover:bg-accent': !link.active,
                                        }"
                                    />
                                    <span
                                        v-else
                                        v-html="link.label"
                                        class="px-3 py-2 text-sm text-muted-foreground"
                                    />
                                </template>
                            </div>
                        </div>

                        <div v-else class="py-12 text-center">
                            <p class="text-muted-foreground">
                                No campaigns found.
                            </p>
                            <Link :href="create.url()" class="mt-4">
                                <Button>Create your first campaign</Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
