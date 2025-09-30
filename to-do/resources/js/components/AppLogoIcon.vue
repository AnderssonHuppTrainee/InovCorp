<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 73 73"
        fill="currentColor"
        :class="className"
        v-bind="$attrs"
    >
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect
                stroke="#9B1011"
                stroke-width="2"
                fill="#FFFFFF"
                fill-rule="nonzero"
                x="1"
                y="1"
                width="71"
                height="71"
                rx="14"
            />
            <g transform="translate(15, 13)" fill-rule="nonzero">
                <g transform="translate(0, 2)" stroke="#FF9882">
                    <circle fill="#FF9882" cx="22" cy="22" r="4" />
                    <circle stroke-width="6" cx="22" cy="22" r="12" />
                    <circle stroke-width="6" cx="22" cy="22" r="22" />
                </g>
                <g transform="translate(20, 0)">
                    <path
                        d="M26.9575195,25.4825897 C26.7476355,28.9460224 25.9440822,31.8525722 24.4520566,35.0184096 L20.9139662,34.1019573 L19.8646466,30.533563 L1.50160327,25.6008682 C0.557225645,25.3909449 0.0325156178,24.4463906 0.347341634,23.5018363 C0.557225645,22.557282 1.50160327,22.0324738 2.4459809,22.3473588 L20.7040822,27.2800535 L23.4322731,24.6563139 L26.9575195,25.4825897 Z"
                        fill="#334A5E"
                        opacity="0.2"
                    />
                    <path
                        d="M17.2160909,8.78381918 C16.5740989,8.14182772 15.3970284,8.14182772 14.754934,8.78381918 L0.737307905,22.8015374 C0.0953159696,23.4435289 0.0953159696,24.6205986 0.737307905,25.2626924 C1.37929984,25.9046839 2.5563704,25.9046839 3.19846474,25.2626924 L17.2161933,11.2449742 C17.8581852,10.6029827 17.8581852,9.42591305 17.2160909,8.78381918 Z"
                        fill="#40596B"
                    />
                    <polygon
                        fill="#42C3FF"
                        points="14.0695287 11.7809704 19.5878851 13.0488605 25.5863921 7.34524349 19.8279604 5.9577577 18.6999199 0 13 6.22435623"
                    />
                </g>
            </g>
        </g>
    </svg>
</template>
