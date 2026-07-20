<template>
  <k-table
    v-if="fields"
    :columns="tableColumns"
    :rows="markersWithOptions"
    @cell="editValues($event.row, $event.rowIndex)"
  />
</template>

<script>
import { parse } from 'yaml';
export default {
  props: {
    fields: {
      type: Array,
      default: []
    },
    markers: Array,
  },
  computed: {
    // add 3 extra columns on top of the asked fields
    tableColumns() {
      if (!this.fields) return [];

      const translatedFields = Object.fromEntries(
        Object.entries(this.fields).map(([key, field]) => [
          key,
          {
            ...field,
            label: this.$t(field.label) != "" ? this.$t(field.label) : field.label
          }
        ])
      );

      return {
        x: {
          label: "X",
          type: "text",
          disabled: true,
          width: "1/1"
        },
        y: {
          label: "Y",
          type: "text",
          disabled: true,
          width: "1/1"
        },
        zoom: {
          label: "Zoom",
          type: "text",
          width: "1/1"
        },
        ...translatedFields
      }
    },
    // add options to the rows to edit / delete the row
    markersWithOptions() {
      return this.markers.map((row, index) => {
        return {
          ...row,
          options: [
            {
              icon: "edit",
              text: this.$t('libis.annotator.edit'),
              click: () => this.editValues(row, index)
            },
            {
              icon: "trash",
              text: this.$t('libis.annotator.delete'),
              click: () => this.openDeleteRow(row, index)
            }
          ]
        };
      });
    }
  },  
  methods: {
    // function that will open an edit drawer
    editValues(item, id) {
      this.$panel.drawer.open({
        component: "k-form-drawer",
        props: {
          icon: "box",
          title: item.title ?? id,
          fields: this.tableColumns,
          value: item,
          model: this.model,
          options: [{ icon: "trash", text: "Delete", click: () => this.openDeleteRow(item, id) }],
        },
        on: {
          submit: data => this.updateValue(data, id),
        }
      });
    },
    // if value will be updated then handles the update
    updateValue(item, id) {
      this.markers[id] = item;

      this.$emit("update:markers", this.markers);
      this.$panel.drawer.close();
    },
    // if delete is select open delete dialog
    openDeleteRow(item, id) {
      this.$panel.dialog.open({
        component: 'k-remove-dialog',
        props: {
          text: "Ben je zeker dat je rij " + (id + 1) + " wilt verwijderen?",
        },
        on: {
          cancel: () => this.$panel.dialog.close(),
          close: () => this.$panel.dialog.close(),
          submit: data => this.deleteRow(id),
        }
      })
    },
    // handles a delete of a row
    deleteRow(id) {
      this.markers.splice(id, 1);
      this.$emit("update:markers", this.markers);
      this.$panel.dialog.close();
    }
  },
}
</script>