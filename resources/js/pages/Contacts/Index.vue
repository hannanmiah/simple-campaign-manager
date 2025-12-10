<script setup lang="ts">
import {
    create,
    destroy,
    edit,
    index,
    show,
} from '@/actions/App/Http/Controllers/ContactController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
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
import { Mail, MoreHorizontal, Search, UserPlus } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    contacts: Object;
    filters: Object;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contacts',
        href: index.url(),
    },
];

const search = ref(props.filters.search || '');
const selectedContacts = ref(new Set());

watch(search, (value) => {
    router.get(
        index.url({ query: { search: value } }),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
});

const toggleAll = () => {
    if (selectedContacts.value.size === props.contacts.data.length) {
        selectedContacts.value.clear();
    } else {
        props.contacts.data.forEach((contact) => {
            selectedContacts.value.add(contact.id);
        });
    }
};

const toggleContact = (contactId) => {
    if (selectedContacts.value.has(contactId)) {
        selectedContacts.value.delete(contactId);
    } else {
        selectedContacts.value.add(contactId);
    }
};
</script>

<template>
    <Head title="Contacts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">
                            Contacts
                        </h1>
                        <p class="text-muted-foreground">
                            Manage your email contacts and recipients
                        </p>
                    </div>
                    <Link :href="create.url()">
                        <Button>
                            <UserPlus class="mr-2 h-4 w-4" />
                            Add Contact
                        </Button>
                    </Link>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>All Contacts</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="mb-4 flex items-center space-x-2">
                            <div class="relative flex-1">
                                <Search
                                    class="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground"
                                />
                                <Input
                                    v-model="search"
                                    placeholder="Search contacts..."
                                    class="pl-8"
                                />
                            </div>
                        </div>

                        <div v-if="contacts.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead class="w-12">
                                            <Checkbox
                                                :checked="
                                                    selectedContacts.size ===
                                                    contacts.data.length
                                                "
                                                @update:checked="toggleAll"
                                            />
                                        </TableHead>
                                        <TableHead>Name</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Campaigns</TableHead>
                                        <TableHead class="text-right"
                                            >Actions</TableHead
                                        >
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="contact in contacts.data"
                                        :key="contact.id"
                                    >
                                        <TableCell>
                                            <Checkbox
                                                :checked="
                                                    selectedContacts.has(
                                                        contact.id,
                                                    )
                                                "
                                                @update:checked="
                                                    () =>
                                                        toggleContact(
                                                            contact.id,
                                                        )
                                                "
                                            />
                                        </TableCell>
                                        <TableCell class="font-medium">{{
                                            contact.name
                                        }}</TableCell>
                                        <TableCell>
                                            <div
                                                class="flex items-center space-x-2"
                                            >
                                                <Mail
                                                    class="h-4 w-4 text-muted-foreground"
                                                />
                                                <span>{{ contact.email }}</span>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">
                                                {{
                                                    contact.campaigns?.length ||
                                                    0
                                                }}
                                                campaigns
                                            </Badge>
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
                                                                    contact.id,
                                                                )
                                                            "
                                                        >
                                                            View details
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem as-child>
                                                        <Link
                                                            :href="
                                                                edit.url(
                                                                    contact.id,
                                                                )
                                                            "
                                                        >
                                                            Edit contact
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuSeparator />
                                                    <DropdownMenuItem
                                                        class="text-destructive"
                                                        @click="
                                                            router.delete(
                                                                destroy.url(
                                                                    contact.id,
                                                                ),
                                                            )
                                                        "
                                                    >
                                                        Delete contact
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <div class="mt-4">
                                <template
                                    v-for="link in contacts.links"
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
                                No contacts found.
                            </p>
                            <Link :href="create.url()" class="mt-4">
                                <Button>Create your first contact</Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
