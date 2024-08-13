<template>
    <Head title="Story" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Story
            </h2>
        </template>
        <div class="pb-16 pt-6 sm:pb-24">
            <div class="mx-auto mt-8 max-w-2xl px-4 sm:px-6 lg:max-w-7xl lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-center" v-if="story.status !== 'COMPLETED'">
                            <h1 class="text-xl font-medium text-gray-900 mb-3" v-if="story.title">
                                {{ story.title }}
                            </h1>
                            <StoryStatus :status="story.status" />
                            <div class="prose prose-sm mt-4 text-gray-500 mx-auto" v-if="story.content">
                                {{ story.content }}
                            </div>
                        </div>
                        <div class="lg:grid lg:auto-rows-min lg:grid-cols-12 lg:gap-x-8" v-else>

                            <div class="mt-8 lg:col-span-4 lg:col-start-1 lg:row-span-3 lg:row-start-1 lg:mt-0">
                                <img :src="story.images[0].image_path" class="lg:col-span-2 lg:row-span-2" v-if="story.images.length > 0 && story.status !== 'COMPLETED'" />
                                <video controls :src="`/${story.video_path}`" class="lg:col-span-2 lg:row-span-2" v-else-if="story.status === 'COMPLETED'"></video>
                            </div>

                            <div class="lg:col-span-5">
                                <StoryStatus :status="story.status" />
                                <h1 class="text-xl font-medium text-gray-900">
                                    {{ story.title }}
                                </h1>
                                <div class="mt-10">
                                    <h2 class="text-sm font-medium text-gray-900">Story</h2>
                                    <div class="prose prose-sm mt-4 text-gray-500">
                                        {{ story.content }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {ref} from "vue";
import {Head} from "@inertiajs/vue3";
import StoryStatus from "@/Components/StoryStatus.vue";

const props = defineProps({
    story: Object,
})

const story = ref(props.story);

Echo
    .channel(`story.${props.story.id}`)
    .listen('StoryStatusUpdated', (e) => {
        fetch(`/api/stories/${props.story.id}`)
            .then(response => response.json())
            .then(data => {
                story.value = data;
            });
    });

</script>
