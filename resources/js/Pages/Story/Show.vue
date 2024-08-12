<template>
    <div class="bg-white">
        <div class="pb-16 pt-6 sm:pb-24">
            <div class="mx-auto mt-8 max-w-2xl px-4 sm:px-6 lg:max-w-7xl lg:px-8">
                <div class="lg:grid lg:auto-rows-min lg:grid-cols-12 lg:gap-x-8">

                    <div class="mt-8 lg:col-span-4 lg:col-start-1 lg:row-span-3 lg:row-start-1 lg:mt-0">
                        <img :src="story.images[0].image_path" class="lg:col-span-2 lg:row-span-2" v-if="story.images.length > 0 && story.status !== 'VIDEO_END'" />
                        <video controls :src="`/${story.video_path}`" class="lg:col-span-2 lg:row-span-2" v-else-if="story.status === 'VIDEO_END'"></video>
                    </div>

                    <div class="lg:col-span-5">
                        <h1 class="text-xl font-medium text-gray-900">
                            <template v-if="story.title">{{ story.title }}</template>
                            <template v-else>
                                (title pending)
                            </template>
                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                {{ getStatus(story.status) }}
                            </span>
                        </h1>
                        <div class="mt-10">
                            <h2 class="text-sm font-medium text-gray-900">Story</h2>

                            <div class="prose prose-sm mt-4 text-gray-500">
                                <template v-if="story.content">{{ story.content }}</template>
                                <template v-else>(story pending)</template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

import {ref} from "vue";

const props = defineProps({
    story: Object,
})

const story = ref(props.story);

const getStatus = (status) => {
    return status;
}

Echo
    .channel(`story.${props.story.id}`)
    .listen('StoryStatusUpdated', (e) => {
        fetch(`/api/story/${props.story.id}`)
            .then(response => response.json())
            .then(data => {
                story.value = data;
            });
    });

</script>
