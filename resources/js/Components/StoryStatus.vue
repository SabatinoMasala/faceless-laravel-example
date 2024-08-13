<template>
    <span :class="statusClasses" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset">
        {{ getStatus(status) }}
    </span>
</template>
<script setup>

import {computed} from "vue";

const props = defineProps({
    status: String,
});

const statusClasses = computed(() => {
    if (props.status.indexOf('ERROR') !== -1) {
        return 'bg-red-50 text-red-800 ring-red-600/20';
    }
    if (props.status === 'COMPLETED') {
        return 'bg-green-50 text-green-800 ring-green-600/20';
    }
    return 'bg-yellow-50 text-yellow-800 ring-yellow-600/20';
});

const getStatus = (status) => {
    const step = status.replace('_START', '').replace('_END', '');
    switch (step) {
        case 'PENDING':
            return 'Pending';
        case 'BRAINSTORM':
            return 'Brainstorming idea';
        case 'CREATIVE_DIRECTION':
            return 'Creative direction';
        case 'VOICEOVER':
            return 'Generating voiceover';
        case 'TRANSCRIBE':
            return 'Generating transcript';
        case 'IMAGES':
            return 'Generating images';
        case 'VIDEO':
            return 'Rendering video';
        case 'STORY':
            return 'Generating a story';
    }
    return step.charAt(0).toUpperCase() + step.slice(1).toLowerCase();
}
</script>
