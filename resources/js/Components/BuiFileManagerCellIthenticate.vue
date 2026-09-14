<template>
  <PkpTableCell>
    <span v-if="status">{{ status }} </span>
    <PkpSpinner v-else></PkpSpinner>
  </PkpTableCell>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({ file: { type: Object, required: true } });

const fileStore = pkp.registry.getPiniaStore("fileManager_SUBMISSION_FILES");
const status = computed(
  () => fileStore?.ithenticateStatus?.[props.file.id] || null,
);
</script>
