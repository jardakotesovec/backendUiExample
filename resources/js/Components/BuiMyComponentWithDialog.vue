<template>
  <div>
    <div class="buttons">
      <PkpButton @click="openExampleDialog">{{
        t("plugins.generic.backendUiExample.openDialog")
      }}</PkpButton>
      <PkpButton @click="openExampleSideModal">{{
        t("plugins.generic.backendUiExample.openSideModal")
      }}</PkpButton>
    </div>
  </div>
</template>

<script setup>
import BuiExampleSideModal from "./BuiExampleSideModal.vue";

const { useModal } = pkp.modules.useModal;
const { useLocalize } = pkp.modules.useLocalize;

const { t } = useLocalize();
const { openDialog, openSideModal } = useModal();

function openExampleDialog() {
  openDialog({
    title: t("plugins.generic.backendUiExample.exampleDialog.title"),
    message: t("plugins.generic.backendUiExample.exampleDialog.message"),
    actions: [
      {
        label: t("plugins.generic.backendUiExample.exampleDialog.yes"),
        isPrimary: true,
        callback: (close) => {
          close();
          // user has confirmed
        },
      },
      {
        label: t("plugins.generic.backendUiExample.exampleDialog.no"),
        isWarnable: true,
        callback: (close) => {
          close();
          // user has cancelled. close the modal
        },
      },
    ],
  });
}

function openExampleSideModal() {
  openSideModal(BuiExampleSideModal);
}
</script>

<style scoped>
.buttons {
  display: flex;
  column-gap: var(--spacing-2);
}
</style>
