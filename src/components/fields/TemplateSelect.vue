<template>
  <k-select-field
    v-if="templateIsSelect"
    :label="$t(template.label) || template.label"
    :options="selectOptions"
    :value="selectedTemplate"
    @input="$emit('update:templateSelect', $event)"
  />
</template>

<script>
export default {
  props: {
    template: [String, Object],
    selectedTemplate: String
  },
  computed: {
    templateIsSelect() {
      return this.template && typeof this.template === 'object' && this.template.type === 'select'
    },
    selectOptions() {
      if (!this.templateIsSelect) return []

      return Object.entries(this.template.options).map(([value, label]) => ({
        value,
        text: this.$t(label) != "" ? this.$t(label) : label
      }))
    }
  },  
}
</script>

<style>

</style>