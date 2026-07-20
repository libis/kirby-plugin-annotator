<template>
  <k-field
    class="k-annotator-field"
    :disabled="disabled"
    :help="help"
    :label="false"
    :required="required"
  >
    <k-template-select
      :template="template"
      :selectedTemplate="currentTemplate"
      @update:templateSelect="onTemplateSelect"
    />
    <k-annotator-file 
      :value="imageId"
      :query="query"
      :label="$t('libis.annotator.image')"
      min="1"
      max="1"
      :limit="limit"
      @input="setImage"
    />
    <div class="intro-info">
      <h2 class="k-label">{{ $t('libis.annotator.intro') }}</h2>
      <div class="intro-info__intro-fields-wrapper intro-fields-wrapper">
        <component
          v-for="(field, name) in fields"
          :label="$t(field.label) || field.label"
          :key="name"
          :is="`k-${field.type}-field`"
          v-bind="field",
          v-model="introData[name]"
        />
      </div>
    </div>
    <div class="annotorfield-wrapper">
      <p class="k-label">{{label}}</p>
      <div v-if="!imageUrl">
        {{ $t('libis.annotator.empty.text.image') }}
      </div>
      <div v-if="imageUrl" class="annotator-wrapper">
        <k-image-annotator 
          :markers="markers"
          :imageUrl="imageUrl"
          @update:markers="onMarkersUpdate"
        />  
        <k-marker-table
          :markers="markers"
          :fields="fields"
          @update:markers="onMarkersUpdate"
        />  
      </div>
    </div>
  </k-field>
</template>

<script>
import { parse } from 'yaml';
export default {
  props: {
    label: String,
    value: {
      type: Array,
      default: []
    },
    fields: {
      type: Array,
      default: []
    },
    infoTemplate: String,
    template: [String, Object],
    query: {
      type: String,
      default: ""
    },
    limit: {
      type: Number,
      default: 5
    }
  },
  data() {
    return {
      markers: this.parseValue(this.value),
      imageId: this.value?.image ?? [],
      imageUrl: null,
      selectedTemplate: this.value?.template ?? null,
      introData: this.value?.introData ?? {}
    };
  },
  mounted() {  
    // if image value excist set this image ready
    if (this.value?.image) {
      this.setImage(this.value.image);
    }
  },
  computed: {
    templateIsSelect() {
      return this.template && typeof this.template === 'object' && this.template.type === 'select'
    },
    currentTemplate() {
      if (this.templateIsSelect) {
        return this.selectedTemplate
      }
      return this.template
    },
  },  
  watch: {
    // get all the values that are in the field before showing this field and put this values inside markers
    value(val) {
      this.markers = this.parseValue(val);
    },
    // watch changes inside the intro data and save the block if so
    introData: {
      handler() {
        this.save();
      },
      deep: true
    }
  },
  methods: {
    // parse the saved yaml text to data to work with
    parseValue(val) {  
      try {
        if (!val) return [];

        if (typeof val === "object" && val.markers) {
          return val.markers;
        }

        if (typeof val === "string") {
          const parsed = parse(val);
          return parsed?.markers ?? [];
        }

        return [];
      } catch (e) {
        console.warn("Invalid YAML", e);
        return [];
      }
    },
    onMarkersUpdate(newMarkers) {
      this.markers = newMarkers;

      setTimeout(() => {
        this.save();
      }, 0);
    },
    // get image object based on the saved uuid
    async setImage(value) {
      let formData = new FormData();      
      formData.append('uuids[]', value);
    
      let response = await fetch('/get/images/uuid', {
        method: 'POST',
        body: formData
      });
      let files = await response.json();
      this.imageUrl = files[0].url;
      this.imageId = value;

      this.save();
    },
    // save the data and let the page know that there was a change in the field
    save() {
      const normalized = this.markers.map(marker => {
        const newMarker = { ...marker };

        Object.values(this.fields).forEach(field => {
          if (!(field.name in newMarker)) {
            newMarker[field.name] = this.defaultOfValues(field.type);
          }
        });

        return newMarker;
      });
      this.markers = normalized;
      
      Object.entries(this.fields).forEach(([name, field]) => {
        if (!(name in this.introData)) {
          this.$set(this.introData, name, this.defaultOfValues(field.type));
        }
      });

      const savedObject = {
        template: this.currentTemplate,
        infoTemplate: this.infoTemplate,
        markers: this.markers,
        image: this.imageId,
        introData: this.introData
      }

      this.$emit("input", savedObject);
    },
    defaultOfValues(value) { 
      switch (value) {
        case "text":
          return "";
        case "textarea":
          return "";
        case "number":
          return 0;
        case "checkbox":
          return false;
        case "select":
          return null;
        case "object":
          return {};
        case "files":
          return [];
        default:
          return null;
      }
    },
    onTemplateSelect(value) {
      this.selectedTemplate = value;
      this.save();
    },
  },
}
</script>

<style>
  .annotator-wrapper {
    display: flex;
    flex-direction: column;
    gap: 30px;
  }

  .k-annotator-field {
    display: flex;
    flex-direction: column;
    gap: 30px;
  }

  .annotorfield-wrapper {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .intro-info {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .intro-fields-wrapper {
    background-color: var(--panel-color-back);
    outline: 1px solid var(--input-color-border);
    border-radius: 5px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 10px 15px;
  }
</style>