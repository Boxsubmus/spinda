<script setup>
import { ref, reactive, watch, computed, onBeforeUnmount } from 'vue';
import {
	computePosition,
	offset,
	flip,
	shift,
	arrow,
	autoUpdate,
} from '@floating-ui/dom';

const props = defineProps({
	text: { type: String, required: true },
	placement: { type: String, default: 'top' }, // top, bottom, left, right
	display: { type: String, default: 'inline-block' }, // inline-block, block, inline-flex, flex, inline, contents...
	tag: { type: String, default: 'span' }, // change to 'div' if display is 'flex'/'block' etc.
	disabled: { type: Boolean, default: false },
});

const displayClass = computed(() => props.display);

const triggerRef = ref(null);
const tooltipRef = ref(null);
const arrowRef = ref(null);
const visible = ref(false);
const actuallyVisible = ref(false);

const floatingStyles = reactive({ top: '0px', left: '0px' });
const arrowStyles = reactive({});

let cleanupAutoUpdate = null;

const staticSide = {
	top: 'bottom',
	bottom: 'top',
	left: 'right',
	right: 'left',
};

async function updatePosition() {
	if (!triggerRef.value || !tooltipRef.value) return;

	const { x, y, placement, middlewareData } = await computePosition(
		triggerRef.value,
		tooltipRef.value,
		{
			placement: props.placement,
			middleware: [
				offset(8),
				flip(),
				shift({ padding: 8 }),
				arrow({ element: arrowRef.value }),
			],
		}
	);

	floatingStyles.left = `${x}px`;
	floatingStyles.top = `${y}px`;

	const side = placement.split('-')[0];
	const { x: arrowX, y: arrowY } = middlewareData.arrow;

	Object.assign(arrowStyles, {
		left: arrowX != null ? `${arrowX}px` : '',
		top: arrowY != null ? `${arrowY}px` : '',
		[staticSide[side]]: '-4px',
	});
}

function show() {
	if (props.disabled)
		return;
	visible.value = true;
}

function hide() {
	visible.value = false;
}

watch(visible, async (isVisible) => {
	if (isVisible) {
		await import('vue').then(({ nextTick }) => nextTick());
		cleanupAutoUpdate = autoUpdate(triggerRef.value, tooltipRef.value, updatePosition);
		actuallyVisible.value = true;
	} else if (cleanupAutoUpdate) {
		cleanupAutoUpdate();
		cleanupAutoUpdate = null;
		actuallyVisible.value = false;
	}
});

onBeforeUnmount(() => {
	if (cleanupAutoUpdate) cleanupAutoUpdate();
});
</script>

<template>
	<component
		:is="tag"
		ref="triggerRef"
		:class="displayClass"
		@mouseenter="show"
		@mouseleave="hide"
		@focusin="show"
		@focusout="hide"
	>
		<slot />
	</component>

	<Teleport to="body">
		<div
			v-if="actuallyVisible"
			ref="tooltipRef"
			role="tooltip"
			class="pointer-events-none absolute z-50 whitespace-nowrap rounded-md bg-zinc-900 px-2 py-1 text text-white transition-opacity duration-150 shadow-[0_0px_12px_rgba(0,0,0,0.35)]"
			:class="actuallyVisible ? 'opacity-100' : 'opacity-0'"
			:style="floatingStyles"
		>
			{{ text }}
			<div
				ref="arrowRef"
				class="absolute h-2 w-2 rotate-45 bg-zinc-900"
				:style="arrowStyles"
			></div>
		</div>
	</Teleport>
</template>