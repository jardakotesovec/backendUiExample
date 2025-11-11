<template>
  <PkpSideModalBody>
    <template #title>
      {{ t("plugins.generic.backendUiExample.sideModalTitle") }}
    </template>
    <PkpSideModalLayoutBasic>
      <div>{{ t("plugins.generic.backendUiExample.sideModalContent") }}</div>
      <div>
        <PkpButton @click="submit">{{
          t("plugins.generic.backendUiExample.sideModalSubmit")
        }}</PkpButton>
      </div>
    </PkpSideModalLayoutBasic>
  </PkpSideModalBody>
</template>

<script setup>
import { inject } from "vue";

const { useLocalize } = pkp.modules.useLocalize;

const closeModal = inject("closeModal");

const { t } = useLocalize();

function submit() {
  // SUBMIT
  // ...
  // Close
  closeModal();
}
</script>
