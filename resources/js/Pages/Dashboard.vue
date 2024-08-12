<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Button from "@/Components/ui/button/Button.vue";
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/Components/ui/form'
import { Input } from '@/components/ui/input'
import {ref} from "vue";
import { router } from '@inertiajs/vue3'

const onSubmit = () => {
    router.post('/story', {
        subject: subject.value
    })
};

const subject = ref('');

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form class="w-2/3 space-y-6" @submit.stop.prevent="onSubmit">
                            <FormField name="subject">
                                <FormItem>
                                    <FormLabel>Subject</FormLabel>
                                    <FormControl>
                                        <Input type="text" placeholder="Eg. the roman empire" v-model="subject" />
                                    </FormControl>
                                    <FormDescription>
                                        This is the subject of your video
                                    </FormDescription>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                            <Button type="submit">
                                Submit
                            </Button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
