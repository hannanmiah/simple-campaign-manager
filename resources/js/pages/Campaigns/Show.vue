<script setup lang="ts">
import { index, send } from '@/actions/App/Http/Controllers/CampaignController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
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
import {
    ArrowLeft,
    CheckCircle,
    Clock,
    Eye,
    Mail,
    Send,
    Users,
    XCircle,
} from 'lucide-vue-next';

const { campaign, stats } = defineProps<{
    campaign: Object;
    stats: Object;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Campaigns',
        href: index.url(),
    },
    {
        title: campaign.subject,
        href: '#',
    },
];

const getStatusIcon = (status) => {
    switch (status) {
        case 'sent':
            return CheckCircle;
        case 'failed':
            return XCircle;
        case 'pending':
            return Clock;
        default:
            return Mail;
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case 'sent':
            return 'text-green-600';
        case 'failed':
            return 'text-red-600';
        case 'pending':
            return 'text-yellow-600';
        default:
            return 'text-gray-600';
    }
};

const sendCampaign = () => {
    if (confirm(`Are you sure you want to send "${campaign.subject}"?`)) {
        router.post(send.url(campaign.id));
    }
};
</script>

<template>
    <Head :title="`Campaign - ${campaign.subject}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <Link :href="index.url()">
                        <Button variant="ghost" size="sm">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Campaigns
                        </Button>
                    </Link>
                </div>
                <Button
                    v-if="campaign.status === 'draft'"
                    @click="sendCampaign"
                    :disabled="campaign.status === 'sending'"
                >
                    <Send class="mr-2 h-4 w-4" />
                    {{
                        campaign.status === 'sending'
                            ? 'Sending...'
                            : 'Send Campaign'
                    }}
                </Button>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Campaign Details</CardTitle>
                        <CardDescription>
                            Created on
                            {{
                                new Date(
                                    campaign.created_at,
                                ).toLocaleDateString()
                            }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Subject
                            </p>
                            <p class="text-lg">{{ campaign.subject }}</p>
                        </div>
                        <Separator />
                        <div>
                            <p
                                class="mb-2 text-sm font-medium text-muted-foreground"
                            >
                                Message
                            </p>
                            <div class="prose prose-sm max-w-none">
                                <div
                                    class="rounded-md bg-muted p-4 whitespace-pre-wrap"
                                >
                                    {{ campaign.body }}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center space-x-2">
                                <Badge
                                    :variant="getStatusColor(campaign.status)"
                                >
                                    {{ campaign.status }}
                                </Badge>
                            </div>
                            <p
                                v-if="campaign.sent_at"
                                class="mt-2 text-sm text-muted-foreground"
                            >
                                Sent on
                                {{
                                    new Date(
                                        campaign.sent_at,
                                    ).toLocaleDateString()
                                }}
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Delivery Statistics</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <p
                                        class="text-2xl font-bold text-green-600"
                                    >
                                        {{ stats.sent }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Sent
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-red-600">
                                        {{ stats.failed }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Failed
                                    </p>
                                </div>
                            </div>
                            <Separator />
                            <div class="text-center">
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{ stats.pending }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Pending
                                </p>
                            </div>
                            <Separator />
                            <div class="text-center">
                                <p class="text-2xl font-bold">
                                    {{ stats.total }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Total Recipients
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center">
                        <Users class="mr-2 h-5 w-5" />
                        Recipient Status
                    </CardTitle>
                    <CardDescription>
                        Track delivery status for each recipient
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="campaign.email_statuses.length > 0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Contact</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Sent Date</TableHead>
                                    <TableHead
                                        v-if="campaign.status !== 'draft'"
                                        class="text-right"
                                        >Actions</TableHead
                                    >
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="emailStatus in campaign.email_statuses"
                                    :key="emailStatus.id"
                                >
                                    <TableCell class="font-medium">{{
                                        emailStatus.contact.name
                                    }}</TableCell>
                                    <TableCell>{{
                                        emailStatus.contact.email
                                    }}</TableCell>
                                    <TableCell>
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <component
                                                :is="
                                                    getStatusIcon(
                                                        emailStatus.status,
                                                    )
                                                "
                                                class="h-4 w-4"
                                                :class="
                                                    getStatusColor(
                                                        emailStatus.status,
                                                    )
                                                "
                                            />
                                            <Badge
                                                :variant="
                                                    emailStatus.status ===
                                                    'sent'
                                                        ? 'default'
                                                        : 'secondary'
                                                "
                                            >
                                                {{ emailStatus.status }}
                                            </Badge>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div v-if="emailStatus.sent_at">
                                            {{
                                                new Date(
                                                    emailStatus.sent_at,
                                                ).toLocaleDateString()
                                            }}
                                        </div>
                                        <span
                                            v-else
                                            class="text-muted-foreground"
                                            >-</span
                                        >
                                    </TableCell>
                                    <TableCell
                                        v-if="campaign.status !== 'draft'"
                                        class="text-right"
                                    >
                                        <Button variant="ghost" size="sm">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                    <div v-else class="py-8 text-center">
                        <p class="text-muted-foreground">
                            No recipients added to this campaign.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
