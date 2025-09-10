<script setup>
import { defineEmits, defineProps, onMounted } from 'vue';

defineProps({
  modelValue: Object,
})
defineEmits(['update:modelValue'])

</script>

<template>
        
    <div v-if="modelValue" class="fixed inset-0 bg-[#262626] flex items-center justify-center z-50" @click.stop>
        <button @click="$emit('update:modelValue', null)" class="exit">✕</button>
        <div class="flex flex-col" :class="modelValue.type === 'video' ? 'max-w-[900px]' : 'max-w-[400px]'">
            <video :src="modelValue.content_url" controls autoplay class="object-contain" :class="modelValue.type === 'video' ? 'aspect-16/9' : 'aspect-9/16'"></video>
            <h1>{{ modelValue.title }}</h1>
            <h2>{{ modelValue.description }}</h2>
        </div>
    </div>
</template>

<style scoped>
.exit{
    font-size: 32px;
    color: white;
    position: fixed;
    top: 50px;
    right: 50px;
    z-index: 51;
}
.exit:hover {
    opacity: 0.5;
}
h1 {
    padding: 40px 0px 20px;
    color: white;
    font-size: 3em;
    font-weight: 600;
    text-transform: uppercase;
    line-height: 1em;
}
h2 {
    color: white;
    white-space: pre-line;
    line-height: 1.2em;
}
</style>