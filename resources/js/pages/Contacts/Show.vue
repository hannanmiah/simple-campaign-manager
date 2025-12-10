<script setup lang="ts">
import { show as showCampaign } from '@/actions/App/Http/Controllers/CampaignController';
import { index } from '@/actions/App/Http/Controllers/ContactController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Mail } from 'lucide-vue-next';

const props = defineProps<{
    contact: Object;
    campaigns: Array;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contacts',
        href: index.url(),
    },
    {
        title: props.contact.name,
        href: '#',
    },
];

const getStatusColor = (status) => {
    switch (status) {
        case 'sent':
            return 'default';
        case 'pending':
            return 'secondary';
        case 'failed':
            return 'destructive';
        default:
            return 'secondary';
    }
};
</script>

<template>
    <Head :title="`Contact - ${contact.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center space-x-2">
                <Link :href="index.url()">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Contacts
                    </Button>
                </Link>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Contact Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Name
                            </p>
                            <p class="text-lg">{{ contact.name }}</p>
                        </div>
                        <div>
                            <p
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Email
                            </p>
                            <div class="flex items-center space-x-2">
                                <Mail class="h-4 w-4 text-muted-foreground" />
                                <a
                                    :href="`mailto:${contact.email}`"
                                    class="text-lg hover:underline"
                                >
                                    {{ contact.email }}
                                </a>
                            </div>
                        </div>
                        <div>
                            <p
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Total Campaigns
                            </p>
                            <p class="text-lg">{{ campaigns.length }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Campaign Statistics</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">
                                    {{
                                        campaigns.filter(
                                            (c) => c.pivot.status === 'sent',
                                        ).length
                                    }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Sent
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{
                                        campaigns.filter(
                                            (c) => c.pivot.status === 'pending',
                                        ).length
                                    }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Pending
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-red-600">
                                    {{
                                        campaigns.filter(
                                            (c) => c.pivot.status === 'failed',
                                        ).length
                                    }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Failed
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Campaign History</CardTitle>
                    <CardDescription>
                        All campaigns sent to this contact
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="campaigns.length > 0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Sent Date</TableHead>
                                    <TableHead class="text-right"
                                        >Actions</TableHead
                                    >
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="campaign in campaigns"
                                    :key="campaign.id"
                                >
                                    <TableCell class="font-medium">{{
                                        campaign.subject
                                    }}</TableCell>
                                    <TableCell>
                                        <Badge
                                            :variant="
                                                getStatusColor(
                                                    campaign.pivot.status,
                                                )
                                            "
                                        >
                                            {{ campaign.pivot.status }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div
                                            v-if="campaign.pivot.sent_at"
                                            class="flex items-center space-x-2"
                                        >
                                            <Calendar
                                                class="h-4 w-4 text-muted-foreground"
                                            />
                                            <span>{{
                                                new Date(
                                                    campaign.pivot.sent_at,
                                                ).toLocaleDateString()
                                            }}</span>
                                        </div>
                                        <span
                                            v-else
                                            class="text-muted-foreground"
                                            >-</span
                                        >
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <Link
                                            :href="
                                                showCampaign.url(campaign.id)
                                            "
                                        >
                                            <Button variant="ghost" size="sm">
                                                View Campaign
                                            </Button>
                                        </Link>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                    <div v-else class="py-8 text-center">
                        <p class="text-muted-foreground">
                            No campaigns sent to this contact yet.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
