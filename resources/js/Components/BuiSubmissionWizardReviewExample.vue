<template>
  <div class="review-container">
    <h3>{{ title }}</h3>
    <p>Custom review component for submission ID: {{ submissionId }}</p>
    <div class="review-content">
      <p>
        This is an example review component that can be dynamically rendered in
        the submission wizard review step.
      </p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  submissionId: { type: [String, Number], required: true },
  title: { type: String, default: "Review Component" },
});
</script>

<style scoped>
.review-container {
  border: 2px solid var(--color-primary);
  border-radius: var(--radius);
  padding: var(--spacing-4);
  margin: var(--spacing-4) 0;
  background-color: var(--background-color-secondary);
}

.review-container h3 {
  color: var(--text-color-heading);
  font: var(--font-xl-bold);
  margin-top: 0;
  margin-bottom: var(--spacing-2);
}

.review-container p {
  color: var(--text-color-default);
  font: var(--font-lg-normal);
  margin: var(--spacing-2) 0;
}

.review-content {
  margin-top: var(--spacing-3);
  padding-top: var(--spacing-3);
  border-top: 1px solid var(--border-color-light);
}
</style>
