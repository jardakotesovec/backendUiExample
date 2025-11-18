<template>
  <div class="container">
    <h4>Custom Wizard Component</h4>
    <p>
      This is a custom component dynamically injected into the submission
      wizard.
    </p>
    <p class="submission-info">Submission ID: {{ submissionId }}</p>
    <div class="content-area">
      <p>You can add any custom interactive elements here.</p>
    </div>
  </div>
</template>

<script setup>
defineProps({ submissionId: { type: [String, Number], required: true } });
</script>

<style scoped>
.container {
  border: 1px solid var(--border-color-light);
  border-radius: var(--radius);
  padding: var(--spacing-4);
  margin: var(--spacing-4) 0;
  background-color: var(--background-color-secondary);
}

.container h4 {
  color: var(--text-color-heading);
  font: var(--font-lg-bold);
  margin: 0 0 var(--spacing-2) 0;
}

.container p {
  color: var(--text-color-default);
  font: var(--font-base-normal);
  margin: var(--spacing-2) 0;
}

.submission-info {
  color: var(--text-color-secondary);
  font: var(--font-sm-normal);
  background-color: var(--background-color-default);
  padding: var(--spacing-2);
  border-radius: var(--radius);
}

.content-area {
  margin-top: var(--spacing-3);
  padding-top: var(--spacing-3);
  border-top: 1px solid var(--border-color-light);
}
</style>
