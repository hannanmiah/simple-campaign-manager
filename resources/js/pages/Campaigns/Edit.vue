<script setup lang="ts">
import {
    index,
    show,
    update,
} from '@/actions/App/Http/Controllers/CampaignController';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Users } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    campaign: object;
    contacts: Array;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Campaigns',
        href: index.url(),
    },
    {
        title: props.campaign.subject,
        href: '#',
    },
    {
        title: 'Edit',
        href: update.url(props.campaign.id),
    },
];

const selectedContacts = ref(props.campaign.recipients?.map((r) => r.id) || []);
const form = useForm({
    subject: props.campaign.subject,
    body: props.campaign.body,
    recipients: [],
});

// Initialize form.recipients with selectedContacts
form.recipients = [...selectedContacts.value];

watch(
    selectedContacts,
    (newValue) => {
        form.recipients = [...newValue];
    },
    { deep: true },
);

const toggleAll = () => {
    if (selectedContacts.value.length === props.contacts.length) {
        selectedContacts.value = [];
    } else {
        selectedContacts.value = [...props.contacts.map((c) => c.id)];
    }
};

const toggleContact = (contactId: number) => {
    const index = selectedContacts.value.indexOf(contactId);
    if (index > -1) {
        selectedContacts.value.splice(index, 1);
    } else {
        selectedContacts.value.push(contactId);
    }
};

const submit = () => {
    form.put(update.url(props.campaign.id));
};
</script>

<template>
    <Head title="Edit Campaign" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center space-x-2">
                <Link :href="index.url()">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Campaigns
                    </Button>
                </Link>
            </div>

            <form @submit.prevent="submit">
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="space-y-4 md:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Edit Campaign</CardTitle>
                                <CardDescription>
                                    Update your email campaign content
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="subject">Subject</Label>
                                    <Input
                                        id="subject"
                                        v-model="form.subject"
                                        type="text"
                                        required
                                        placeholder="Enter campaign subject"
                                        :class="{
                                            'border-destructive':
                                                form.errors.subject,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.subject"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.subject }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="body">Message Body</Label>
                                    <Textarea
                                        id="body"
                                        v-model="form.body"
                                        required
                                        placeholder="Write your email message here..."
                                        rows="10"
                                        :class="{
                                            'border-destructive':
                                                form.errors.body,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.body"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors.body }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div class="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center">
                                    <Users class="mr-2 h-5 w-5" />
                                    Recipients
                                </CardTitle>
                                <CardDescription>
                                    Select contacts to receive this campaign
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        :checked="
                                            selectedContacts.length ===
                                            contacts.length
                                        "
                                        @update:model-value="toggleAll"
                                    />
                                    <Label class="text-sm font-medium">
                                        Select All ({{
                                            contacts.length
                                        }}
                                        contacts)
                                    </Label>
                                </div>

                                <div class="max-h-96 space-y-2 overflow-y-auto">
                                    <div
                                        v-for="contact in contacts"
                                        :key="contact.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox
                                            :checked="
                                                selectedContacts.includes(
                                                    contact.id,
                                                )
                                            "
                                            @update:model-value="
                                                () => toggleContact(contact.id)
                                            "
                                            :value="contact.id"
                                            name="recipients[]"
                                        />
                                        <div class="flex-1 text-sm">
                                            <p class="font-medium">
                                                {{ contact.name }}
                                            </p>
                                            <p class="text-muted-foreground">
                                                {{ contact.email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <p
                                    v-if="form.errors.recipients"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.recipients }}
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardContent class="pt-6">
                                <div class="space-y-2">
                                    <p class="text-sm font-medium">
                                        Selected Recipients
                                    </p>
                                    <p class="text-2xl font-bold">
                                        {{ selectedContacts.length }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-2">
                    <Link :href="show.url(campaign.id)">
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button
                        type="submit"
                        :disabled="
                            form.processing || selectedContacts.length === 0
                        "
                    >
                        <Save class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
