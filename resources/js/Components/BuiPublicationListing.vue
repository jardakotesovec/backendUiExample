<template>
  <PkpTable>
    <template #label>
      <h3 class="">
        {{ t("plugins.generic.backendUiExample.publicationsListing") }}
      </h3>
    </template>
    <PkpTableHeader>
      <PkpTableColumn>{{
        t("plugins.generic.backendUiExample.publicationsListing.id")
      }}</PkpTableColumn>
      <PkpTableColumn>{{
        t("plugins.generic.backendUiExample.publicationsListing.title")
      }}</PkpTableColumn>
    </PkpTableHeader>
    <PkpTableBody>
      <PkpTableRow
        v-for="publication in submission.publications"
        :key="publication.id"
      >
        <PkpTableCell>
          {{ publication.id }}
        </PkpTableCell>
        <PkpTableCell>
          {{ localizeSubmission(publication.fullTitle, submission.locale) }}
        </PkpTableCell>
      </PkpTableRow>
    </PkpTableBody>
  </PkpTable>
</template>

<script setup>
const { useLocalize } = pkp.modules.useLocalize;

const { t, localizeSubmission } = useLocalize();
defineProps({ submission: { type: Object, required: true } });
</script>
