<template>
    <Pagination
        v-slot="{ page }"
        :itemsPerPage="data.per_page"
        :total="data.total"
        :sibling-count="1"
        show-edges
        :default-page="currentPage"
        @update:page="handleUpdate"
    >
        <PaginationList v-slot="{ items }" class="flex items-center gap-1">
            <PaginationFirst />
            <PaginationPrev />
            <template v-for="(item, index) in items">
                <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                    <Button class="w-10 h-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                        {{ item.value }}
                    </Button>
                </PaginationListItem>
                <PaginationEllipsis v-else :key="item.type" :index="index" />
            </template>

            <PaginationNext />
            <PaginationLast />
        </PaginationList>
    </Pagination>
</template>
<script setup>
import { router } from '@inertiajs/vue3'

import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev
} from "@/Components/ui/pagination/index.js";
import {Button} from "@/Components/ui/button/index.js";

const props = defineProps({
    data: Object,
    route: String,
    currentPage: Number
})

const handleUpdate = (page) => {
    router.visit(route(props.route, { page }))
}

</script>
