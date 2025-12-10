<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Users, Mail, Send, TrendingUp } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-8">
                <h1 class="text-3xl font-bold">Email Campaign Manager</h1>
                <p class="text-muted-foreground">Manage your contacts and email campaigns efficiently</p>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card class="hover:shadow-md transition-shadow">
                    <Link href="/contacts">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Contacts</CardTitle>
                            <Users class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">50+</div>
                            <p class="text-xs text-muted-foreground">Manage your email list</p>
                        </CardContent>
                    </Link>
                </Card>

                <Card class="hover:shadow-md transition-shadow">
                    <Link href="/campaigns">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Campaigns</CardTitle>
                            <Mail class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">0</div>
                            <p class="text-xs text-muted-foreground">Create and send campaigns</p>
                        </CardContent>
                    </Link>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Sent Emails</CardTitle>
                        <Send class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">0</div>
                        <p class="text-xs text-muted-foreground">Successfully delivered</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Analytics</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">-</div>
                        <p class="text-xs text-muted-foreground">Track performance</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2 mt-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>Get started with these common tasks</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <Link href="/contacts/create">
                            <Button class="w-full justify-start">
                                <Users class="mr-2 h-4 w-4" />
                                Add New Contact
                            </Button>
                        </Link>
                        <Link href="/campaigns/create">
                            <Button class="w-full justify-start" variant="outline">
                                <Mail class="mr-2 h-4 w-4" />
                                Create Campaign
                            </Button>
                        </Link>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Getting Started</CardTitle>
                        <CardDescription>Learn how to use the campaign manager</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium">1. Add Contacts</h4>
                            <p class="text-sm text-muted-foreground">Import or manually add contacts to your email list</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium">2. Create Campaign</h4>
                            <p class="text-sm text-muted-foreground">Design your email campaign and select recipients</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium">3. Send & Track</h4>
                            <p class="text-sm text-muted-foreground">Send your campaign and monitor delivery status</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
