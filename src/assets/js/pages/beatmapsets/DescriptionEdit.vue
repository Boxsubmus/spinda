<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import MarkdownEditor from '../components/MarkdownEditor.vue';
import ActionButton from '../components/ActionButton.vue';
import Markdown from '../components/Markdown.vue';

const props = defineProps({
    beatmapset: Object,
    auth: Object
});

const descriptionDraft = ref(props.beatmapset.description || '');
const isOwnProfile = computed(() => (props.auth?.user?.id === props.beatmapset.author.id) || props.auth?.user?.isAdmin);

const isEditing = ref(false);
const saving = ref(false);

function startEditing() {
    descriptionDraft.value = props.beatmapset.description || '';
    isEditing.value = true;
}

function cancelEditing() {
    isEditing.value = false;
}

async function saveDescription() {
    saving.value = true;

    try {
        
        // Only send a post request when this changes lol
        if (props.beatmapset.description != descriptionDraft.value)
        {
            const response = await axios.post(`/api/maps/${props.beatmapset.id}/description`, { content: descriptionDraft.value });
            // Update about me so it's immediately reflected
            props.beatmapset.description = response.data.description;
            isEditing.value = false;
        }
        else
        {
            isEditing.value = false;
        }
    } catch (e) {
        console.error('Post failed', e);
    } finally {
        saving.value = false;
    }
}

</script>

<template>

    <button
        v-if="isOwnProfile && !isEditing"
        @click="startEditing"
        class="text-sm text-gray-400 hover:text-white cursor-pointer"
    >
        <i class="fas fa-pen"></i> edit
    </button>

    <div v-if="isEditing">
        <MarkdownEditor v-model="descriptionDraft" placeholder="(Markdown supported)" />
        <div class="flex gap-2 mt-3">

            <form @submit.prevent="saveDescription">
                <ActionButton
                        :label="saving ? 'Saving...' : 'Save'"
                        icon="fas fa-save"
                        type="submit"
                        :disabled="saving"
                    />
            </form>
            <form @submit.prevent="cancelEditing">
                <ActionButton
                        label="Cancel"
                        icon="fas fa-times"
                        type="submit"
                        :disabled="saving"
                        class="bg-gray-500 hover:bg-gray-400"
                />
            </form>
        </div>
    </div>
    <div v-else>
        <Markdown :source="beatmapset.description" />
    </div>

</template>