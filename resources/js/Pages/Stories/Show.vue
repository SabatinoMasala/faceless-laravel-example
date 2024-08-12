<template>
    <div class="max-w-2xl mx-auto p-3">
        <div>{{ status }}</div>
    </div>
</template>
<script setup>
import {ref} from 'vue';
const props = defineProps({
    story: Object
})

const status = ref(props.story.status);

Echo
    .channel(`story.${props.story.id}`)
    .listen('StoryStatusUpdated', (e) => {
        status.value = e.status;
    });

</script>
