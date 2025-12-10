<script setup lang="ts">
import { index, store } from '@/actions/App/Http/Controllers/ContactController';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contacts',
        href: index.url(),
    },
    {
        title: 'Create',
        href: store.url(),
    },
];

const submit = () => {
    form.post(store.url());
};
</script>

<template>
    <Head title="Create Contact" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-4">
                <div class="flex items-center space-x-2">
                    <Link :href="index.url()">
                        <Button variant="ghost" size="sm">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Contacts
                        </Button>
                    </Link>
                </div>

                <Card class="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Create New Contact</CardTitle>
                        <CardDescription>
                            Add a new contact to your email list
                        </CardDescription>
                    </CardHeader>
                    <form @submit.prevent="submit">
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="John Doe"
                                    :class="{
                                        'border-destructive': form.errors.name,
                                    }"
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="john@example.com"
                                    :class="{
                                        'border-destructive': form.errors.email,
                                    }"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <div
                                class="flex items-center justify-end space-x-2"
                            >
                                <Link :href="index.url()">
                                    <Button variant="outline">Cancel</Button>
                                </Link>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    <Save class="mr-2 h-4 w-4" />
                                    {{
                                        form.processing
                                            ? 'Creating...'
                                            : 'Create Contact'
                                    }}
                                </Button>
                            </div>
                        </CardFooter>
                    </form>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
