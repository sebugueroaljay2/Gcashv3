<script setup>
import { defineProps, defineEmits, computed } from 'vue'

const props = defineProps({
    links: {
        type: Object,
        required: true,
    },
    currentPage: {
        type: Number,
        required: true,
    },
    isLoading: {
        type: Boolean,
        default: false,
    }
})

const emit = defineEmits(['change-page'])

const pages = computed(() => {
    const total = props.links.last_page
    const current = props.currentPage
    const delta = 2 // how many pages to show on each side

    const range = []
    for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
        range.push(i)
    }

    if (current - delta > 2) {
        range.unshift('...')
    }
    if (current + delta < total - 1) {
        range.push('...')
    }

    range.unshift(1)
    if (total > 1) range.push(total)

    return range
})

const goToPage = (page) => {
    if (!props.isLoading && page && page !== props.currentPage) {
        emit('change-page', Number(page))
    }
}
</script>

<template>
    <div class="flex gap-2 items-center my-4">
        <button @click="goToPage(props.links.prev_page_url?.split('page=')[1])"
            :disabled="!props.links.prev_page_url || isLoading"
            class="px-4 py-2 bg-blue-400 text-white rounded disabled:opacity-50">
            Previous
        </button>

        <button v-for="page in pages" :key="page + '-'" @click="page !== '...' && goToPage(page)"
            :disabled="page === '...' || isLoading" :class="[
                'px-3 py-1 rounded border transition',
                currentPage === page ? 'bg-indigo-500 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100',
                page === '...' ? 'cursor-default text-gray-400' : ''
            ]">
            {{ page }}
        </button>

        <button @click="goToPage(props.links.next_page_url?.split('page=')[1])"
            :disabled="!props.links.next_page_url || isLoading"
            class="px-4 py-2 bg-blue-400 text-white rounded disabled:opacity-50">
            Next
        </button>
    </div>
</template>