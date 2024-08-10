<template>
    <div class="max-w-2xl mx-auto p-3">
        <div class="flex mb-3" v-for="image in story.images">
            <img width="256" :src="getImage(image)" />
            <div class="p-3">
                {{ image.paragraph }}
            </div>
        </div>
        <pre>{{ story }}</pre>
    </div>
</template>
<script setup>
const props = defineProps({
    story: Object
})

const getImage = (image) => {
    return image.image_path;
}

</script>
