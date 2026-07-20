<template>
  <k-field class="k-annotator-file-field">
    <header class="k-section-header">
      <h2 class="k-label k-section-label">{{ label }}<span v-if="min >= 1" class="error-red">*</span></h2>
      <span :class="['text-sm ml-2', isInvalid ? 'error-red' : 'succes-green']">
        {{ selectedCount }}
        <span v-if="max"> / {{ isObjectSelected ? 1 : max }}</span>
      </span>

      <div class="k-button-group k-section-buttons">
        <k-button variant="filled" icon="checklist" :disabled="disabled" @click="openDialog" size="xs">
          {{ $t('libis.annotator.select')}}
        </k-button>
      </div>
    </header>
    <k-collection v-if="collectionItems.length" :disabled="disabled" :items="collectionItems">
      <template #options="{ index }">
        <k-button :title="$t('libis.annotator.delete')" icon="remove" :disabled="disabled" @click="remove(index)" />
      </template>
    </k-collection>
    
    <k-annotator-file-dialog v-if="openDialogValue" :disabled="disabled" :open="openDialogValue" :values="tempSelected" :query="query" :limit="limit"
      @close="handleClose" @confirm="handleConfirm" />
  </k-field>
</template>

<script>
import { parse } from 'yaml';
export default {
  name: "annotatorFile",
  props: {
    value: {
      type: [Array, Object],
      default: () => [],
    },
    query: {
      type: String,
      default: () => ""
    },
    label: String,
    min: Number,
    max: Number,
    limit: Number,
    disabled: {
      type: Boolean,
      default: false
    },
  },
  data() {
    return {
      openDialogValue: false,
      tempSelected: [],
      resolvedFiles: []
    };
  },  
  watch: {
    selectedProxy: {
      immediate: true,
      handler(val) {
        this.resolveFiles(val);
      }
    }
  },
  computed: {
    // make images that are selected more usefull to use in a collection
    collectionItems() {
      return this.resolvedFiles.map(file => ({
        image: file.panel?.image || file.url,
        text: file.filename,
        info: file.template,
        link: file.panel?.url
      }));
    },
    selectedProxy: {
      get() {
        return this.parseKirbyYamlToArray(this.value);
      },
      set(next) {
        const safe = JSON.parse(JSON.stringify(next ?? []));
        this.$emit('input', safe);
      },
    },
    // check if the selected amount of files is not less or more then asked
    isInvalid() {
      const count = this.selectedCount;
      const minOk = this.min ? count >= this.min : true;
      const maxOk = this.max ? count <= this.max : true;
      return !(minOk && maxOk);
    },
    isObjectSelected() {
      return !Array.isArray(this.selectedProxy);
    },
    // amount of selected
    selectedCount() {
      const items = this.selectedProxy;
      return Array.isArray(items) ? items.length : (items ? 1 : 0);
    },
  },
  methods: {
    // dialog for selecting files (open and handle close and confirm)
    openDialog() {
      if(this.disabled == true) return;
      this.tempSelected = Array.isArray(this.selectedProxy)
        ? [...this.selectedProxy]
        : (this.selectedProxy ? [this.selectedProxy] : []);

      this.openDialogValue = true;
    },
    handleConfirm(data) {
      if(this.disabled == true) return;
      this.openDialogValue = false;

      let next = Array.isArray(data) ? data : [];
      next = next.filter(id => id);
      if (this.max && next.length > this.max) {
        next = next.slice(-this.max)
      }

      this.selectedProxy = next;

      this.tempSelected = [];
    },
    handleClose() {
      if(this.disabled == true) return;
      this.tempSelected = [];
      this.openDialogValue = false;
    },
    remove(index) {
      if(this.disabled == true) return;
      const updated = Array.isArray(this.selectedProxy)
        ? [...this.selectedProxy]
        : (this.selectedProxy ? [this.selectedProxy] : []);

      updated.splice(index, 1);
      this.selectedProxy = updated;
    },
    // parse yaml to data to work with
    parseKirbyYamlToArray(yamlStr) {
      if (typeof yamlStr !== 'string') return yamlStr;

      return parse(yamlStr);
    },
    // get the image data of the selected images
    async resolveFiles(values) {
      let uuids = [];

      if (Array.isArray(values)) {
        uuids = values;
      } 
      else if (values) {
        uuids = [values];
      }

      if (!uuids.length) {
        this.resolvedFiles = [];
        return;
      }

      let formData = new FormData();      
      uuids.forEach(uuid => {
        formData.append('uuids[]', uuid);
      });
    
      let response = await fetch('/get/images/uuid', {
        method: 'POST',
        body: formData
      });
      let files = await response.json();

      this.resolvedFiles = files;
    },

  },
};
</script>
<style>
.error-red {
  color: #f70303;
}

.succes-green {
  color: #0cbe33;
}
</style>