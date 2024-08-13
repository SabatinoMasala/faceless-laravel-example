<template>
    <Head title="Story" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stories
            </h2>
        </template>
        <div>
            <div class="pb-16 pt-6 sm:pb-24">
                <div class="mx-auto mt-8 max-w-2xl px-4 sm:px-6 lg:max-w-7xl lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="text-center" v-if="stories.data.length === 0">
                                No stories yet
                            </div>
                            <table class="min-w-full divide-y divide-gray-300" v-else>
                                <thead>
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">ID</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Title</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Series</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                <tr v-for="story in stories.data" :key="story.id">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{ story.id }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ story.title ? story.title : '(Title pending)' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ story.series }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <StoryStatus :status="story.status" />
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                        <Link :href="route('stories.show', {story})" class="text-indigo-600 hover:text-indigo-900">View</Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="flex justify-center mt-3" v-if="stories.data.length > 0">
                        <Pagination
                            route="stories.index"
                            :current-page="page"
                            :data="stories" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import StoryStatus from "@/Components/StoryStatus.vue";

const props = defineProps({
    stories: Object,
    page: Number,
})

</script>
